<?php

namespace App\Livewire\Admin;

use App\Models\BroadcastNotification;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class BroadcastNotifications extends Component
{
    use WithPagination;

    public string $title = '';
    public string $message = '';
    public string $targetRole = 'user';

    public bool $showCreateModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'targetRole' => 'required|in:' . implode(',', $this->allowedTargets()),
        ];
    }

    /**
     * Determine which target roles the current user can broadcast to.
     */
    public function allowedTargets(): array
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return ['user', 'admin', 'all'];
        }

        if ($user->isAdmin()) {
            return ['user'];
        }

        return [];
    }

    public function openCreate(): void
    {
        $this->reset(['title', 'message']);
        $this->targetRole = 'user';
        $this->showCreateModal = true;
    }

    public function send(): void
    {
        $this->validate();

        $user = auth()->user();

        if (! $user->canBroadcast()) {
            session()->flash('error', 'You do not have permission to broadcast.');
            return;
        }

        $allowed = $this->allowedTargets();
        if (! in_array($this->targetRole, $allowed, true)) {
            session()->flash('error', 'You cannot broadcast to this audience.');
            return;
        }

        // Create the notification record
        $notification = BroadcastNotification::create([
            'sender_id' => $user->id,
            'title' => $this->title,
            'message' => $this->message,
            'target_role' => $this->targetRole,
        ]);

        // Determine recipient query
        $recipientQuery = User::query()->where('id', '!=', $user->id);

        if ($this->targetRole === 'user') {
            $recipientQuery->where('role', 'user');
        } elseif ($this->targetRole === 'admin') {
            $recipientQuery->where('role', 'admin');
        }
        // 'all' = all users except sender (for super_admin broadcasting to both admin + user)

        $recipientIds = $recipientQuery->pluck('id');
        $notification->recipients()->attach($recipientIds);

        $this->reset(['title', 'message', 'targetRole']);
        $this->showCreateModal = false;

        session()->flash('message', 'Notification broadcast to ' . $recipientIds->count() . ' recipient(s).');
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            $notification = BroadcastNotification::find($this->deletingId);
            if ($notification && $notification->sender_id === auth()->id()) {
                $notification->delete();
                session()->flash('message', 'Notification deleted.');
            }
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function render()
    {
        $query = BroadcastNotification::with('sender')
            ->withCount('recipients');

        // Admin can only see their own broadcasts; super_admin sees all
        if (! auth()->user()->isSuperAdmin()) {
            $query->where('sender_id', auth()->id());
        }

        $notifications = $query->orderByDesc('created_at')->paginate(10);

        return view('livewire.admin.broadcast-notifications', [
            'notifications' => $notifications,
            'allowedTargets' => $this->allowedTargets(),
        ]);
    }
}
