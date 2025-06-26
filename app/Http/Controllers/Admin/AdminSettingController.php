<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_email' => 'required|email|max:255',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string|max:500',
            'currency' => 'required|string|max:10',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'shipping_cost' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'required|numeric|min:0',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'site_favicon' => 'nullable|image|mimes:ico,png|max:1024',
        ]);

        $settings = [
            'site_name' => $request->site_name,
            'site_description' => $request->site_description,
            'site_email' => $request->site_email,
            'site_phone' => $request->site_phone,
            'site_address' => $request->site_address,
            'currency' => $request->currency,
            'tax_rate' => $request->tax_rate,
            'shipping_cost' => $request->shipping_cost,
            'free_shipping_threshold' => $request->free_shipping_threshold,
        ];

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            // Delete old logo
            $oldLogo = Setting::where('key', 'site_logo')->first();
            if ($oldLogo && Storage::exists($oldLogo->value)) {
                Storage::delete($oldLogo->value);
            }

            $logoPath = $request->file('site_logo')->store('settings', 'public');
            $settings['site_logo'] = $logoPath;
        }

        // Handle favicon upload
        if ($request->hasFile('site_favicon')) {
            // Delete old favicon
            $oldFavicon = Setting::where('key', 'site_favicon')->first();
            if ($oldFavicon && Storage::exists($oldFavicon->value)) {
                Storage::delete($oldFavicon->value);
            }

            $faviconPath = $request->file('site_favicon')->store('settings', 'public');
            $settings['site_favicon'] = $faviconPath;
        }

        // Update or create settings
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }
}
