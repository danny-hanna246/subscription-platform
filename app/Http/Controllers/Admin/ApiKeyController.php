<?php
// app/Http/Controllers/Admin/ApiKeyController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ApiKeyController extends Controller
{
    public function index()
    {
        $apiKeys = ApiKey::latest()->paginate(15);
        return view('admin.api-keys.index', compact('apiKeys'));
    }

    public function create()
    {
        return view('admin.api-keys.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:150',
            'allowed_ips' => 'nullable|string',
            'scopes' => 'nullable|array',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
        ]);

        $apiKey = 'sk_' . Str::random(40);
        $secret = Str::random(64);

        // حساب تاريخ الانتهاء
        $expiresAt = $request->expires_in_days
            ? now()->addDays($request->expires_in_days)
            : null;

        $createdKey = ApiKey::create([
            'client_name' => $request->client_name,
            'api_key' => $apiKey,
            'secret_hash' => hash('sha256', $secret),
            'allowed_ips' => $request->allowed_ips,
            'scopes' => $request->scopes ?? ['integration'],
            'status' => 'active',
            'expires_at' => $expiresAt,
        ]);

        // تسجيل الحدث
        Log::info('API Key created', [
            'id' => $createdKey->id,
            'client_name' => $createdKey->client_name,
            'created_by' => auth('admin')->id(),
            'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : 'never',
        ]);

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API Key created successfully')
            ->with('api_key', $apiKey)
            ->with('api_secret', $secret)
            ->with('expires_at', $expiresAt);
    }

    public function destroy(ApiKey $apiKey)
    {
        $apiKey->update(['status' => 'revoked']);

        Log::warning('API Key revoked', [
            'id' => $apiKey->id,
            'client_name' => $apiKey->client_name,
            'revoked_by' => auth('admin')->id(),
        ]);

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API Key revoked successfully');
    }

    /**
     * تجديد API Key
     */
    public function renew(ApiKey $apiKey, Request $request)
    {
        $request->validate([
            'extends_days' => 'required|integer|min:1|max:365',
        ]);

        $newExpiresAt = $apiKey->expires_at
            ? $apiKey->expires_at->addDays($request->extends_days)
            : now()->addDays($request->extends_days);

        $apiKey->update([
            'expires_at' => $newExpiresAt,
            'status' => 'active',
        ]);

        Log::info('API Key renewed', [
            'id' => $apiKey->id,
            'client_name' => $apiKey->client_name,
            'new_expires_at' => $newExpiresAt->toDateTimeString(),
            'renewed_by' => auth('admin')->id(),
        ]);

        return redirect()->back()->with('success', 'API Key renewed successfully');
    }
}
