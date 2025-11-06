<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

// CRUD for menu items and options
class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('options')->orderBy('category')->orderBy('name')->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $data = $this->validateMenu($request);
        $data['is_active'] = $request->has('is_active');

        $menu = Menu::create($data);
        $this->syncOptions($menu, $request->input('options', []));

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $this->validateMenu($request, $menu->id);
        $data['is_active'] = $request->has('is_active');

        $menu->update($data);
        $this->syncOptions($menu, $request->input('options', []));

        return redirect()->back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->back()->with('success', 'Menu dihapus.');
    }

    private function validateMenu(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function syncOptions(Menu $menu, array $options): void
    {
        if (empty($options)) {
            return;
        }

        $menu->options()->delete();

        foreach ($options as $option) {
            if (! isset($option['name']) || $option['name'] === '') {
                continue;
            }

            $menu->options()->create([
                'name' => $option['name'],
                'type' => $option['type'] ?? 'addon',
                'extra_price' => $option['extra_price'] ?? 0,
            ]);
        }
    }
}
