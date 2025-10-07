<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        ]);

        $apiKey = 'sk_' . Str::random(40);
        $secret = Str::random(64);

        ApiKey::create([
            'client_name' => $request->client_name,
            'api_key' => $apiKey,
            'secret_hash' => hash('sha256', $secret),
            'allowed_ips' => $request->allowed_ips,
            'scopes' => $request->scopes ?? ['integration'],
            'status' => 'active',
        ]);

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API Key created successfully')
            ->with('api_key', $apiKey)
            ->with('api_secret', $secret);
    }

    public function destroy(ApiKey $apiKey)
    {
        $apiKey->update(['status' => 'revoked']);

        return redirect()->route('admin.api-keys.index')
            ->with('success', 'API Key revoked successfully');
    }
}
