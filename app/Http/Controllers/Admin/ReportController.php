<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

// Sales and transaction reporting
class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $end = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfDay();

        $orders = Order::with(['tableSession.table', 'payments'])
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get();

        $totalRevenue = Payment::whereBetween('paid_at', [$start, $end])
            ->where('status', Payment::STATUS_SUCCESS)
            ->sum('amount');

        return view('admin.reports.index', [
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'start' => $start,
            'end' => $end,
        ]);
    }
}
