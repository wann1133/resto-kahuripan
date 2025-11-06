<?php

namespace App\Http\Controllers;

use App\Models\Menu;

// Expose menu list for public consumption
class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('options')
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json($menus);
    }
}

