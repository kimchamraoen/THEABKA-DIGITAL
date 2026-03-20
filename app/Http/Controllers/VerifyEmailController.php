<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Handle email verification links — works even when NOT logged in.
     *
     * Verifies the user's email using the signed URL (id + hash),
     * then redirects to login (if not authenticated) or dashboard (if authenticated).
     */
    public function __invoke(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Verify the hash matches the user's email
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        // Mark as verified if not already
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // If the user is logged in, go to dashboard with success indicator
        if ($request->user()) {
            return redirect('/dashboard?verified=1');
        }

        // Not logged in — redirect to login with a success message
        return redirect()->route('login')->with('status', 'Your email has been verified! Please log in to continue.');
    }
}
