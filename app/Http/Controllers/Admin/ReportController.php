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
        $data = $this->buildReportData($request);

        return view('admin.reports.index', $data);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->buildReportData($request);
        $filename = sprintf('laporan-penjualan-%s.xls', now()->format('Ymd-His'));
        $content = view('admin.reports.exports.excel', $data)->render();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function exportPdf(Request $request)
    {
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(500, 'Ekspor PDF belum dikonfigurasi. Silakan instal barryvdh/laravel-dompdf.');
        }

        $data = $this->buildReportData($request);
        $filename = sprintf('laporan-penjualan-%s.pdf', now()->format('Ymd-His'));

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.exports.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }

    private function buildReportData(Request $request): array
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

        return [
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'start' => $start,
            'end' => $end,
            'filters' => [
                'start_date' => $request->input('start_date', $start->format('Y-m-d')),
                'end_date' => $request->input('end_date', $end->format('Y-m-d')),
            ],
        ];
    }
}
