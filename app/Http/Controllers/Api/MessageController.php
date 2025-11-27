<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HRAccount;
use App\Models\ManagerCOOAccount;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class MessageController extends Controller
{
    /**
     * Get the current authenticated account and its type.
     */
    private function getCurrentAccount(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return [null, null];
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return [null, null];
        }

        $account = $accessToken->tokenable;
        $accountType = $this->getAccountType($account);

        return [$account, $accountType];
    }

    /**
     * Get account type from account instance.
     */
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
     * Get messages between the authenticated user and another user.
     */
    public function getMessages(Request $request, $userId)
    {
        $request->validate([
            'userType' => 'nullable|in:user,hr,manager_coo',
        ]);

        [$currentAccount, $currentAccountType] = $this->getCurrentAccount($request);
        if (!$currentAccount || !$currentAccountType) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$userId || !is_numeric($userId)) {
            return response()->json(['message' => 'Invalid user ID'], 422);
        }

        $receiverType = $request->input('userType', 'user');
        $receiverId = (int) $userId;

        // Get messages where current account is sender or receiver with the specified account
        $messages = Message::where(function ($query) use ($currentAccount, $currentAccountType, $receiverId, $receiverType) {
            $query->where('sender_type', $currentAccountType)
                  ->where('sender_id', $currentAccount->id)
                  ->where('receiver_type', $receiverType)
                  ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($currentAccount, $currentAccountType, $receiverId, $receiverType) {
            $query->where('sender_type', $receiverType)
                  ->where('sender_id', $receiverId)
                  ->where('receiver_type', $currentAccountType)
                  ->where('receiver_id', $currentAccount->id);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages as read where current account is the receiver
        Message::where('receiver_type', $currentAccountType)
               ->where('receiver_id', $currentAccount->id)
               ->where('sender_type', $receiverType)
               ->where('sender_id', $receiverId)
               ->where('is_read', false)
               ->update([
                   'is_read' => true,
                   'read_at' => now(),
               ]);

        return response()->json($messages);
    }

    /**
     * Send a message to another user.
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|in:user,hr,manager_coo',
            'message' => 'required|string|max:1000',
        ]);

        [$currentAccount, $currentAccountType] = $this->getCurrentAccount($request);
        if (!$currentAccount || !$currentAccountType) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Prevent users from sending messages to themselves
        if ($currentAccount->id == $validated['receiver_id'] && 
            $currentAccountType == $validated['receiver_type']) {
            return response()->json(['message' => 'You cannot send a message to yourself'], 422);
        }

        $message = Message::create([
            'sender_type' => $currentAccountType,
            'sender_id' => $currentAccount->id,
            'receiver_type' => $validated['receiver_type'],
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return response()->json($message, 201);
    }

    /**
     * Get all conversations for the authenticated user.
     */
    public function getConversations(Request $request)
    {
        [$currentAccount, $currentAccountType] = $this->getCurrentAccount($request);
        if (!$currentAccount || !$currentAccountType) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Get the latest message for each conversation
        $conversations = Message::select([
                DB::raw('CASE 
                    WHEN sender_type = "' . $currentAccountType . '" AND sender_id = ' . $currentAccount->id . ' THEN receiver_type 
                    ELSE sender_type 
                END as other_account_type'),
                DB::raw('CASE 
                    WHEN sender_type = "' . $currentAccountType . '" AND sender_id = ' . $currentAccount->id . ' THEN receiver_id 
                    ELSE sender_id 
                END as other_account_id'),
                DB::raw('MAX(created_at) as last_message_at'),
                DB::raw('MAX(id) as last_message_id')
            ])
            ->where(function ($query) use ($currentAccount, $currentAccountType) {
                $query->where(function ($q) use ($currentAccount, $currentAccountType) {
                    $q->where('sender_type', $currentAccountType)
                      ->where('sender_id', $currentAccount->id);
                })->orWhere(function ($q) use ($currentAccount, $currentAccountType) {
                    $q->where('receiver_type', $currentAccountType)
                      ->where('receiver_id', $currentAccount->id);
                });
            })
            ->groupBy('other_account_type', 'other_account_id')
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Get account details and last message for each conversation
        $conversationsWithDetails = $conversations->map(function ($conversation) use ($currentAccount, $currentAccountType) {
            $otherAccount = $this->getAccountByTypeAndId($conversation->other_account_type, $conversation->other_account_id);
            $lastMessage = Message::find($conversation->last_message_id);

            if (!$otherAccount) {
                return null;
            }

            // Count unread messages
            $unreadCount = Message::where('sender_type', $conversation->other_account_type)
                                 ->where('sender_id', $conversation->other_account_id)
                                 ->where('receiver_type', $currentAccountType)
                                 ->where('receiver_id', $currentAccount->id)
                                 ->where('is_read', false)
                                 ->count();

            return [
                'user' => [
                    'id' => $otherAccount->id,
                    'name' => $otherAccount->name,
                    'last_name' => $otherAccount->last_name ?? '',
                    'email' => $otherAccount->email,
                    'type' => $conversation->other_account_type,
                ],
                'last_message' => $lastMessage ? [
                    'id' => $lastMessage->id,
                    'message' => $lastMessage->message,
                    'created_at' => $lastMessage->created_at,
                    'is_sender' => $lastMessage->sender_type === $currentAccountType && 
                                  $lastMessage->sender_id === $currentAccount->id,
                ] : null,
                'unread_count' => $unreadCount,
                'last_message_at' => $conversation->last_message_at,
            ];
        })->filter();

        return response()->json($conversationsWithDetails->values());
    }

    /**
     * Get account by type and ID.
     */
    private function getAccountByTypeAndId($type, $id)
    {
        switch ($type) {
            case 'hr':
                return HRAccount::find($id);
            case 'manager_coo':
                return ManagerCOOAccount::find($id);
            case 'user':
                return User::find($id);
            default:
                return null;
        }
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(Request $request, $userId)
    {
        $request->validate([
            'userType' => 'required|in:user,hr,manager_coo',
        ]);

        [$currentAccount, $currentAccountType] = $this->getCurrentAccount($request);
        if (!$currentAccount || !$currentAccountType) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $senderType = $request->input('userType', 'user');
        $senderId = (int) $userId;

        $updated = Message::where('receiver_type', $currentAccountType)
                         ->where('receiver_id', $currentAccount->id)
                         ->where('sender_type', $senderType)
                         ->where('sender_id', $senderId)
                         ->where('is_read', false)
                         ->update([
                             'is_read' => true,
                             'read_at' => now(),
                         ]);

        return response()->json([
            'message' => 'Messages marked as read',
            'updated_count' => $updated,
        ]);
    }

    /**
     * Get unread message count for the authenticated user.
     */
    public function getUnreadCount(Request $request)
    {
        [$currentAccount, $currentAccountType] = $this->getCurrentAccount($request);
        if (!$currentAccount || !$currentAccountType) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $unreadCount = Message::where('receiver_type', $currentAccountType)
                             ->where('receiver_id', $currentAccount->id)
                             ->where('is_read', false)
                             ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }
}
