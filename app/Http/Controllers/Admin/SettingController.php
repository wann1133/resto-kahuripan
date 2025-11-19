<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateQrisSettingRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function editPayment()
    {
        return view('admin.settings.payment', [
            'qrisPayload' => Setting::value('payments.qris_payload'),
            'qrisImage' => Setting::value('payments.qris_image_path'),
        ]);
    }

    public function updatePayment(UpdateQrisSettingRequest $request)
    {
        $payload = strtoupper(trim(preg_replace('/\s+/', '', $request->input('qris_payload'))));
        Setting::put('payments.qris_payload', $payload);

        if ($request->boolean('remove_qris_image')) {
            $this->deleteExistingImage();
            Setting::put('payments.qris_image_path', null);
        }

        if ($request->hasFile('qris_image')) {
            $this->deleteExistingImage();
            $path = $request->file('qris_image')->store('qris', 'public');
            Setting::put('payments.qris_image_path', $path);
        }

        return redirect()
            ->back()
            ->with('success', 'Pengaturan QRIS berhasil diperbarui.');
    }

    private function deleteExistingImage(): void
    {
        $current = Setting::value('payments.qris_image_path');
        if ($current && Storage::disk('public')->exists($current)) {
            Storage::disk('public')->delete($current);
        }
    }
}
