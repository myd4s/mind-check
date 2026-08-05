<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Livewire\Concerns\WithTableControls;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class GuruBkAccountManagement extends Component
{
    use WithPagination, WithTableControls;

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public bool $showModal = false;

    #[Computed]
    public function accounts()
    {
        return User::where('role', UserRole::GuruBk)
            ->when($this->search, fn ($query) => $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->orderBy($this->sortField ?: 'name', $this->sortField ? $this->sortDirection : 'asc')
            ->paginate($this->perPage);
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $user = User::where('role', UserRole::GuruBk)->findOrFail($id);

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingId)],
            'password' => $this->editingId ? 'nullable|string|min:8' : 'required|string|min:8',
        ]);

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->fill(['name' => $validated['name'], 'email' => $validated['email']]);
            if (filled($validated['password'])) {
                $user->password = $validated['password'];
            }
            $user->save();
        } else {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => UserRole::GuruBk,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        $this->closeModal();
    }

    public function toggleActive(int $id): void
    {
        $user = User::where('role', UserRole::GuruBk)->findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'email', 'password']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.guru-bk-account-management');
    }
}
