<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMonthlyDuesRequest;
use App\Http\Requests\UpdateMonthlyDuesRequest;
use App\Http\Resources\MonthlyDuesResource;
use App\Models\MonthlyDues;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MonthlyDuesController extends Controller
{
    private const ALLOCATED = 'payment_monthly_dues.allocated_amount';

    private const ALLOCATED_SQL = '(select coalesce(sum(pmd.allocated_amount), 0) from payment_monthly_dues pmd where pmd.monthly_dues_id = monthly_dues.id)';

    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', MonthlyDues::class);

        $filters = $request->validate([
            'month' => ['sometimes', 'integer', 'between:1,12'],
            'year' => ['sometimes', 'integer'],
            'member_id' => ['sometimes', 'integer'],
            'status' => ['sometimes', Rule::in(['paid', 'unpaid'])],
        ]);

        $query = MonthlyDues::with('memberProfile')
            ->withSum('payments as allocated_total', self::ALLOCATED)
            ->when(array_key_exists('month', $filters), fn ($q) => $q->where('month', $filters['month']))
            ->when(array_key_exists('year', $filters), fn ($q) => $q->where('year', $filters['year']))
            ->when(array_key_exists('member_id', $filters), fn ($q) => $q->where('member_profile_id', $filters['member_id']))
            ->when($filters['status'] ?? null, function ($q, $status) {
                $operator = $status === 'paid' ? '>=' : '<';
                $q->whereRaw(self::ALLOCATED_SQL." {$operator} monthly_dues.required_amount");
            })
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('id');

        return MonthlyDuesResource::collection($query->paginate(10)->withQueryString());
    }

    public function store(StoreMonthlyDuesRequest $request): MonthlyDuesResource
    {
        $due = MonthlyDues::create($request->validated())->refresh();

        return new MonthlyDuesResource(
            $due->load('memberProfile')->loadSum('payments as allocated_total', self::ALLOCATED)
        );
    }

    public function show(MonthlyDues $monthlyDue): MonthlyDuesResource
    {
        Gate::authorize('view', $monthlyDue);

        return new MonthlyDuesResource(
            $monthlyDue->load(['memberProfile', 'payments'])
                ->loadSum('payments as allocated_total', self::ALLOCATED)
        );
    }

    public function update(UpdateMonthlyDuesRequest $request, MonthlyDues $monthlyDue): MonthlyDuesResource
    {
        $monthlyDue->update($request->validated());

        return new MonthlyDuesResource(
            $monthlyDue->load('memberProfile')->loadSum('payments as allocated_total', self::ALLOCATED)
        );
    }

    public function destroy(MonthlyDues $monthlyDue): Response
    {
        Gate::authorize('delete', $monthlyDue);

        if ($monthlyDue->payments()->exists()) {
            throw ValidationException::withMessages([
                'monthly_due' => ['A month that has payments cannot be deleted.'],
            ]);
        }

        $monthlyDue->delete();

        return response()->noContent();
    }

    public function mine(Request $request): AnonymousResourceCollection
    {
        $profileId = $request->user()->memberProfile()->value('id');

        $dues = MonthlyDues::with('memberProfile')
            ->withSum('payments as allocated_total', self::ALLOCATED)
            ->where('member_profile_id', $profileId ?? 0)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(10);

        return MonthlyDuesResource::collection($dues);
    }
}
