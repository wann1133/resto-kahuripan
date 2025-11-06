<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Table;
use App\Models\TableSession;

// Admin overview metrics and charts
class DashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'active_tables' => TableSession::where('status', TableSession::STATUS_ACTIVE)->count(),
            'open_orders' => Order::whereIn('status', [
                Order::STATUS_PLACED,
                Order::STATUS_IN_PROGRESS,
                Order::STATUS_READY,
                Order::STATUS_SERVED,
            ])->count(),
            'today_revenue' => Payment::whereDate('paid_at', today())
                ->where('status', Payment::STATUS_SUCCESS)
                ->sum('amount'),
            'menu_count' => Menu::count(),
            'table_count' => Table::count(),
        ];

        $recentOrders = Order::with(['tableSession.table', 'payments'])
            ->latest()
            ->limit(10)
            ->get();

        $topMenus = Menu::withSum('orderItems as total_sold', 'qty')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('metrics', 'recentOrders', 'topMenus'));
    }
}

