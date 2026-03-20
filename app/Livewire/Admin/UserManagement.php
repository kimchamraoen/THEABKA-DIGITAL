<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AccountDeletionService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class UserManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Search & Filter
    public string $search = '';
    public string $roleFilter = '';
    public string $verifiedFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Edit modal state
    public bool $showEditModal = false;
    public ?int $editingUserId = null;
    public string $editName = '';
    public string $editEmail = '';
    public string $editRole = 'user';
    public $editPhoto = null;

    // Create modal state
    public bool $showCreateModal = false;
    public string $createName = '';
    public string $createEmail = '';
    public string $createPassword = '';
    public string $createRole = 'user';

    // Delete confirmation
    public bool $showDeleteModal = false;
    public ?int $deletingUserId = null;
    public string $deletingUserName = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'verifiedFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingVerifiedFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Admin-verify a user's email without them needing to check their inbox.
     */
    public function verifyUserEmail(int $userId): void
    {
        $user = User::findOrFail($userId);

        if (! $user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
            session()->flash('message', "Email verified for {$user->name}.");
        }
    }

    /**
     * Unverify a user's email.
     */
    public function unverifyUserEmail(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => null])->save();
            session()->flash('message', "Email unverified for {$user->name}.");
        }
    }

    /**
     * Toggle a user's email verification bypass flag.
     */
    public function toggleBypassEmailVerification(int $userId): void
    {
        $user = User::findOrFail($userId);

        $user->bypass_email_verification = !$user->bypass_email_verification;
        $user->save();

        $status = $user->bypass_email_verification ? 'enabled' : 'disabled';
        session()->flash('message', "Email verification bypass {$status} for {$user->name}.");
    }

    /**
     * Toggle a user's role between user and super_admin.
     */
    public function toggleRole(int $userId): void
    {
        $user = User::findOrFail($userId);

        // Prevent demoting yourself
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot change your own role.');
            return;
        }

        $user->role = $user->role === 'super_admin' ? 'user' : 'super_admin';
        $user->save();

        session()->flash('message', "Role updated for {$user->name}.");
    }

    /**
     * Open edit modal for a user.
     */
    public function editUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email ?? '';
        $this->editRole = $user->role ?? 'user';
        $this->editPhoto = null;
        $this->showEditModal = true;
    }

    /**
     * Save edited user.
     */
    public function updateUser(): void
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|max:255|unique:users,email,' . $this->editingUserId,
            'editRole' => 'required|in:user,super_admin',
            'editPhoto' => 'nullable|image|max:2048',
        ]);

        $user = User::findOrFail($this->editingUserId);
        $user->name = $this->editName;

        // If email changed, reset verification
        if ($user->email !== $this->editEmail) {
            $user->email = $this->editEmail;
            $user->email_verified_at = null;
        }

        $user->role = $this->editRole;

        if ($this->editPhoto) {
            $user->updateProfilePhoto($this->editPhoto);
        }

        $user->save();

        $this->showEditModal = false;
        $this->editingUserId = null;
        session()->flash('message', "User {$user->name} updated successfully.");
    }

    /**
     * Open create modal.
     */
    public function openCreateModal(): void
    {
        $this->reset(['createName', 'createEmail', 'createPassword', 'createRole']);
        $this->createRole = 'user';
        $this->showCreateModal = true;
    }

    /**
     * Create a new user.
     */
    public function createUser(): void
    {
        $this->validate([
            'createName' => 'required|string|max:255',
            'createEmail' => 'required|email|max:255|unique:users,email',
            'createPassword' => 'required|string|min:8',
            'createRole' => 'required|in:user,super_admin',
        ]);

        $user = User::create([
            'name' => $this->createName,
            'email' => $this->createEmail,
            'password' => bcrypt($this->createPassword),
            'role' => $this->createRole,
            'email_verified_at' => now(), // Admin-created users are auto-verified
        ]);

        $this->showCreateModal = false;
        session()->flash('message', "User {$user->name} created successfully.");
    }

    /**
     * Confirm user deletion.
     */
    public function confirmDelete(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $this->deletingUserId = $user->id;
        $this->deletingUserName = $user->name;
        $this->showDeleteModal = true;
    }

    /**
     * Delete the user.
     */
    public function deleteUser(): void
    {
        if ($this->deletingUserId === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $user = User::findOrFail($this->deletingUserId);
        $name = $user->name;

        app(AccountDeletionService::class)->delete($user);

        $this->showDeleteModal = false;
        $this->deletingUserId = null;
        session()->flash('message', "User {$name} deleted successfully.");
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        if ($this->verifiedFilter === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($this->verifiedFilter === 'unverified') {
            $query->whereNull('email_verified_at');
        }

        $users = $query->with('socialAccounts')
                       ->orderBy($this->sortField, $this->sortDirection)
                       ->paginate(10);

        return view('livewire.admin.user-management', compact('users'));
    }
}
