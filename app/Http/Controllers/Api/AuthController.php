<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\LoginNotification;
use App\Models\HRAccount;
use App\Models\ManagerCOOAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'account_type' => 'required|in:user,hr,manager_coo',
        ]);

        $accountType = $request->account_type;
        $email = $request->email;
        $password = $request->password;

        $account = null;
        $token = null;

        switch ($accountType) {
            case 'hr':
                $account = HRAccount::where('email', $email)->first();
                if ($account && Hash::check($password, $account->password)) {
                    $token = $account->createToken('hr-token', ['hr'])->plainTextToken;
                }
                break;

            case 'manager_coo':
                $account = ManagerCOOAccount::where('email', $email)->first();
                if ($account && Hash::check($password, $account->password)) {
                    $token = $account->createToken('manager-coo-token', ['manager_coo'])->plainTextToken;
                }
                break;

            case 'user':
                $account = User::where('email', $email)->first();
                if ($account && Hash::check($password, $account->password)) {
                    $token = $account->createToken('user-token', ['user'])->plainTextToken;
                }
                break;
        }

        if (!$account || !$token) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Get account name
        $accountName = $account->name . ' ' . ($account->last_name ?? '');
        $accountName = trim($accountName);
        
        // Get IP address from request
        $ipAddress = $request->ip();
        
        // Log login in terminal/console
        $loginTime = now()->toDateTimeString();
        
        // Display in terminal/console using error_log (won't affect HTTP response)
        error_log("\n");
        error_log("========================================");
        error_log("🔐 LOGIN SUCCESSFUL - Backend API");
        error_log("========================================");
        error_log("👤 Account Name: {$accountName}");
        error_log("📧 Email: {$email}");
        error_log("🔑 Account Type: " . strtoupper($accountType));
        error_log("⏰ Login Time: {$loginTime}");
        error_log("🌐 IP Address: {$ipAddress}");
        error_log("🆔 User ID: {$account->id}");
        error_log("========================================\n");
        
        // Also log to Laravel log file
        Log::info('User Login Detected', [
            'email' => $email,
            'account_type' => $accountType,
            'account_name' => $accountName,
            'ip_address' => $ipAddress,
            'login_time' => $loginTime,
        ]);
        
        // Return response immediately - email will be sent in background
        $response = response()->json([
            'account' => $account,
            'token' => $token,
            'account_type' => $accountType,
        ]);

        // Send email notification asynchronously (non-blocking)
        // Queue the email so it doesn't block the login response
        try {
            Mail::to($email)->queue(new LoginNotification(
                $accountName,
                $email,
                $accountType,
                $ipAddress
            ));
            
            Log::info('Login notification email queued (will be sent in background)', [
                'email' => $email,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the login
            Log::error('Failed to queue login notification email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }

        return $response;
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $accessToken->delete();
            }
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $account = $accessToken->tokenable;
        
        return response()->json([
            'account' => $account,
            'account_type' => $this->getAccountType($account),
        ]);
    }

    private function getAccountType($account)
    {
        if ($account instanceof HRAccount) {
            return 'hr';
        } elseif ($account instanceof ManagerCOOAccount) {
            return 'manager_coo';
        } elseif ($account instanceof User) {
            return 'user';
        }
        return null;
    }

    /**
     * Check if email exists in any account table
     */
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        
        // Check in all account tables
        $account = null;
        $accountType = null;

        // Check HR accounts
        $hrAccount = HRAccount::where('email', $email)->first();
        if ($hrAccount) {
            $account = $hrAccount;
            $accountType = 'hr';
        }

        // Check Manager/COO accounts
        if (!$account) {
            $managerAccount = ManagerCOOAccount::where('email', $email)->first();
            if ($managerAccount) {
                $account = $managerAccount;
                $accountType = 'manager_coo';
            }
        }

        // Check User accounts
        if (!$account) {
            $userAccount = User::where('email', $email)->first();
            if ($userAccount) {
                $account = $userAccount;
                $accountType = 'user';
            }
        }

        if (!$account) {
            return response()->json([
                'exists' => false,
                'message' => 'Email not found in any account',
            ], 404);
        }

        return response()->json([
            'exists' => true,
            'account_type' => $accountType,
            'name' => $account->name,
        ]);
    }

    /**
     * Reset password for an account
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'new_password' => 'required|min:6',
        ]);

        $email = $request->email;
        $newPassword = $request->new_password;
        
        // Find account in all tables
        $account = null;
        $accountType = null;

        // Check HR accounts
        $hrAccount = HRAccount::where('email', $email)->first();
        if ($hrAccount) {
            $account = $hrAccount;
            $accountType = 'hr';
        }

        // Check Manager/COO accounts
        if (!$account) {
            $managerAccount = ManagerCOOAccount::where('email', $email)->first();
            if ($managerAccount) {
                $account = $managerAccount;
                $accountType = 'manager_coo';
            }
        }

        // Check User accounts
        if (!$account) {
            $userAccount = User::where('email', $email)->first();
            if ($userAccount) {
                $account = $userAccount;
                $accountType = 'user';
            }
        }

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found',
            ], 404);
        }

        // Update password
        $account->password = Hash::make($newPassword);
        $account->save();

        Log::info('Password reset successful', [
            'email' => $email,
            'account_type' => $accountType,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }
}
