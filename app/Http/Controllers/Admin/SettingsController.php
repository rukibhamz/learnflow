<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Update system settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $settings = $request->except(['_token', 'test_email', 'currency_display', 'site_logo', 'remove_logo']);
        
        foreach ($settings as $key => $value) {
            if ($value !== null && $value !== '') {
                Setting::set($key, $value);
            }
        }

        // Keep currency and payment_currency in sync (currency from General tab takes precedence)
        $currency = $request->input('currency') ?? $request->input('payment_currency');
        if ($currency !== null) {
            Setting::set('currency', $currency);
            Setting::set('payment_currency', $currency);
        }

        // Handle logo removal
        if ($request->boolean('remove_logo')) {
            $oldPath = Setting::get('site_logo');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            Setting::set('site_logo', '');
        }

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $request->validate(['site_logo' => ['image', 'max:2048', 'mimes:png,jpg,jpeg,svg,webp']]);

            // Delete old logo
            $oldPath = Setting::get('site_logo');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('site_logo')->store('settings', 'public');
            Setting::set('site_logo', $path);
        }

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }

    /**
     * Send a test email to verify mailer configuration.
     */
    public function sendTestEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        $to = $request->test_email;

        try {
            Mail::raw(
                'This is a test email from LearnFlow. If you received this, your mailer configuration is working correctly.',
                function ($message) use ($to) {
                    $message->to($to)
                        ->subject('LearnFlow – Test Email');
                }
            );
            return back()->with('success', 'Test email sent successfully to ' . $to);
        } catch (\Throwable $e) {
            Log::error('Mailer test failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
