<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    private const RELATIONS = ['memberProfile', 'recordedBy', 'monthlyDues', 'donation'];

    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Payment::class);

        $filters = $request->validate([
            'member_id' => ['sometimes', 'integer'],
            'payment_method' => ['sometimes', Rule::in(['cash', 'bank_transfer'])],
            'from' => ['sometimes', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'date_format:Y-m-d'],
            'sort' => ['sometimes', Rule::in(['payment_date', '-payment_date', 'amount', '-amount'])],
        ]);

        $query = Payment::with(self::RELATIONS)
            ->when($filters['member_id'] ?? null, fn ($q, $id) => $q->where('member_profile_id', $id))
            ->when($filters['payment_method'] ?? null, fn ($q, $method) => $q->where('payment_method', $method))
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->whereDate('payment_date', '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->whereDate('payment_date', '<=', $to));

        $sort = $filters['sort'] ?? '-payment_date';
        $query->orderBy(ltrim($sort, '-'), str_starts_with($sort, '-') ? 'desc' : 'asc')
            ->orderByDesc('id');

        return PaymentResource::collection($query->paginate(10)->withQueryString());
    }

    public function store(StorePaymentRequest $request, PaymentService $service): PaymentResource
    {
        $payment = $service->record($request->validated(), $request->user());

        return new PaymentResource($payment->load(self::RELATIONS));
    }

    public function show(Payment $payment): PaymentResource
    {
        Gate::authorize('view', $payment);

        return new PaymentResource($payment->load(self::RELATIONS));
    }

    public function mine(Request $request): AnonymousResourceCollection
    {
        $profileId = $request->user()->memberProfile()->value('id');

        // A user with no member profile has no payments (id 0 matches nothing).
        $payments = Payment::with(self::RELATIONS)
            ->where('member_profile_id', $profileId ?? 0)
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(10);

        return PaymentResource::collection($payments);
    }
}
