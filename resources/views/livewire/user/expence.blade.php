<div>
<div class="p-6">
    <div class="flex justify-between items-center">
        <div class="mb-5">
            <h1 class="text-2xl font-bold">{{ __('app.dashboard.Expense List') }}</h1>
            <p class="text-sm opacity-50 mt-0.5">{{ __('app.dashboard.welcome_back') }} {{ auth()->user()->name }}</p>
        </div>

        {{-- create expense --}}
        <button wire:click="openModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg transition shadow-sm">
            + {{ __('app.dashboard.Add Expense') }}
        </button>
    </div>

    <div class="flex justify-between items-center mb-6">
        <!-- <div class="flex gap-5">

            {{-- search group --}}
            <div class="flex justify-center items-center gap-3">
                <label class="block text-sm font-medium mb-1">Group: </label>
                <select wire:model.live="filgroup" class="w-full border-gray-300 bg-white/10 rounded-lg shadow-sm">
                    <option value="">Show All</option>
                    <option value="Bride">Bride</option>
                    <option value="Groom">Groom</option>
                    <option value="Nothing">Nothing</option>
                </select>
            </div>

            {{-- search statue --}}
            <div class="flex justify-center items-center gap-3">
                <label class="block text-sm font-medium mb-1">Status:</label>
                <select wire:model.live="filstatus" class="w-full bg-white/10 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500">
                    <option value="">Show All</option>
                    <option value="Coming">Coming</option>
                    <option value="Pending">Pending</option>
                    <option value="Not Coming">Not Coming</option>
                </select>
            </div>
        </div> -->
        <div class="flex gap-3">

            {{-- send link via telegram --}}
            <!-- <div x-data="{ open: false }" class="relative inline-block">
                <button @click="open = !open" class="bg-amber-600 text-white px-3 py-2.5 rounded-lg shadow hover:bg-amber-700">
                    Invite All
                </button>
                <div 
                    x-show="open" 
                    @click.away="open = false"
                    x-transition
                    class="absolute right-0 top-11 text-left w-64 p-4 bg-white border border-pink-200 rounded-2xl shadow-xl z-50"
                    x-cloak
                >
                    <h3 class="text-sm font-bold text-gray-700 mb-2">Invitation Link All</h3>
                    <input type="link" class="w-full p-3 bg-gray-50 border border-pink-400 rounded-xl break-all text-xs text-gray-600">
                    <div class="mt-3 flex justify-end">
                        <button 
                            class="text-xs text-blue-900 font-semibold bg-blue-300 rounded-lg px-2 py-2 hover:bg-blue-600 hover:text-blue-200"
                        >
                            Send
                        </button>
                    </div>
                </div>
            </div> -->

            {{-- export excel --}}
            <button wire:click="exportExcel" class="bg-green-600 text-white px-3 py-2 rounded-lg shadow hover:bg-green-700">
                Export
            </button>

            {{-- inport file excel --}}
            <div class="">
                <input type="file" id="auto_import" wire:model="file" class="hidden" accept=".xlsx,.xls,.csv" />

                <label for="auto_import" class="inline-flex items-center cursor-pointer group">
                    <div class="flex items-center space-x-3 py-2.5 px-2 bg-white border border-gray-200 rounded-lg shadow-sm group-hover:border-blue-400 group-hover:bg-blue-50 transition-all">
                        <span class="text-sm font-medium text-gray-700 group-hover:text-blue-700">
                            <span wire:loading.remove wire:target="file">Import</span>
                            <span wire:loading wire:target="file">Processing...</span>
                        </span>
                    </div>
                </label>

                @error('file') 
                    <p class="mt-2 text-xs text-red-500">{{ $message }}</p> 
                @enderror
            </div>
        </div>
    </div>

    {{-- Table Container --}}
    <div class="bg-white/10 rounded-xl shadow overflow-x-scroll border border-gray-100">
        <table class="w-full text-left ">
            <thead class="bg-white/50 text-black border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">No</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">Name</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">Category</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">mount</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">date</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">note</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($expense as $expense)
                <tr wire:key="expense-{{ $expense->id }}" class="hover:bg-white/30 transition">
                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 font-medium">{{ $expense->name }}</td>
                    <td class="px-6 py-4">{{ $expense->category }}</td>
                    <td class="px-6 py-4">${{ number_format($expense->amount, 2) }}</td>
                    <td class="px-6 py-4">{{ $expense->date }}</td>
                    <td class="px-6 py-4">{{ $expense->notes }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button wire:click="edit({{ $expense->id }})" class="text-indigo-600  hover:text-indigo-900 font-medium"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button wire:click="delete({{ $expense->id }})" wire:confirm="Are you sure?" class="text-red-600  hover:text-red-900 font-medium"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 flex justify-center items-center text-gray-400">No Expenses Now.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Single Dynamic Modal (Add & Edit) --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="closeModal"></div>
        
        <div class="bg-white rounded-xl shadow-2xl z-10 w-full max-w-md mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">{{ $expense_id ? 'Edit Expense' : 'Add New Expense' }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" wire:model="name" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input type="text" wire:model="amount" class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categories</label>
                        <select wire:model="category" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">Select category</option>
                            <option value="Food">Food</option>
                            <option value="Drink">Drink</option>
                            <option value="Necessaries">Necessaries</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" wire:model="date" class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                    <textarea wire:model="notes" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</button>
                <button wire:click="save" class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition">
                    {{ $expense_id ? 'Update Changes' : 'Save Expense' }}
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<script>

function openAddModal(){
document.getElementById('addModal').classList.remove('hidden')
}

function closeAddModal(){
document.getElementById('addModal').classList.add('hidden')
}

function closeEditModal(){
document.getElementById('editModal').classList.add('hidden')
}

function editGuest(guest){

document.getElementById('editModal').classList.remove('hidden')

document.getElementById('edit_name').value = guest.guest_name
document.getElementById('edit_phone').value = guest.phone
document.getElementById('edit_group').value = guest.group
document.getElementById('edit_status').value = guest.statue
document.getElementById('edit_note').value = guest.note

document.getElementById('editForm').action = "/guests/"+guest.id

}

</script>
</div>