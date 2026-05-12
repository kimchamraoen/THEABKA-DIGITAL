<div>
    @if($isGuestView)
        {{-- GUEST VIEW: Show ONLY the wedding form --}}
        <div class="w-full min-h-screen">
            <livewire:components.wedding-form :uuid="$uuid" />
        </div>
    @else
        <div class="max-w-7xl mx-auto p-6">
            @if(session()->has('success'))
                <div class="bg-green-100 text-green-800 px-4 py-2 mb-6 rounded">
                    {{ session('success') }}
                </div>
            @endif
                
            <div class="flex flex-col lg:flex-row gap-8">
                {{-- Left: Edit Form --}}
                <div class="lg:w-2.5/5 bg-white/10 text-gray-200 p-0 rounded-2xl shadow-2xl border border-white/10 overflow-hidden flex flex-col" wire:listen="refresh">
                    <div class="p-6 border-b border-white/20 bg-white/[0.02]">
                        <h2 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">
                            Edit Your Invitation
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Customize every detail of your special day</p>
                    </div>

                    <div>
                        <div class="flex-1 custom-scrollbar px-6 py-4 space-y-8 no-scrollbar overflow-y-scroll h-[100dvh]">
                        
                            {{-- Section: Media & Atmosphere --}}
                            <div class="space-y-4">
                                <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Atmosphere</h3>
                                
                                {{-- Background Music --}}
                                <div class="bg-white/[0.03] p-4 rounded-xl border border-white/5">
                                    <label class="block text-xs font-semibold mb-3 uppercase">Background Music</label>
                                    <select wire:model.live="background_music" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm">
                                        <option value="">-- Select Music --</option>
                                        @foreach($musicFiles as $music)
                                            <option value="{{ $music }}">{{ basename($music) }}</option>
                                        @endforeach
                                    </select>

                                    @php
                                        $musicUrl = null;

                                        if ($background_music instanceof TemporaryUploadedFile) {
                                            $musicUrl = $background_music->temporaryUrl();

                                        } elseif (is_string($background_music)) {
                                            $musicUrl = asset('storage/' . ltrim($background_music, '/'));

                                        } elseif (is_array($background_music)) {
                                            // Fix bad data (array → take first item)
                                            $musicUrl = isset($background_music[0])
                                                ? asset('storage/' . ltrim($background_music[0], '/'))
                                                : null;
                                        }
                                    @endphp

                                    @if($musicUrl)
                                        <div class="mt-4">
                                            <audio 
                                                controls 
                                                autoplay 
                                                wire:key="{{ md5($musicUrl) }}" 
                                                class="w-full"
                                            >
                                                <source src="{{ $musicUrl }}" type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        </div>
                                    @endif
                                </div>

                                {{-- Typography Section --}}
                                <div class="grid grid-cols-1 gap-4 bg-white/[0.03] p-4 rounded-xl border border-white/5">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-400 mb-2 uppercase">Title Font</label>
                                        <select wire:model.live="title_font_family" class="w-full bg-white/10 border border-white/10 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none transition-all no-scrollbar">
                                            @foreach([
                                                'Classic Serif (Elegant)' => ['Playfair Display', 'Lora', 'Cormorant Garamond', 'Libre Baskerville', 'Merriweather', 'PT Serif', 'Crimson Text', 'Zilla Slab', 'Arvo', 'Cardo'],
                                                'Modern Sans (Clean)' => ['Poppins', 'Montserrat', 'Roboto', 'Open Sans', 'Raleway', 'Quicksand', 'Nunito', 'Inter', 'Work Sans', 'Rubik', 'Mulish', 'Barlow', 'Kanit', 'Manrope', 'Heebo'],
                                                'Display & Bold (Impact)' => ['Oswald', 'Anton', 'Bebas Neue', 'Abril Fatface', 'Righteous', 'Titan One', 'Alfa Slab One', 'Bowlby One SC', 'Paytone One', 'Passion One'],
                                                'Script & Handwritten' => ['Great Vibes', 'Dancing Script', 'Pacifico', 'Satisfy', 'Lobster', 'Caveat', 'Shadows Into Light', 'Indie Flower', 'Yellowtail', 'Cookie', 'Sacramento'],
                                                'Retro & Unique' => ['Orbitron', 'Monoton', 'Bungee', 'Creepster', 'Special Elite', 'Press Start 2P', 'Uncial Antiqua', 'Alumni Sans Pinstripe'],
                                                'Monospace (Tech)' => ['Fira Code', 'Inconsolata', 'Source Code Pro', 'JetBrains Mono', 'Roboto Mono', 'Ubuntu Mono']
                                            ] as $category => $fonts)
                                                <optgroup label="--- {{ $category }} ---" class="font-bold bg-white/10">
                                                    @foreach($fonts as $font)
                                                        <option value="{{ $font }}" style="font-family: '{{ $font }}'; font-size: 1.1rem;">
                                                            {{ $font }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-400 mb-2 uppercase">Text Font</label>
                                        <select wire:model.live="text_font_family" class="w-full bg-white/10 border border-white/10 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 outline-none transition-all no-scrollbar">
                                            @foreach([
                                                'Classic Serif (Elegant)' => ['Playfair Display', 'Lora', 'Cormorant Garamond', 'Libre Baskerville', 'Merriweather', 'PT Serif', 'Crimson Text', 'Zilla Slab', 'Arvo', 'Cardo'],
                                                'Modern Sans (Clean)' => ['Poppins', 'Montserrat', 'Roboto', 'Open Sans', 'Raleway', 'Quicksand', 'Nunito', 'Inter', 'Work Sans', 'Rubik', 'Mulish', 'Barlow', 'Kanit', 'Manrope', 'Heebo'],
                                                'Display & Bold (Impact)' => ['Oswald', 'Anton', 'Bebas Neue', 'Abril Fatface', 'Righteous', 'Titan One', 'Alfa Slab One', 'Bowlby One SC', 'Paytone One', 'Passion One'],
                                                'Script & Handwritten' => ['Great Vibes', 'Dancing Script', 'Pacifico', 'Satisfy', 'Lobster', 'Caveat', 'Shadows Into Light', 'Indie Flower', 'Yellowtail', 'Cookie', 'Sacramento'],
                                                'Retro & Unique' => ['Orbitron', 'Monoton', 'Bungee', 'Creepster', 'Special Elite', 'Press Start 2P', 'Uncial Antiqua', 'Alumni Sans Pinstripe'],
                                                'Monospace (Tech)' => ['Fira Code', 'Inconsolata', 'Source Code Pro', 'JetBrains Mono', 'Roboto Mono', 'Ubuntu Mono']
                                            ] as $category => $fonts)
                                                <optgroup label="--- {{ $category }} ---" class="font-bold bg-white/10">
                                                    @foreach($fonts as $font)
                                                        <option value="{{ $font }}" style="font-family: '{{ $font }}'; font-size: 1.1rem;">
                                                            {{ $font }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1">
                                            <label class="block text-[10px] text-gray-500 mb-1 uppercase">Title Color</label>
                                            <div class="flex items-center bg-white/10  border border-white/10 rounded-lg p-1">
                                                <input type="color" wire:model.live="title_color" class="w-8 h-8 rounded bg-transparent border-none cursor-pointer">
                                                <span class="ml-2 text-xs uppercase font-mono">{{ $title_color }}</span>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-[10px] text-gray-500 mb-1 uppercase">Text Color</label>
                                            <div class="flex items-center bg-white/10  border border-white/10 rounded-lg p-1">
                                                <input type="color" wire:model.live="text_color" class="w-8 h-8 rounded bg-transparent border-none cursor-pointer">
                                                <span class="ml-2 text-xs uppercase font-mono">{{ $text_color }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-400 mb-2 uppercase tracking-tighter">Font Size ({{ $text_font_size }}px)</label>
                                        <select wire:model.live="text_font_size" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm">
                                            @foreach([10,12,14,16,18,20,24,28,32,36,40,48,56,64,72,80,90,100,120,140,160,180,200] as $size)
                                                <option value="{{ $size }}">{{ $size }}px</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Section: Content --}}
                            <div class="space-y-4">
                                <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Event Content</h3>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-white/[0.03] p-3 rounded-xl border border-white/5 space-y-2">
                                        <label class="text-[10px] font-bold text-gray-500 uppercase">Bride</label>
                                        <input type="text" wire:model.live="bride_name" class="w-full bg-white/10 border-white/10 rounded-lg p-2 text-sm focus:ring-0 text-white placeholder-gray-600" placeholder="Bride Name">
                                    </div>
                                    <div class="bg-white/[0.03] p-3 rounded-xl border border-white/5 space-y-2">
                                        <label class="text-[10px] font-bold text-gray-500 uppercase">Groom</label>
                                        <input type="text" wire:model.live="groom_name" class="w-full bg-white/10 border-white/10 p-2 rounded-lg text-sm focus:ring-0 text-white placeholder-gray-600" placeholder="Groom Name">
                                    </div>
                                </div>

                                <div class="space-y-3 bg-white/[0.03] p-4 rounded-xl border border-white/5">
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-500 uppercase">Main Title</label>
                                        <input type="text" wire:model.live="title" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm mt-1">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-500 uppercase">Subtitle</label>
                                        <input type="text" wire:model.live="subtitle" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm mt-1">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-[10px] font-bold text-gray-500 uppercase">Time</label>
                                            <input type="text" wire:model.live="event_time" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm mt-1">
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-bold text-gray-500 uppercase">Date</label>
                                            <input type="datetime-local" wire:model="date" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm mt-1 text-white">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-500 uppercase">Address</label>
                                        <input type="text" wire:model.live="address" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm mt-1">
                                    </div>
                                </div>
                            </div>

                            {{-- Section: Messages --}}
                            <div class="grid grid-cols-1 gap-4">
                                <div class="bg-white/[0.03] p-4 rounded-xl border border-white/5 space-y-3">
                                    <h4 class="text-[10px] font-black text-blue-400 uppercase">Invitation Message</h4>
                                    <input type="text" wire:model.live="title_invitation" placeholder="Invitation Title" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm">
                                    <textarea wire:model.live="message_invitation" rows="6" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm"></textarea>
                                </div>
                                
                                <div class="bg-white/[0.03] p-4 rounded-xl border border-white/5 space-y-3">
                                    <h4 class="text-[10px] font-black text-blue-400 uppercase">Thank You Message</h4>
                                    <input type="text" wire:model.live="title_thanks" placeholder="Thanks Title" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm">
                                    <textarea wire:model.live="message_thanks" rows="8" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm"></textarea>
                                </div>
                            </div>

                            {{-- Section: Event --}}
                            <div class="space-y-4">
                                <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Event</h3>
                                    <div class="group bg-white/[0.03] p-6 rounded-2xl border border-white/10 shadow-xl">
                                        <div class="flex justify-between items-center">
                                            <h2 class="text-xl font-bold">Manage Events</h2>
                                            <button wire:click="openModal" class="bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700">
                                                + Add New Event
                                            </button>
                                        </div>

                                        @if (session()->has('message'))
                                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                                                {{ session('message') }}
                                            </div>
                                        @endif

                                        {{-- Popup Modal --}}
                                        @if($isOpen)
                                        <div class="fixed inset-0 flex items-center justify-center z-50">
                                            <div class="absolute inset-0 bg-black opacity-50" wire:click="closeModal"></div>
                                            
                                            <div class="bg-white p-8 rounded-lg shadow-xl z-10 w-full max-w-md">
                                                <div class="space-y-4">
                                                    <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                                        {{ $event_id ? 'Edit Event' : 'Create Event' }}
                                                    </h3>
                                                    
                                                    <div class="grid grid-cols-1 gap-4">
                                                        <input type="text" wire:model="name" placeholder="Event Name" class="border p-2 rounded w-full text-black">
                                                        <input type="datetime-local" wire:model="event_date" class="border p-2 rounded w-full text-black">
                                                        <input type="text" wire:model="title_event" placeholder="Event Title" class="border p-2 rounded w-full text-black">

                                                        <div class="flex justify-end space-x-2 mt-4">
                                                            <button wire:click="closeModal" class="px-4 py-2 text-gray-500 hover:underline">Cancel</button>
                                                            <button wire:click="store" class="bg-blue-600 text-white px-6 py-2 rounded font-bold">
                                                                {{ $event_id ? 'Update' : 'Save Event' }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="overflow-x-auto">
                                            <div class="pt-5 pb-5 ">
                                                <div class="grid gap-6 max-w-2xl mx-auto">

                                                    @forelse($events as $event)
                                                        <div 
                                                            wire:key="event-{{ $event->id }}"
                                                            class="group bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/20 shadow-xl transition-all duration-300"
                                                        >

                                                            <!-- edit & delete -->
                                                            <div class="flex justify-between items-start gap-4 mb-3">
                                                                <!-- ACTION BUTTONS -->
                                                                <div class="flex items-center space-x-1 shrink-0 opacity-60 group-hover:opacity-100 transition-opacity">

                                                                    <button wire:click="edit({{ $event->id }})" class="text-indigo-600 hover:text-indigo-900 mx-2">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>

                                                                    <button wire:click="delete({{ $event->id }})" class="text-red-600 hover:text-red-900 mx-2" onclick="return confirm('Are you sure?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>

                                                                </div>
                                                            </div>

                                                            <!-- CONTENT -->
                                                            <div class="p-4 bg-white/5 rounded-lg border border-white/10">
                                                                <h3 class="text-base font-bold text-white truncate leading-relaxed break-words break-all whitespace-normal w-full" title="{{ $event->name }}">
                                                                    {{ $event->name }}
                                                                </h3><hr class="my-2 border-white/10">

                                                                

                                                                <p class="text-white/80 text-sm leading-relaxed break-words break-all whitespace-normal w-full">
                                                                    {{ $event->title }}
                                                                </p>

                                                                <div class="flex items-center justify-between pt-3 border-t border-white/10 mt-3">

                                                                    <span class="text-xs font-medium text-white/50 bg-white/5 px-3 py-1 rounded-full">
                                                                        <i class="far fa-clock mr-1"></i>
                                                                        {{ \Carbon\Carbon::parse($event->event_time)->format('M j, Y • g:i A') }}
                                                                    </span>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    @empty
                                                        <!-- EMPTY STATE -->
                                                        <div class="bg-white/5 border-2 border-dashed border-white/10 p-10 rounded-2xl text-center">
                                                            <p class="text-white/40 text-sm mb-2">No events found</p>
                                                            <p class="text-white/30 text-xs">Create your first event to get started 🚀</p>
                                                        </div>
                                                    @endforelse

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            {{-- Section: Images --}}
                            <div class="space-y-4">
                                <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Images & Media</h3>
                                
                                {{-- Cover Image --}}
                                <div class="bg-white/[0.03] p-4 rounded-xl border border-white/5">
                                    <label class="block text-xs font-semibold mb-3 uppercase">Cover Image</label>
                                    <div class="flex items-center space-x-4">
                                        @if($cover_image)
                                            <div class="w-40 h-40 rounded-lg overflow-hidden border border-white/10 flex-shrink-0">
                                                <img 
                                                    src="{{ $cover_image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile 
                                                        ? $cover_image->temporaryUrl() 
                                                        : Storage::url($cover_image) }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            </div>
                                        @endif
                                        <div class="flex-1 relative">
                                            <input type="file" wire:model.live="cover_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                            <div class="border-2 border-dashed border-white/10 rounded-lg p-4 text-center text-xs text-gray-500 group-hover:border-blue-500/50 transition-colors">
                                                Click to change photo
                                            </div>
                                        </div>
                                    </div>
                                    <div wire:loading wire:target="cover_image" class="text-[10px] text-blue-400 mt-2">Uploading...</div>
                                    @error('cover_image') <span class="text-red-500 text-[10px] mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Background Image Selector --}}
                                <div class="bg-white/[0.03] p-4 rounded-xl border border-white/5">
                                    <label class="block text-xs font-semibold mb-3 uppercase">Background Image</label>
                                    <select wire:model.live="background_images" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm">
                                        <option value="">-- Select Image or video --</option>
                                        @foreach($imageFiles as $image)
                                            <option value="{{ $image }}">{{ basename($image) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Map Details --}}
                                <div class="bg-white/[0.03] p-4 rounded-xl border border-white/5 space-y-4">
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-500 uppercase">Google Maps Link</label>
                                        <input type="text" wire:model.live="link_map" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm mt-1">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-500 uppercase block mb-2">Map Photo</label>
                                        <div class="flex items-center space-x-4">
                                            @if($map_photo)
                                                <img src="{{ $map_photo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $map_photo->temporaryUrl(): Storage::url($map_photo) }}" class="w-40 h-40 rounded object-cover">
                                            @endif
                                            <input type="file" wire:model="map_photo" class="text-xs border-lg border-white/10 p-3 text-gray-500 file:bg-blue-500/10 file:text-blue-400 file:border-none file:px-3 file:py-1 file:rounded-full file:mr-4 cursor-pointer">
                                        </div>
                                    </div>
                                </div>

                                {{-- Financial QRs --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-white/[0.03] p-4 rounded-xl border border-white/5">
                                        <label class="text-[10px] font-bold text-gray-500 uppercase block mb-2">Dollar QR</label>
                                        <div class="mb-3">
                                            <!-- <label class="text-[10px] font-bold text-gray-500 uppercase">Google Maps Link</label> -->
                                            <input type="text" wire:model.live="link_dollar" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm mt-1">
                                        </div>

                                        @if($dollar_qr)
                                            <img src="{{ $dollar_qr instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $dollar_qr->temporaryUrl() : Storage::url($dollar_qr) }}" class="w-56 aspect-square object-cover rounded mb-2">
                                        @endif
                                        <input type="file" wire:model="dollar_qr" class="w-full text-[8px]">
                                    </div>
                                    <div class="bg-white/[0.03] p-4 rounded-xl border border-white/5">
                                        <label class="text-[10px] font-bold text-gray-500 uppercase block mb-2">Khmer QR</label>
                                        <div class="mb-3">
                                            <!-- <label class="text-[10px] font-bold text-gray-500 uppercase">Google Maps Link</label> -->
                                            <input type="text" wire:model.live="link_khmer" class="w-full bg-white/10 border border-white/10 rounded-lg p-2 text-sm mt-1">
                                        </div>

                                        @if($khmer_qr)
                                            <img src="{{ $khmer_qr instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $khmer_qr->temporaryUrl() : Storage::url($khmer_qr) }}" class="w-56 aspect-square object-cover rounded mb-2">
                                        @endif
                                        <input type="file" wire:model="khmer_qr" class="w-full text-[8px]">
                                    </div>
                                </div>

                                {{-- Pre-Wedding Gallery --}}
                                <div class="space-y-4">
                                    <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Pre-Wedding Gallery</h3>
                                    <div class="grid grid-cols-2 gap-3">
                                        @for($i = 1; $i <= 4; $i++)
                                            @php $photo = ${"pre_wedding$i"}; @endphp
                                            <div class="bg-white/[0.03] p-2 rounded-xl border border-white/5 flex flex-col items-center">
                                                @if($photo)
                                                    <img src="{{ $photo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $photo->temporaryUrl() : Storage::url($photo) }}" class="w-56 object-cover rounded-lg mb-2">
                                                @else
                                                    <div class="w-40 bg-black/20 rounded-lg mb-2 flex items-center justify-center text-[10px] text-gray-600 italic">Photo {{ $i }}</div>
                                                @endif
                                                <input type="file" wire:model="pre_wedding{{ $i }}" class="w-40 text-[8px]">
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                {{-- Right: Live Preview --}}
                <div class="lg:w-2.5/5 bg-white/10 text-gray-200 p-0 rounded-2xl shadow-2xl border border-white/10 overflow-hidden flex flex-col" wire:listen="refresh">
                    {{-- header --}}
                    <div class="p-6 border-b border-white/5 bg-white/[0.02]">
                        <div class="flex justify-between items-center backdrop-blur-md">
                            <h2 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">Live Preview</h2>
                            
                            <button 
                                wire:click="saveTemplate" 
                                wire:loading.attr="disabled"
                                class="relative flex items-center gap-2 bg-blue-500 text-white px-8 py-2.5 rounded-full hover:bg-blue-600 hover:shadow-blue-300 active:scale-95 transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed"
                            >
                                <span wire:loading.remove wire:target="saveTemplate">
                                    Save Template
                                </span>

                                <span wire:loading wire:target="saveTemplate" class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>

                            <!-- CONNECT TELEGRAM BUTTON -->
                            <a href="/telegram/connect/{{ $template->id }}"
                            class="bg-blue-500 text-white px-4 py-2 rounded">
                                Connect Telegram
                            </a>
                        </div>
                    </div>
                    {{-- body --}}
                    <div>
                        
                        @php
                            $url = null;
                            $isVideo = false;

                            if ($background_images instanceof TemporaryUploadedFile) {
                                // Upload preview
                                $filename = $background_images->getClientOriginalName();
                                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
                                $url = $background_images->temporaryUrl();

                            } elseif (is_string($background_images)) {
                                // Stored file
                                $filename = $background_images;
                                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
                                $url = asset('storage/' . ltrim($background_images, '/'));

                            } elseif (is_array($background_images)) {
                                // Fix bad data (array → take first item)
                                $file = $background_images[0] ?? null;

                                if ($file) {
                                    $filename = $file;
                                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
                                    $url = asset('storage/' . ltrim($file, '/'));
                                }
                            }
                        @endphp

                        <div class="relative w-full h-[100dvh] flex justify-center">

                            {{-- background Image --}}
                            <div class="absolute inset-0 z-0 overflow-hidden" wire:key="main-container-{{ md5($url ?? 'empty') }}">
                                @if($url)
                                    @if($isVideo)
                                        {{-- Keyed specifically for main background --}}
                                        <video 
                                            autoplay 
                                            muted 
                                            loop 
                                            playsinline 
                                            preload="auto"
                                            class="w-full h-full object-cover"
                                            wire:key="v-main-{{ md5($url) }}">
                                            <source src="{{ $url }}#t=0.1" type="video/{{ $extension == 'mov' ? 'quicktime' : $extension }}">
                                        </video>
                                    @else
                                        <div 
                                            class="w-full h-full bg-cover bg-center" 
                                            style="background-image: url('{{ $url }}');"
                                            wire:key="i-main-{{ md5($url) }}">
                                        </div>
                                    @endif
                                @endif
                            </div>

                            {{-- ================= CONTENT LAYER ================= --}}
                            <div class="relative z-10 w-full h-full overflow-y-auto no-scrollbar">

                                {{-- COVER SECTION --}}
                                <div class="relative w-full h-[100dvh] overflow-hidden">
                                    <img 
                                        src="{{ $cover_image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile 
                                            ? $cover_image->temporaryUrl() 
                                            : Storage::url($cover_image) }}"
                                    />

                                    <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/60"></div>

                                    <div class="absolute inset-0 z-10 flex flex-col items-center justify-between py-16 px-6 text-white">
                                        
                                        <div class="space-y-2 animate-fade-in-down">
                                            <h3 class="text-xl text-center md:text-2xl tracking-[0.3em] uppercase font-light opacity-90" 
                                                style="font-family: {{ $title_font_family }}; color: {{ $title_color }};">
                                                {{ $title }}
                                            </h3>
                                            <!-- <div class="h-[1px] w-24 bg-white/50 mx-auto"></div> -->
                                            <div class="flex items-center justify-center opacity-70">
                                                <svg width="600" height="12" viewBox="0 0 600 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full max-w-2xl text-current">
                                                    <circle cx="15" cy="20" r="4" fill="currentColor"/>
                                                    <line x1="25" y1="20" x2="200" y2="20" stroke="currentColor" stroke-width="2"/>
                                                    
                                                    <circle cx="210" cy="20" r="2.5" fill="currentColor"/>
                                                    <circle cx="220" cy="20" r="3.5" fill="currentColor"/>
                                                    <circle cx="235" cy="20" r="6" fill="currentColor"/>

                                                    <g transform="translate(300, 20)">
                                                        <circle cx="0" cy="0" r="3" fill="currentColor"/>
                                                        @foreach(range(0, 315, 45) as $angle)
                                                            <ellipse transform="rotate({{ $angle }})" cx="18" cy="0" rx="10" ry="2" fill="currentColor"/>
                                                        @endforeach
                                                    </g>

                                                    <circle cx="365" cy="20" r="6" fill="currentColor"/>
                                                    <circle cx="380" cy="20" r="3.5" fill="currentColor"/>
                                                    <circle cx="390" cy="20" r="2.5" fill="currentColor"/>

                                                    <line x1="400" y1="20" x2="575" y2="20" stroke="currentColor" stroke-width="2"/>
                                                    <circle cx="585" cy="20" r="4" fill="currentColor"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="flex flex-col items-center gap-4">
                                            <h1 class="tracking-tight"
                                                style="font-family: {{ $title_font_family }}; color: {{ $title_color }}; font-size: {{ $text_font_size }}px">
                                                {{ $groom_name }}
                                            </h1>
                                            
                                            <div class="relative flex items-center justify-center w-full">
                                                <div class="h-[1px] w-20 bg-white/30"></div>
                                                <span class="mx-4 text-2xl italic font-light serif opacity-80">&</span>
                                                <div class="h-[1px] w-20 bg-white/30"></div>
                                            </div>

                                            <h1 class=" tracking-tight"
                                                style="font-family: {{ $title_font_family }}; color: {{ $title_color }};  font-size: {{ $text_font_size }}px">
                                                {{ $bride_name }}
                                            </h1>
                                        </div>

                                        <div class="w-full max-w-sm space-y-8 animate-fade-in-up">
                                            
                                            <div class="relative group">
                                                <p class="text-xs uppercase tracking-widest mb-3 opacity-70 font-medium" style="font-family: {{ $title_font_family }}; color: {{ $title_color }};" >Dear Honorable Guest</p>
                                                <div class="rounded-2xl py-5 px-6 backdrop-blur-xl bg-white/10 border border-white/20 shadow-2xl transition-all duration-500 hover:bg-white/15">
                                                    <p class="text-xl text-center md:text-2xl font-semibold tracking-wide" style="font-family: {{ $title_font_family }}; ">
                                                        {{ $isGuestView ? $guest_name : 'Guest Name' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="space-y-1" style="font-family: {{ $title_font_family }}; color: {{ $title_color }};">
                                                <p class="text-lg md:text-xl font-medium tracking-wide">
                                                    {{ $subtitle }}
                                                </p>
                                                <p class="text-sm md:text-base opacity-80 tracking-widest uppercase">
                                                    {{ $event_time }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ================= REST OF SECTIONS ================= --}}
                                <div class="space-y-20 py-16 px-16" style="font-family: {{ $text_font_family }}; color: {{ $text_color }}">
                                    <section class=" text-center max-w-2xl mx-auto">
                                        <h2 class="text-2xl md:text-3xl mb-1 tracking-tight">
                                            {{ $title_invitation }}
                                        </h2>

                                        <div class="flex items-center justify-center mb-10 opacity-60">
                                                                <svg width="600" height="10" viewBox="0 0 600 30" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="w-full max-w-2xl text-current">
                                                                    <path d="M10 15C10 15 25 10 35 10C40 10 45 12 45 15C45 18 40 20 35 20C25 20 10 15 10 15Z" />
                                                                    
                                                                    <line x1="45" y1="15" x2="265" y2="15" stroke="currentColor" stroke-width="1.5"/>
                                                                    <line x1="335" y1="15" x2="555" y2="15" stroke="currentColor" stroke-width="1.5"/>

                                                                    <rect x="294" y="9" width="12" height="12" transform="rotate(45 300 15)" /> <rect x="272" y="11" width="8" height="8" transform="rotate(45 276 15)" />   <rect x="320" y="11" width="8" height="8" transform="rotate(45 324 15)" />   <path d="M590 15C590 15 575 10 565 10C560 10 555 12 555 15C555 18 560 20 565 20C575 20 590 15 590 15Z" />
                                                                </svg>
                                        </div>

                                        <!-- <div class="h-[1px] w-20 text-white bg-current opacity-20 mx-auto mb-6"></div> -->
                                        <p class="text-md md:text-lg opacity-80 leading-relaxed font-light italic">
                                            {{ $message_invitation }}
                                        </p>
                                    </section>

                                    {{-- countdown timer --}}
                                    <section class=" text-center">
                                        <h2 class="text-sm uppercase tracking-[0.4em] mb-12 opacity-60 text-center">The Celebration Begins In</h2>

                                        <div class="flex flex-wrap justify-center gap-4 md:gap-8 text-center" wire:ignore>
                                            @foreach(['days' => 'Days', 'hours' => 'Hours', 'minutes' => 'Minutes', 'seconds' => 'Seconds'] as $id => $label)
                                                <div class="bg-white/10 backdrop-blur-xl p-6 md:p-8 rounded-2xl border border-white/20 shadow-xl min-w-[100px] md:min-w-[130px] transition-transform hover:scale-105">
                                                    <p class="text-3xl md:text-4xl font-bold tracking-tighter" id="{{ $id }}">00</p>
                                                    <span class="text-[10px] uppercase tracking-widest opacity-60">{{ $label }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <p class="mt-10 text-sm tracking-[0.2em] opacity-70 uppercase pt-8 border-t border-white/10 inline-block">
                                            {{ $event_time }}
                                        </p>
                                    </section>

                                    <section class="text-center max-w-2xl mx-auto">
                                        <h2 class="text-3xl mb-1 tracking-wide">Event Schedule</h2>
                                        <div class="flex items-center justify-center mb-5 opacity-60">
                                                                <svg width="600" height="10" viewBox="0 0 600 30" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="w-full max-w-2xl text-current">
                                                                    <path d="M10 15C10 15 25 10 35 10C40 10 45 12 45 15C45 18 40 20 35 20C25 20 10 15 10 15Z" />
                                                                    
                                                                    <line x1="45" y1="15" x2="265" y2="15" stroke="currentColor" stroke-width="1.5"/>
                                                                    <line x1="335" y1="15" x2="555" y2="15" stroke="currentColor" stroke-width="1.5"/>

                                                                    <rect x="294" y="9" width="12" height="12" transform="rotate(45 300 15)" /> <rect x="272" y="11" width="8" height="8" transform="rotate(45 276 15)" />   <rect x="320" y="11" width="8" height="8" transform="rotate(45 324 15)" />   <path d="M590 15C590 15 575 10 565 10C560 10 555 12 555 15C555 18 560 20 565 20C575 20 590 15 590 15Z" />
                                                                </svg>
                                        </div>
                                        <div class="max-w-2xl mx-auto space-y-6">
                                            @forelse($events as $event)
                                                <div class="p-4 bg-white/50 rounded-lg border border-white/10">
                                                                <h3 class="text-base font-bold text-white truncate leading-relaxed break-words break-all whitespace-normal w-full" title="{{ $event->name }}" style="color: {{ $text_color }};">
                                                                    {{ $event->name }}
                                                                </h3><hr class="my-2 border-white/10">

                                                                <p class="text-white/80 text-sm leading-relaxed break-words break-all whitespace-normal w-full" style="color: {{ $text_color }};">
                                                                    {{ $event->title }}
                                                                </p>

                                                                <div class="flex items-center justify-between pt-3 border-t border-white/10 mt-3">

                                                                    <span class="text-xs font-medium text-white/50 bg-white/5 px-3 py-1 rounded-full" style="color: {{ $text_color }};">
                                                                        <i class="far fa-clock mr-1"></i>
                                                                        {{ \Carbon\Carbon::parse($event->event_time)->format('M j, Y • g:i A') }}
                                                                    </span>
                                                                </div>
                                                </div>
                                            @empty
                                                <div class="bg-white/5 backdrop-blur-xl p-12 rounded-3xl border-2 border-dashed border-white/10 text-center" style="color: {{ $text_color }};">
                                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/5 mb-4">
                                                        <i class="fas fa-calendar-day text-white/20 text-2xl"></i>
                                                    </div>
                                                    <h4 class="text-white font-medium">No events scheduled</h4>
                                                    <p class="text-white/40 text-sm mt-1">Check back later for new updates.</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </section>

                                    {{-- location & map --}}
                                    <section class=" text-center px-6 max-w-3xl mx-auto">
                                                            <h2 class="text-3xl mb-1 tracking-wide">Location</h2>

                                                            <div class="flex items-center justify-center mb-5 opacity-60">
                                                                <svg width="600" height="10" viewBox="0 0 600 30" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="w-full max-w-2xl text-current">
                                                                    <path d="M10 15C10 15 25 10 35 10C40 10 45 12 45 15C45 18 40 20 35 20C25 20 10 15 10 15Z" />
                                                                    
                                                                    <line x1="45" y1="15" x2="265" y2="15" stroke="currentColor" stroke-width="1.5"/>
                                                                    <line x1="335" y1="15" x2="555" y2="15" stroke="currentColor" stroke-width="1.5"/>

                                                                    <rect x="294" y="9" width="12" height="12" transform="rotate(45 300 15)" /> <rect x="272" y="11" width="8" height="8" transform="rotate(45 276 15)" />   <rect x="320" y="11" width="8" height="8" transform="rotate(45 324 15)" />   <path d="M590 15C590 15 575 10 565 10C560 10 555 12 555 15C555 18 560 20 565 20C575 20 590 15 590 15Z" />
                                                                </svg>
                                                            </div>

                                                            <p class="mb-10 opacity-80 text-lg font-light leading-relaxed italic">
                                                                <i class="fa-solid fa-location-dot mr-2"></i> {{ $address }}
                                                            </p>

                                                            <div class="group overflow-hidden rounded-[2rem] shadow-2xl border border-white/20">
                                                                
                                                                <img src="{{ $map_photo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $map_photo->temporaryUrl(): Storage::url($map_photo) }}" alt="Map Photo" class="w-full h-72 md:h-96 object-cover transition-transform duration-700 group-hover:scale-110">
                                                                
                                                            </div>
                                                            <div class=" inset-0 group-hover:bg-black/40 transition-colors flex items-center justify-center mt-3">
                                                                    <a href="{{ $link_map }}" target="_blank"
                                                                    class="px-8 py-3 bg-white/20 rounded-full font-semibold shadow-2xl transform transition hover:scale-110 active:scale-95">
                                                                        Open Google Maps
                                                                    </a>
                                                            </div>
                                    </section>

                                    {{-- pre-wedding gallery --}}
                                    <section class=" text-center px-6">
                                        <h2 class="text-3xl mb-3">Our Moments</h2>

                                        <div class="flex items-center justify-center mb-8 opacity-60">
                                                                <svg width="600" height="10" viewBox="0 0 600 30" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="w-40 max-w-2xl text-current">
                                                                    <path d="M10 15C10 15 25 10 35 10C40 10 45 12 45 15C45 18 40 20 35 20C25 20 10 15 10 15Z" />
                                                                    
                                                                    <line x1="45" y1="15" x2="265" y2="15" stroke="currentColor" stroke-width="1.5"/>
                                                                    <line x1="335" y1="15" x2="555" y2="15" stroke="currentColor" stroke-width="1.5"/>

                                                                    <rect x="294" y="9" width="12" height="12" transform="rotate(45 300 15)" /> <rect x="272" y="11" width="8" height="8" transform="rotate(45 276 15)" />   <rect x="320" y="11" width="8" height="8" transform="rotate(45 324 15)" />   <path d="M590 15C590 15 575 10 565 10C560 10 555 12 555 15C555 18 560 20 565 20C575 20 590 15 590 15Z" />
                                                                </svg>
                                        </div>

                                        <div class="columns-2 md:columns-2 gap-4 min-w-40 mx-auto space-y-4">
                                            @foreach([$pre_wedding1, $pre_wedding2, $pre_wedding3, $pre_wedding4] as $img)
                                                <div class="overflow-hidden rounded-2xl border border-white/10 shadow-lg group">
                                                    <img src="{{ $img instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $img->temporaryUrl() : Storage::url($img) }}" 
                                                        class="w-60 object-cover transform transition-transform duration-500 group-hover:scale-110">
                                                </div>
                                            @endforeach
                                        </div>
                                    </section>

                                    {{-- thanks message --}}
                                    <section class=" text-center px-6 max-w-2xl mx-auto">
                                        <h2 class="text-2xl  mb-3">{{ $title_thanks }}</h2>

                                        <div class="flex items-center justify-center mb-8 opacity-60">
                                                                <svg width="600" height="10" viewBox="0 0 600 30" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="w-full max-w-2xl text-current">
                                                                    <path d="M10 15C10 15 25 10 35 10C40 10 45 12 45 15C45 18 40 20 35 20C25 20 10 15 10 15Z" />
                                                                    
                                                                    <line x1="45" y1="15" x2="265" y2="15" stroke="currentColor" stroke-width="1.5"/>
                                                                    <line x1="335" y1="15" x2="555" y2="15" stroke="currentColor" stroke-width="1.5"/>

                                                                    <rect x="294" y="9" width="12" height="12" transform="rotate(45 300 15)" /> <rect x="272" y="11" width="8" height="8" transform="rotate(45 276 15)" />   <rect x="320" y="11" width="8" height="8" transform="rotate(45 324 15)" />   <path d="M590 15C590 15 575 10 565 10C560 10 555 12 555 15C555 18 560 20 565 20C575 20 590 15 590 15Z" />
                                                                </svg>
                                        </div>

                                        <p class="text-md opacity-80 leading-relaxed font-light">
                                            {{ $message_thanks }}
                                        </p>
                                    </section>

                                    {{-- qr --}}
                                    <section class=" text-center">
                                        <h2 class="text-lg uppercase tracking-[0.4em] mb-12 opacity-60">Wedding Gift</h2>

                                        <div class="justify-center columns-1 md:columns-2 gap-4 max-w-4xl mx-auto space-y-4">
                                            <div class="bg-white/10 backdrop-blur-md p-8 rounded-[2rem] border border-white/20 shadow-xl transition-all hover:bg-white/20">
                                                <p class="mb-4 text-xs uppercase tracking-widest opacity-60">Digital Gift (USD)</p>
                                                <!-- <div class="bg-white p-3 rounded-2xl"> -->
                                                <img src="{{ $dollar_qr instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $dollar_qr->temporaryUrl(): Storage::url($dollar_qr) }}" class="w-40 h-40 object-contain mx-auto" alt="USD QR">
                                                <!-- </div> -->
                                                <div class=" inset-0 group-hover:bg-black/40 transition-colors flex items-center justify-center mt-3">
                                                    <a href="{{ $link_dollar }}" target="_blank"
                                                        class="px-8 py-3 bg-white/10 rounded-full font-semibold shadow-2xl transform transition hover:scale-110 active:scale-95">
                                                        Pay via link
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="bg-white/10 backdrop-blur-md p-8 rounded-[2rem] border border-white/20 shadow-xl transition-all hover:bg-white/20">
                                                <p class="mb-4 text-xs uppercase tracking-widest opacity-60">Digital Gift (KHR)</p>
                                                <!-- <div class="bg-white p-3 rounded-2xl"> -->
                                                    <img src="{{ $khmer_qr instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $khmer_qr->temporaryUrl(): Storage::url($khmer_qr) }}" class="w-40 h-40 object-contain mx-auto" alt="KHR QR">
                                                <!-- </div> -->
                                                 <div class=" inset-0 group-hover:bg-black/40 transition-colors flex items-center justify-center mt-3">
                                                    <a href="{{ $link_khmer }}" target="_blank"
                                                        class="px-8 py-3 bg-white/10 rounded-full font-semibold shadow-2xl transform transition hover:scale-110 active:scale-95">
                                                        Pay via link
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <section class=" text-center px-6 max-w-2xl mx-auto">
                                        <div class="bg-white/10 backdrop-blur-lg rounded-[2.5rem] p-10 shadow-2xl border border-white/20">
                                            <h2 class="text-3xl mb-8 tracking-tight">Messages & Blessings</h2>

                                            <div class="space-y-6">
                                                <!-- <div class="bg-white/50 p-6 rounded-2xl border border-white text-left transition hover:bg-white/10">
                                                
                                                </div> -->
                                              
                                                <div class="py-5 border-t border-white/10">
                                                    <p class="opacity-40 text-xs tracking-widest uppercase">
                                                        Messages and blessings will appear here ❤️
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    {{-- social media --}}
                                    <section class=" text-center">
                                        <h2 class="text-xs uppercase tracking-[0.6em] mb-5 opacity-50 font-bold">Stay Connected</h2>

                                        <div class="flex flex-wrap justify-center gap-6 md:gap-10">
                                    
                                            <a href="#" target="_blank" 
                                            class=" group flex items-center justify-center w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg transition-all duration-300 hover:bg-[#1877F2] hover:scale-110 hover:border-transparent">
                                                <i class="fa-brands fa-facebook-f text-xl opacity-70 group-hover:opacity-100 group-hover:text-white transition-all"></i>
                                            </a>

                                            <a href="#" target="_blank" 
                                            class=" group flex items-center justify-center w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg transition-all duration-300 hover:bg-gradient-to-tr hover:from-[#f9ce34] hover:via-[#ee2a7b] hover:to-[#6228d7] hover:scale-110 hover:border-transparent">
                                                <i class="fa-brands fa-instagram text-2xl opacity-70 group-hover:opacity-100 group-hover:text-white transition-all"></i>
                                            </a>

                                            <a href="#" target="_blank" 
                                            class=" group flex items-center justify-center w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg transition-all duration-300 hover:bg-[#26A5E4] hover:scale-110 hover:border-transparent">
                                                <i class="fa-brands fa-telegram text-2xl opacity-70 group-hover:opacity-100 group-hover:text-white transition-all"></i>
                                            </a>

                                            <a href="#" 
                                            class=" group flex items-center justify-center w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg transition-all duration-300 hover:bg-[#4ADE80] hover:scale-110 hover:border-transparent">
                                                <i class="fa-solid fa-phone text-xl opacity-70 group-hover:opacity-100 group-hover:text-white transition-all"></i>
                                            </a>

                                        </div>
                                    </section>

                                    <div class="flex items-center justify-center opacity-60">
                                        <svg width="600" height="60" viewBox="0 0 600 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full max-w-xl">
                                            <g stroke="currentColor" stroke-width="1.2" stroke-linecap="round" fill="none">
                                                <path d="M300 30C300 30 290 15 260 20M260 20C240 23 230 40 210 40M210 40C200 40 190 35 185 30M185 30C180 25 185 20 190 20C195 20 200 25 200 30" />
                                                <path d="M255 21C255 21 245 35 220 30M220 30C200 25 195 15 205 15C215 15 220 22 220 30" stroke-width="0.8"/>
                                                
                                                <path d="M300 30C300 30 310 15 340 20M340 20C360 23 370 40 390 40M390 40C400 40 410 35 415 30M415 30C420 25 415 20 410 20C405 20 400 25 400 30" />
                                                <path d="M345 21C345 21 355 35 380 30M380 30C400 25 405 15 395 15C385 15 380 22 380 30" stroke-width="0.8"/>
                                            </g>
                                            
                                            <line x1="184" y1="30" x2="30" y2="30" stroke="currentColor" stroke-width="1"/>
                                            <line x1="416" y1="30" x2="570" y2="30" stroke="currentColor" stroke-width="1"/>
                                            
                                            <path d="M25 30C25 30 20 25 15 25C10 25 5 28 5 30C5 32 10 35 15 35C20 35 25 30 25 30Z" fill="none" stroke="currentColor" stroke-width="1.2"/>
                                            <circle cx="28" cy="30" r="1.5" fill="currentColor"/>
                                            
                                            <path d="M575 30C575 30 580 25 585 25C590 25 595 28 595 30C595 32 590 35 585 35C580 35 575 30 575 30Z" fill="none" stroke="currentColor" stroke-width="1.2"/>
                                            <circle cx="572" cy="30" r="1.5" fill="currentColor"/>
                                        </svg>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        console.log("Timer Started"); // If you don't see this in F12, the script isn't loading

        let countdownDate = new Date(@js($this->date)).getTime();

        // Listen for the update event
        Livewire.on('updatedDate', () => {
            countdownDate = new Date(@this.date).getTime();
            console.log("Date updated to: " + @this.date);
        });

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = countdownDate - now;

            if (distance < 0 || isNaN(distance)) {
                document.getElementById("days").innerText = "00";
                document.getElementById("hours").innerText = "00";
                document.getElementById("minutes").innerText = "00";
                document.getElementById("seconds").innerText = "00";
                return;
            }

            const d = Math.floor(distance / (1000 * 60 * 60 * 24));
            const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("days").innerText = String(d).padStart(2, '0');
            document.getElementById("hours").innerText = String(h).padStart(2, '0');
            document.getElementById("minutes").innerText = String(m).padStart(2, '0');
            document.getElementById("seconds").innerText = String(s).padStart(2, '0');
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    });
</script>
@endpush