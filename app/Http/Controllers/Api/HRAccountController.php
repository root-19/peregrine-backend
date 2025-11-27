<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HRAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class HRAccountController extends Controller
{
    public function index()
    {
        $accounts = HRAccount::orderBy('created_at', 'desc')->get();
        return response()->json($accounts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:hr_accounts',
            'password' => 'required|string|min:6',
            'company_name' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $account = HRAccount::create($validated);

        return response()->json($account, 201);
    }

    public function show(string $id)
    {
        $account = HRAccount::findOrFail($id);
        return response()->json($account);
    }

    public function update(Request $request, string $id)
    {
        $account = HRAccount::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'last_name' => 'sometimes|required|string',
            'email' => ['sometimes', 'required', 'email', Rule::unique('hr_accounts')->ignore($id)],
            'password' => 'sometimes|string|min:6',
            'company_name' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $account->update($validated);

        return response()->json($account);
    }

    public function destroy(string $id)
    {
        $account = HRAccount::findOrFail($id);
        $account->delete();

        return response()->json(['message' => 'HR Account deleted successfully']);
    }

    public function getByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $account = HRAccount::where('email', $request->email)->first();

        if (!$account) {
            return response()->json(['message' => 'HR Account not found'], 404);
        }

        return response()->json($account);
    }
}
