<?php

namespace App\Livewire;

use App\Models\Payout;
use App\Models\User;
use App\Services\PayoutService;
use Livewire\Component;
use Livewire\WithPagination;

class AdminPayouts extends Component
{
    use WithPagination;

    public string $statusFilter = '';
    public string $search = '';

    public bool $showCreateModal = false;
    public ?int $selectedInstructorId = null;
    public string $periodStart = '';
    public string $periodEnd = '';
    public string $notes = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(?int $instructorId = null): void
    {
        $this->selectedInstructorId = $instructorId;
        $this->periodStart = now()->startOfMonth()->toDateString();
        $this->periodEnd = now()->endOfMonth()->toDateString();
        $this->notes = '';
        $this->showCreateModal = true;
    }

    public function createPayout(): void
    {
        $this->validate([
            'selectedInstructorId' => 'required|exists:users,id',
            'periodStart' => 'required|date',
            'periodEnd' => 'required|date|after:periodStart',
        ]);

        $instructor = User::findOrFail($this->selectedInstructorId);
        $service = new PayoutService();
        $payout = $service->createPayout($instructor, $this->periodStart, $this->periodEnd, $this->notes ?: null);

        if ($payout) {
            session()->flash('success', 'Payout of ' . $payout->formattedAmount() . ' created for ' . $instructor->name);
        } else {
            session()->flash('error', 'No earnings found for this period.');
        }

        $this->showCreateModal = false;
    }

    public function markPaid(int $payoutId): void
    {
        $payout = Payout::findOrFail($payoutId);
        $payout->markPaid();
        session()->flash('success', 'Payout marked as paid.');
    }

    public function cancel(int $payoutId): void
    {
        Payout::where('id', $payoutId)->update(['status' => 'cancelled']);
        session()->flash('success', 'Payout cancelled.');
    }

    public function render()
    {
        $service = new PayoutService();
        $instructorSummaries = $service->getInstructorsPendingPayouts();

        $payoutQuery = Payout::with('instructor')->latest();

        if ($this->statusFilter) {
            $payoutQuery->where('status', $this->statusFilter);
        }
        if ($this->search) {
            $payoutQuery->whereHas('instructor', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        $instructors = User::role('instructor')->orderBy('name')->get(['id', 'name']);

        return view('livewire.admin-payouts', [
            'payouts' => $payoutQuery->paginate(15),
            'instructorSummaries' => $instructorSummaries,
            'instructors' => $instructors,
        ]);
    }
}
