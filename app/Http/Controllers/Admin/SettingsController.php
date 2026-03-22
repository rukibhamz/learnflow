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
                // Sanitize mail host to remove protocol/port if present
                if ($key === 'mail_host') {
                    $value = str_replace(['ssl://', 'tls://'], '', $value);
                    $value = preg_replace('/:(\d+)$/', '', $value); // Remove trailing :port
                }
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

        // Handle favicon removal
        if ($request->boolean('remove_favicon')) {
            $oldPath = Setting::get('site_favicon');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            Setting::set('site_favicon', '');
        }

        // Handle favicon upload
        if ($request->hasFile('site_favicon')) {
            $request->validate(['site_favicon' => ['image', 'max:1024', 'mimes:png,ico,svg,webp']]);

            // Delete old favicon
            $oldPath = Setting::get('site_favicon');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('site_favicon')->store('settings', 'public');
            Setting::set('site_favicon', $path);
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
        $config = config('mail.mailers.smtp');
        $host = $config['host'] ?? 'null';
        $port = $config['port'] ?? 'null';
        $scheme = $config['scheme'] ?? 'null';

        // 1. Definitively check connectivity first via raw sockets
        $connectionOk = false;
        $connectionError = '';
        $fp = @fsockopen($host, (int)$port, $errno, $errstr, 5); // 5 second timeout
        if ($fp) {
            $connectionOk = true;
            fclose($fp);
        } else {
            $connectionError = "Network/Firewall Issue: Could not open connection to $host on Port $port ($errstr). This usually means your server's hosting provider is blocking outgoing email traffic on this port.";
        }

        try {
            if (!$connectionOk) {
                throw new \Exception($connectionError);
            }

            Mail::raw(
                'This is a test email from LearnFlow. If you received this, your mailer configuration is working correctly.',
                function ($message) use ($to) {
                    $message->to($to)
                        ->subject('LearnFlow – Test Email');
                }
            );
            return back()->with('success', 'Test email sent successfully to ' . $to);
        } catch (\Throwable $e) {
            Log::error('Mailer test failed', [
                'error' => $e->getMessage(),
                'config' => [
                    'host' => $host,
                    'port' => $port,
                    'scheme' => $scheme,
                    'user' => $config['username'] ?? 'null'
                ]
            ]);
            
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'Connection timed out') || str_contains($errorMessage, 'Network/Firewall Issue')) {
                $hint = ($port == 465 ? "TIP: Try Port 587 with TLS if 465 is blocked." : "TIP: Try Port 465 with SSL if 587 is blocked.");
                
                if (str_contains($host, 'jellyfish.systems')) {
                    $hint .= " WARNING: '$host' appears to be an incoming mail filter (MX). For outgoing mail (SMTP), you should likely use your primary mail server (e.g., mail.yourdomain.com).";
                }

                $errorMessage = "CONNECTION ERROR: Port $port is unreachable on $host. " . $hint . 
                               " If both fail, contact your hosting provider to 'unblock outgoing SMTP ports'.";
            } elseif (str_contains($errorMessage, 'Authentication failed')) {
                $errorMessage .= ' – TIP: Double check your SMTP username and password.';
            }

            return back()->with('error', 'Failed to send test email: ' . $errorMessage);
        }
    }
}
