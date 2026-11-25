<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function monthly(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('treasurer'), 403);

        $filters = $request->validate([
            'year' => ['sometimes', 'integer', 'min:2000', 'max:2100'],
        ]);

        $year = (int) ($filters['year'] ?? now()->year);
        $months = [];

        for ($month = 1; $month <= 12; $month++) {
            $start = sprintf('%04d-%02d-01', $year, $month);
            $end = date('Y-m-t', strtotime($start));

            $paymentsTotal = Payment::query()
                ->whereBetween('payment_date', [$start, $end])
                ->sum('amount');

            $duesAllocated = DB::table('payment_monthly_dues')
                ->join('payments', 'payments.id', '=', 'payment_monthly_dues.payment_id')
                ->whereBetween('payments.payment_date', [$start, $end])
                ->sum('payment_monthly_dues.allocated_amount');

            $donationsTotal = Donation::query()
                ->whereHas('payment', fn ($query) => $query->whereBetween('payment_date', [$start, $end]))
                ->sum('amount');

            $months[] = [
                'month' => $month,
                'payments_total' => (float) $paymentsTotal,
                'dues_allocated' => (float) $duesAllocated,
                'donations_total' => (float) $donationsTotal,
            ];
        }

        return response()->json([
            'year' => $year,
            'data' => $months,
        ]);
    }

    public function annual(Request $request): JsonResponse
    {
        abort_unless($request->user()->hasRole('treasurer'), 403);

        $yearExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%Y', payment_date) AS INTEGER)",
            'mysql', 'mariadb', 'sqlsrv' => 'YEAR(payment_date)',
            'pgsql' => 'EXTRACT(YEAR FROM payment_date)',
            default => throw new \RuntimeException('Unsupported database driver for annual reports.'),
        };

        $years = Payment::query()
            ->selectRaw("DISTINCT {$yearExpression} AS year")
            ->orderBy('year')
            ->pluck('year');

        $reports = [];

        foreach ($years as $year) {
            $start = sprintf('%04d-01-01', $year);
            $end = sprintf('%04d-12-31', $year);

            $paymentsTotal = Payment::query()
                ->whereBetween('payment_date', [$start, $end])
                ->sum('amount');

            $duesAllocated = DB::table('payment_monthly_dues')
                ->join('payments', 'payments.id', '=', 'payment_monthly_dues.payment_id')
                ->whereBetween('payments.payment_date', [$start, $end])
                ->sum('payment_monthly_dues.allocated_amount');

            $donationsTotal = Donation::query()
                ->whereHas('payment', fn ($query) => $query->whereBetween('payment_date', [$start, $end]))
                ->sum('amount');

            $reports[] = [
                'year' => (int) $year,
                'payments_total' => (float) $paymentsTotal,
                'dues_allocated' => (float) $duesAllocated,
                'donations_total' => (float) $donationsTotal,
            ];
        }

        return response()->json(['data' => $reports]);
    }
}
