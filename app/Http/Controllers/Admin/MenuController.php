<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// CRUD for menu items and options
class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('options')->orderBy('category')->orderBy('name')->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function edit(Menu $menu)
    {
        $menu->load('options');

        return view('admin.menus.edit', compact('menu'));
    }

    public function store(Request $request)
    {
        $data = $this->validateMenu($request);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create($data);
        $this->syncOptions($menu, $request->input('options', []));

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $this->validateMenu($request, $menu->id);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($menu->image_url && ! preg_match('/^https?:\/\//', $menu->image_url)) {
                Storage::disk('public')->delete($menu->image_url);
            }

            $data['image_url'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($data);
        $this->syncOptions($menu, $request->input('options', []));

        return redirect()->route('menus.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->image_url && ! preg_match('/^https?:\/\//', $menu->image_url)) {
            Storage::disk('public')->delete($menu->image_url);
        }

        $menu->delete();

        return redirect()->route('menus.index')->with('success', 'Menu dihapus.');
    }

    private function validateMenu(Request $request, ?int $id = null): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:3072'],
            'options' => ['nullable', 'array'],
            'options.*.name' => ['nullable', 'string', 'max:120'],
            'options.*.extra_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        return $request->only(['name', 'category', 'description', 'price', 'stock']);
    }

    private function syncOptions(Menu $menu, array $options): void
    {
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
