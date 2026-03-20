<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use App\Services\AccountDeletionService;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        app(AccountDeletionService::class)->delete($user);
    }
}
