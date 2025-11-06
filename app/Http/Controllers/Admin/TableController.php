<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// Manage restaurant tables and their QR codes
class TableController extends Controller
{
    public function index()
    {
        $tables = Table::orderBy('number')->get();

        return view('admin.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number' => ['required', 'string', 'max:50', 'unique:tables,number'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['code'] = Str::upper(Str::random(6));
        $data['is_active'] = $data['is_active'] ?? true;

        Table::create($data);

        return redirect()->back()->with('success', 'Meja berhasil dibuat.');
    }

    public function update(Request $request, Table $table)
    {
        $data = $request->validate([
            'number' => ['required', 'string', 'max:50', 'unique:tables,number,'.$table->id],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $table->update($data);

        return redirect()->back()->with('success', 'Meja diperbarui.');
    }

    public function regenerate(Table $table)
    {
        $table->update(['code' => Str::upper(Str::random(6))]);

        return redirect()->back()->with('success', 'QR code meja diperbarui.');
    }

    public function destroy(Table $table)
    {
        $table->delete();

        return redirect()->back()->with('success', 'Meja dihapus.');
    }
}

