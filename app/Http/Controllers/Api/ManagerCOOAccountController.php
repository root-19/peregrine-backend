<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManagerCOOAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManagerCOOAccountController extends Controller
{
    public function index()
    {
        $accounts = ManagerCOOAccount::orderBy('created_at', 'desc')->get();
        return response()->json($accounts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:manager_coo_accounts',
            'password' => 'required|string|min:6',
            'company_name' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $account = ManagerCOOAccount::create($validated);

        return response()->json($account, 201);
    }

    public function show(string $id)
    {
        $account = ManagerCOOAccount::findOrFail($id);
        return response()->json($account);
    }

    public function update(Request $request, string $id)
    {
        $account = ManagerCOOAccount::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'last_name' => 'sometimes|required|string',
            'email' => ['sometimes', 'required', 'email', Rule::unique('manager_coo_accounts')->ignore($id)],
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
        $account = ManagerCOOAccount::findOrFail($id);
        $account->delete();

        return response()->json(['message' => 'Manager/COO Account deleted successfully']);
    }

    public function getByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $account = ManagerCOOAccount::where('email', $request->email)->first();

        if (!$account) {
            return response()->json(['message' => 'Manager/COO Account not found'], 404);
        }

        return response()->json($account);
    }
}
