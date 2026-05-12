<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Expence as ExpenseModel;
use Illuminate\Support\Facades\Auth;

class Expence extends Component
{
    public $name, $amount, $date, $category, $notes, $expense, $expense_id, $user_id;
    public $isModalOpen = false;

    public function mount()
    {
        $this->expense = ExpenseModel::where('user_id', Auth::id())->get();

        $this->name = '';
        $this->amount = '';
        $this->date = '';
        $this->category = '';
        $this->notes = '';
    }

    public function openModal()
    {
        // $this->reset(['name', 'amount', 'date', 'category', 'notes']);
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'nullable|date',
            'category' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($this->expense_id) {
            $expense = ExpenseModel::find($this->expense_id);
            $expense->update([
                'user_id' => Auth::id(),
                'name' => $this->name,
                'amount' => $this->amount,
                'date' => $this->date,
                'category' => $this->category,
                'notes' => $this->notes,
            ]);
        } else {
            ExpenseModel::create([
                'user_id' => Auth::id(),
                'name' => $this->name,
                'amount' => $this->amount,
                'date' => $this->date,
                'category' => $this->category,
                'notes' => $this->notes,
            ]);

            $this->reset(['name', 'amount', 'date', 'category', 'notes']);
        }

        $this->closeModal();
        $this->save();
    }

    public function edit($id)
    {
        $expense = ExpenseModel::where('id', $id)->where('user_id', Auth::id())->findOrFail($id);

        $this->expense_id = $expense->id;
        $this->name = $expense->name;
        $this->amount = $expense->amount;
        $this->date = $expense->date;
        $this->category = $expense->category;
        $this->notes = $expense->notes;

        $this->isModalOpen = true;
    }

    public function update($id)
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'nullable|date',
            'category' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        $expense = ExpenseModel::where('user_id', Auth::id())->findOrFail($id);
        $expense->update([
            'name' => $this->name,
            'amount' => $this->amount,
            'date' => $this->date,
            'category' => $this->category,
            'notes' => $this->notes,
        ]);

        // Reset form fields
        // $this->reset(['name', 'amount', 'date', 'category', 'notes']);

        $this->closeModal();
    }

    public function delete($id)
    {
        $expence = ExpenseModel::where('user_id', Auth::id())->findOrFail($id);
        $expence->delete();
    }

    public function render()
    {
        return view('livewire.user.expence')->layout('layouts.app');
    }
}
