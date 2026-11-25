<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberProfileRequest;
use App\Http\Requests\UpdateMemberProfileRequest;
use App\Http\Resources\MemberProfileResource;
use App\Http\Resources\PaymentResource;
use App\Models\MemberProfile;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class MemberController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', MemberProfile::class);

        return MemberProfileResource::collection(
            MemberProfile::query()->latest()->paginate(15)
        );
    }

    public function store(StoreMemberProfileRequest $request)
    {
        $member = MemberProfile::create($request->validated());

        return (new MemberProfileResource($member))
            ->response()
            ->setStatusCode(201);
    }

    public function show(MemberProfile $member)
    {
        Gate::authorize('view', $member);

        return new MemberProfileResource($member);
    }

    public function update(
        UpdateMemberProfileRequest $request,
        MemberProfile $member
    ) {
        $member->update($request->validated());

        return new MemberProfileResource($member->refresh());
    }

    public function destroy(MemberProfile $member)
    {
        Gate::authorize('delete', $member);

        $member->delete();

        return response()->noContent();
    }

    public function payments(MemberProfile $member): AnonymousResourceCollection
    {
        Gate::authorize('view', $member);

        $payments = $member->payments()
            ->with(['memberProfile', 'recordedBy', 'monthlyDues', 'donation'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(10);

        return PaymentResource::collection($payments);
    }
}
