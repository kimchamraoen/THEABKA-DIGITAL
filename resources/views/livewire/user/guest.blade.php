<div>
<div class="p-6">
    <div class="flex justify-between items-center">
        <div class="mb-5">
            <h1 class="text-2xl font-bold">{{ __('app.dashboard.Guests List') }}</h1>
            <p class="text-sm opacity-50 mt-0.5">{{ __('app.dashboard.welcome_back') }} {{ auth()->user()->name }}</p>
        </div>

        {{-- create guest --}}
        <button wire:click="openModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg transition shadow-sm">
            + {{ __('app.dashboard.Add Guest') }}
        </button>
    </div>

    @if (session()->has('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 3000)"
            class="fixed top-5 right-5 z-50 bg-green-500 text-white px-5 py-3 rounded-xl shadow-lg"
        >
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div class="flex gap-5">

            {{-- search group --}}
            <div class="flex justify-center items-center gap-3">
                <label class="block text-sm font-medium mb-1">{{ __('app.dashboard.Group') }}: </label>
                <select wire:model.live="filgroup" class="w-full border-gray-300 bg-white/10 rounded-lg shadow-sm">
                    <option value="">Show All</option>
                    <option value="Bride">Bride</option>
                    <option value="Groom">Groom</option>
                    <option value="Nothing">Nothing</option>
                </select>
            </div>

            {{-- search statue --}}
            <div class="flex justify-center items-center gap-3">
                <label class="block text-sm font-medium mb-1">{{ __('app.dashboard.Status') }}:</label>
                <select wire:model.live="filstatus" class="w-full bg-white/10 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500">
                    <option value="">Show All</option>
                    <option value="Coming">Coming</option>
                    <option value="Pending">Pending</option>
                    <option value="Not Coming">Not Coming</option>
                </select>
            </div>
        </div>
        <div class="flex gap-3">

            {{-- send link via telegram --}}
            <div x-data="{ open: false }" class="relative inline-block">
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
            </div>

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
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">ID</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">Name</th>
                    <!-- <th class="px-6 py-3 text-xs font-semibold  uppercase">Phone</th> -->
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">Group</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">Amount</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">Gift</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase">Greeting</th>
                    <th class="px-6 py-3 text-xs font-semibold  uppercase text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($guests as $guest)
                <tr wire:key="guest-{{ $guest->id }}" class="hover:bg-white/30 transition">
                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 font-medium">{{ $guest->guest_name }}</td>
                    <!-- <td class="px-6 py-4">{{ $guest->phone ?? 'N/A' }}</td> -->
                    <td class="px-6 py-4">{{ $guest->group }}</td>
                    <td class="px-2 py-1">
                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                            {{ match($guest->statue) {
                                'Coming' => 'bg-green-100 text-green-700',
                                'Pending' => 'bg-amber-100 text-amber-700',
                                'Not Coming' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700', 
                            } }}">
                            {{ $guest->statue }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-medium">{{ number_format($guest->gift_money ?? 0, 2) }}  {{ $guest->note }}</td>
                    <td class="px-6 py-4">
                        {{ $guest->gift ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">{{ $guest->Greeting ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-right space-x-2">

                        {{-- send link via telegram --}}
                        <div x-data="{ 
                                open: false, 
                                isBelow: true, 
                                copied: localStorage.getItem('invitation_copied_{{ $guest->uuid }}') === 'true',
                                toggle() {
                                    if (!this.open) {
                                        const rect = $el.getBoundingClientRect();
                                        this.isBelow = rect.top < (window.innerHeight / 2);
                                    }
                                    this.open = !this.open;
                                },
                                copyLink(link) {
                                    if (!this.copied) {
                                        navigator.clipboard.writeText(link);
                                        this.copied = true;
                                        localStorage.setItem('invitation_copied_{{ $guest->uuid }}', 'true'); // save permanently
                                    }
                                    this.open = false;
                                }
                            }" class="relative inline-block">

                            <!-- Main Button -->
                            <button @click="toggle()" class="text-amber-600 hover:text-amber-900 font-medium">
                                <template x-if="!copied">
                                    <i class="fa-regular fa-paper-plane"></i>
                                </template>
                                <template x-if="copied">
                                    <i class="fa-solid fa-check-double"></i>
                                </template>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" @click.away="open = false" x-transition x-cloak
                                :class="isBelow ? 'top-full mt-2' : 'bottom-full mb-2'"
                                class="absolute right-8 text-left w-96 p-4 bg-white border border-pink-200 rounded-2xl shadow-xl z-50">

                                <h3 class="text-sm font-bold text-gray-700">Invitation Link</h3>

                                <div class="w-auto items-center justify-between bg-white/50 p-2 rounded-2xl border border-white/50 shadow-sm">
                                    
                                    <!-- Telegram bot link with UUID -->
                                    <p class="text-xs text-blue-500 bg-slate-400/10 border rounded-xl p-4 m-2 break-all">
                                       http://127.0.0.1:8000/invitation/guest/{{ $guest->uuid }}
                                    </p>

                                    <div class="flex space-x-2">
    
                                            <!-- Copy Link -->
                                            <button 
                                                @click="
                                                    navigator.clipboard.writeText('{{ 'http://127.0.0.1:8000/invitation/guest/' . $guest->uuid }}');
                                                    alert('Link copied!');
                                                "
                                                class="bg-blue-600/80 text-white px-3 py-1 rounded text-xs">
                                                Copy Link
                                            </button>

                                            <!-- Share Link -->
                                            <a 
                                                href="https://t.me/share/url?url={{ urlencode('http://127.0.0.1:8000/invitation/guest/' . $guest->uuid) }}&text={{ urlencode('🎉 You are invited! Open your personal invitation:') }}"
                                                target="_blank"
                                                class="bg-green-600/80 text-white px-3 py-1 rounded text-xs">
                                                Share
                                            </a>

                                        <!-- </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button wire:click="edit({{ $guest->id }})" class="text-indigo-600  hover:text-indigo-900 font-medium"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button wire:click="confirmDelete({{ $guest->id }})" class="text-red-600  hover:text-red-900 font-medium"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 flex justify-center items-center text-gray-400">No guests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- modal delete -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            
            <div class="bg-white rounded-xl shadow-lg w-full max-w-xl p-6">

                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    Delete Guest
                </h2><hr class="mb-4">

                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete 
                    <span class="text-xl font-bold">
                        {{ $deleteGuestName }}
                    </span>
                    from the guest list?
                </p>

                <div class="flex justify-end gap-3">

                    <button
                        wire:click="$set('showDeleteModal', false)"
                        class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100 text-black"
                    >
                        Cancel
                    </button>

                    <button
                        wire:click="deleteGuest"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700"
                    >
                        Delete
                    </button>

                </div>

            </div>

        </div>
    @endif

    {{-- Single Dynamic Modal (Add & Edit) --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="closeModal"></div>
        
        <div class="bg-white rounded-xl shadow-2xl z-10 w-full max-w-md mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">{{ $guest_id ? 'Edit Guest' : 'Add New Guest' }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guest Name</label>
                    <input type="text" wire:model="guest_name" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" wire:model="phone" class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                        <select wire:model="group" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">Select group</option>
                            <option value="Bride">Bride</option>
                            <option value="Groom">Groom</option>
                            <option value="Nothing">Nothing</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gift Money</label>
                        <input type="text" wire:model="gift_money" class="w-full border-gray-300 rounded-lg shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                        <select wire:model="note" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">Select currency</option>
                            <option value="USD">USD</option>
                            <option value="KHR">KHR</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gift</label>
                    <textarea wire:model="gift" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model="statue" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">Select Status</option>
                        <option value="Coming">Coming</option>
                        <option value="Pending">Pending</option>
                        <option value="Not Coming">Not Coming</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Greeting</label>
                    <textarea wire:model="Greeting" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Cancel</button>
                <button wire:click="save" class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition">
                    {{ $guest_id ? 'Update Changes' : 'Save Guest' }}
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