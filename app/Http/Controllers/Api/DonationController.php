<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonationResource;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DonationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        abort_unless(
            $request->user()->hasRole('admin', 'treasurer'),
            403
        );

        $donations = Donation::with('payment.memberProfile')
            ->latest('id')
            ->paginate(10);

        return DonationResource::collection($donations);
    }
}
