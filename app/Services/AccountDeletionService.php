<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountDeletionService
{
    /**
     * Fully delete a user account and all associated data.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Delete chat messages (via conversations)
            $conversationIds = $user->chatConversations()->pluck('id');
            if ($conversationIds->isNotEmpty()) {
                \App\Models\ChatMessage::whereIn('chat_conversation_id', $conversationIds)->delete();
            }

            // Delete chat conversations
            $user->chatConversations()->delete();

            // Delete user chatbot settings
            $user->chatbotSettings()->delete();

            // Delete social accounts
            $user->socialAccounts()->delete();

            // Delete profile photo if exists
            if ($user->profile_photo_path) {
                $user->deleteProfilePhoto();
            }

            // Revoke API tokens
            $user->tokens->each->delete();

            // Flush all sessions for this user
            $this->flushUserSessions($user);

            // Hard delete the user
            $user->forceDelete();

            Log::info('User account fully deleted', ['user_id' => $user->id, 'email' => $user->email]);
        });
    }

    /**
     * Remove sessions for a specific user.
     */
    private function flushUserSessions(User $user): void
    {
        $driver = config('session.driver');

        if ($driver === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->delete();
        }
    }
}
