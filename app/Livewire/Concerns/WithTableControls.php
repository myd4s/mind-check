<?php

namespace App\Livewire\Concerns;

/**
 * Search + sortable-column + per-page state shared oleh seluruh tabel data Livewire.
 * Dipakai bersama trait Livewire\WithPagination (menyediakan resetPage()).
 */
trait WithTableControls
{
    public string $search = '';

    public string $sortField = '';

    public string $sortDirection = 'asc';

    public int $perPage = 10;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
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

        $this->resetPage();
    }
}
