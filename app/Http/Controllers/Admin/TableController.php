<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use BaconQrCode\Exception\RuntimeException as BaconQrCodeRuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// Manage restaurant tables and their QR codes
class TableController extends Controller
{
    public function index()
    {
        $tables = Table::orderBy('number')->get();

        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function edit(Table $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number' => ['required', 'string', 'max:50', 'unique:tables,number'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['code'] = Str::upper(Str::random(6));
        $data['is_active'] = $request->boolean('is_active', true);

        Table::create($data);

        return redirect()->route('admin.tables.index')->with('success', 'Meja berhasil dibuat.');
    }

    public function update(Request $request, Table $table)
    {
        $data = $request->validate([
            'number' => ['required', 'string', 'max:50', 'unique:tables,number,'.$table->id],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $table->update($data);

        return redirect()->route('admin.tables.index')->with('success', 'Meja diperbarui.');
    }

    public function regenerate(Table $table)
    {
        $table->update(['code' => Str::upper(Str::random(6))]);

        return redirect()->route('admin.tables.index')->with('success', 'QR code meja diperbarui.');
    }

    public function download(Table $table)
    {
        $targetUrl = url('/t/'.$table->code);
        $filenameBase = sprintf('qr-meja-%s', Str::slug($table->number) ?: $table->id);

        try {
            $qrImage = QrCode::format('png')
                ->margin(2)
                ->size(512)
                ->generate($targetUrl);

            return response($qrImage)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="'.$filenameBase.'.png"');
        } catch (BaconQrCodeRuntimeException $exception) {
            // Fallback ke SVG jika ekstensi Imagick belum tersedia.
            $svgImage = (string) QrCode::format('svg')
                ->margin(2)
                ->size(512)
                ->generate($targetUrl);

            return response($svgImage)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'attachment; filename="'.$filenameBase.'.svg"');
        }
    }

    public function destroy(Table $table)
    {
        $table->delete();

        return redirect()->route('admin.tables.index')->with('success', 'Meja dihapus.');
    }
}
