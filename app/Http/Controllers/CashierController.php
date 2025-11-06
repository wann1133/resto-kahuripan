<?php

namespace App\Http\Controllers;

use App\Models\Payment;

// Cashier workspace for outstanding payments
class CashierController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['order.tableSession.table'])
            ->where('status', Payment::STATUS_PENDING)
            ->orderBy('created_at')
            ->get();

        return view('cashier.index', compact('payments'));
    }
}

