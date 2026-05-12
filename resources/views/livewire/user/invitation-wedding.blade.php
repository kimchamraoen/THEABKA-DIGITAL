<div>

<h2 class="text-xl font-bold mb-4">Wedding Template Editor</h2>

{{-- Show button if user has no template yet --}}
@if(!$userTemplate)
<button wire:click="saveTemplate" class="bg-blue-500 text-white px-4 py-2">
Create Default Template
</button>
@endif

{{-- Show user template edit form --}}
@if($userTemplate)

<div>
<label>Bride Name</label>
<input type="text" wire:model="userTemplate.bride_name" class="border p-1">
</div>

<div>
<label>Groom Name</label>
<input type="text" wire:model="userTemplate.groom_name" class="border p-1">
</div>

<div>
<label>Title</label>
<input type="text" wire:model="userTemplate.title" class="border p-1">
</div>

<div>
<label>Subtitle</label>
<input type="text" wire:model="userTemplate.subtitle" class="border p-1">
</div>

<button wire:click="updateTemplate" class="bg-green-500 text-white px-4 py-2 mt-2">
Update Template
</button>

@endif

{{-- Flash message --}}
@if(session()->has('success'))
<div style="color:green;margin-top:10px">
{{ session('success') }}
</div>
@endif

</div>