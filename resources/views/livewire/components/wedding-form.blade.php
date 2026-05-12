<div>
    <div wire:ignore>
        <audio id="bg-music" loop autoplay >
            <source src="{{ asset('storage/' . $music) }}" type="audio/mpeg">
        </audio>
    </div>
    <div>
        @php
            // Ensure $background_images exists and convert it to an array for the loop
            // Replace '$background_images' with whatever variable name your component uses
            $files = is_array($background_images) ? $background_images : [$background_images];
        @endphp
        <div class="relative max-w-xl h-[100dvh] flex justify-center">
            <div class="flex justify-center items-center">
                <div class="absolute inset-0 -z-10 overflow-hidden">
                    @foreach($files as $filePath)
                        @php
                            $filePath = $filePath ?: 'background.jpg';
                            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                            $isVideo = in_array(strtolower($extension), ['mp4', 'webm', 'ogg', 'mov']);
                            $cleanPath = 'storage/' . ltrim($filePath, '/');
                        @endphp

                        @if($isVideo)
                            <video
                                autoplay
                                muted
                                loop
                                playsinline
                                class="absolute inset-0 w-full h-full object-cover"
                            >
                                <source src="{{ asset($cleanPath) }}" type="video/{{ $extension }}">
                            </video>
                        @else
                            <div
                                class="absolute inset-0 w-full h-full bg-cover bg-center"
                                style="background-image: url('{{ asset($cleanPath) }}');"
                            ></div>
                        @endif
                    @endforeach
                </div>

                <div class="relative z-10 h-full overflow-y-auto no-scrollbar">
                    <div class="relative  h-[100dvh] overflow-hidden">
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
                                <h1 class="font-serif tracking-tight"
                                    style="font-family: {{ $title_font_family }}; color: {{ $title_color }}; text: {{ $text_font_size }}">
                                    {{ $groom_name }}
                                </h1>
                                
                                <div class="relative flex items-center justify-center w-full">
                                    <div class="h-[1px] w-20 bg-white/30"></div>
                                    <span class="mx-4 text-2xl italic font-light serif opacity-80">&</span>
                                    <div class="h-[1px] w-20 bg-white/30"></div>
                                </div>

                                <h1 class="font-serif tracking-tight"
                                    style="font-family: {{ $title_font_family }}; color: {{ $title_color }};  text: {{ $text_font_size }}">
                                    {{ $bride_name }}
                                </h1>
                            </div>

                            <div>
                                <span class="flex justify-center items-center border border-sm p-3 bg-white/50 rounded-lg text-black/50">Please Touch here!</span>
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

                    <div class="space-y-20 py-16 px-16" style="font-family: {{ $text_font_family }}; color: {{ $text_color }}">
                        <section class="reveal text-center max-w-2xl mx-auto">
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

                        <section class="reveal text-center">
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
                                            @forelse($this->template?->events ?? [] as $event)
                                                <div class="group relative bg-white/10 backdrop-blur-xl p-6 rounded-3xl border border-white/20 shadow-2xl transition-all duration-500 hover:bg-white/15 hover:-translate-y-1">
                                                    
                                                    <div class="absolute top-0 right-0 -mr-2 -mt-2 w-24 h-24 bg-white/5 blur-3xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>

                                                    <div class="flex items-start gap-5">
                                                        <div class="hidden sm:flex flex-col items-center justify-center bg-white/10 rounded-2xl p-3 min-w-[70px] border border-white/10 shadow-inner">
                                                            <span class="text-xs uppercase font-black  tracking-tighter">
                                                                {{ \Carbon\Carbon::parse($event->event_time)->format('M') }}
                                                            </span>
                                                            <span class="text-2xl font-bold ">
                                                                {{ \Carbon\Carbon::parse($event->event_time)->format('d') }}
                                                            </span>
                                                        </div>

                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="h-1.5 w-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                                                                <p class="text-[12px] font-bold uppercase tracking-[0.2em] ">{{ $event->name }}</p>
                                                            </div>

                                                            <h3 class="text-sm font-extrabold tracking-tight mb-3 break-words" style="color: {{ $title_color }};">
                                                                {{ $event->title }}
                                                            </h3>

                                                            <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-white/5">
                                                                <div class="flex items-center text-sm ">
                                                                    <i class="far fa-clock mr-2 text-indigo-400"></i>
                                                                    {{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}
                                                                </div>
                                                                <div class="flex items-center text-sm ">
                                                                    <i class="far fa-calendar-alt mr-2 text-indigo-400"></i>
                                                                    {{ \Carbon\Carbon::parse($event->event_time)->format('F j, Y') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="bg-white/5 backdrop-blur-xl p-12 rounded-3xl border-2 border-dashed border-white/10 text-center">
                                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/5 mb-4">
                                                        <i class="fas fa-calendar-day text-white/20 text-2xl"></i>
                                                    </div>
                                                    <h4 class="text-white font-medium">No events scheduled</h4>
                                                    <p class="text-white/40 text-sm mt-1">Check back later for new updates.</p>
                                                </div>
                                            @endforelse
                                        </div>
                        </section>

                        <section class="reveal text-center px-6 max-w-3xl mx-auto">
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

                        <section class="reveal text-center px-6">
                            <h2 class="text-3xl mb-3">Our Moments</h2>

                            <div class="flex items-center justify-center mb-8 opacity-60">
                                                    <svg width="600" height="10" viewBox="0 0 600 30" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="w-full max-w-2xl text-current">
                                                        <path d="M10 15C10 15 25 10 35 10C40 10 45 12 45 15C45 18 40 20 35 20C25 20 10 15 10 15Z" />
                                                        
                                                        <line x1="45" y1="15" x2="265" y2="15" stroke="currentColor" stroke-width="1.5"/>
                                                        <line x1="335" y1="15" x2="555" y2="15" stroke="currentColor" stroke-width="1.5"/>

                                                        <rect x="294" y="9" width="12" height="12" transform="rotate(45 300 15)" /> <rect x="272" y="11" width="8" height="8" transform="rotate(45 276 15)" />   <rect x="320" y="11" width="8" height="8" transform="rotate(45 324 15)" />   <path d="M590 15C590 15 575 10 565 10C560 10 555 12 555 15C555 18 560 20 565 20C575 20 590 15 590 15Z" />
                                                    </svg>
                            </div>

                            <div class="columns-2 md:columns-2 gap-4 max-w-4xl mx-auto space-y-4">
                                @foreach([$pre_wedding1, $pre_wedding2, $pre_wedding3, $pre_wedding4] as $img)
                                                <div class="overflow-hidden rounded-2xl border border-white/10 shadow-lg group">
                                                    <img src="{{ $img instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $img->temporaryUrl() : Storage::url($img) }}" 
                                                        class="w-60 object-cover transform transition-transform duration-500 group-hover:scale-110">
                                                </div>
                                @endforeach
                            </div>
                        </section>

                        <section class="reveal text-center px-6 max-w-2xl mx-auto">
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

                        <section class="reveal text-center">
                            <h2 class="text-lg uppercase tracking-[0.4em] mb-12 opacity-60">Wedding Gift</h2>

                            <div class="justify-center columns-1 md:columns-2 gap-4 max-w-4xl mx-auto space-y-4">
                                <div class="bg-white/10 backdrop-blur-md p-8 rounded-[2rem] border border-white/20 shadow-xl transition-all hover:bg-white/20">
                                    <p class="mb-4 text-xs uppercase tracking-widest opacity-60">Digital Gift (USD)</p>
                                    <!-- <div class="bg-white p-3 rounded-2xl"> -->
                                        <img src="{{ $dollar_qr instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $dollar_qr->temporaryUrl(): Storage::url($dollar_qr) }}" class="w-40 h-40 object-contain mx-auto" alt="USD QR">
                                    <!-- </div> -->
                                </div>

                                <div class="bg-white/10 backdrop-blur-md p-8 rounded-[2rem] border border-white/20 shadow-xl transition-all hover:bg-white/20">
                                    <p class="mb-4 text-xs uppercase tracking-widest opacity-60">Digital Gift (KHR)</p>
                                    <!-- <div class="bg-white p-3 rounded-2xl"> -->
                                        <img src="{{ $khmer_qr instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $khmer_qr->temporaryUrl(): Storage::url($khmer_qr) }}" class="w-40 h-40 object-contain mx-auto" alt="KHR QR">
                                    <!-- </div> -->
                                </div>
                            </div>
                        </section>

                        {{-- Guest Message Form --}}
                        <section class="text-center px-6 max-w-2xl mx-auto mt-12">
                            <div class="bg-white/10 backdrop-blur-lg rounded-[2.5rem] p-10 shadow-2xl border border-white/20">
                                
                                @if (session()->has('success'))
                                    <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-2xl text-green-400">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <h2 class="text-3xl font-serif mb-8 tracking-tight">Send Your Blessings</h2>

                                <form wire:submit.prevent="save" class="space-y-6">
                                    <div class="text-left">
                                        <label class="block text-[10px] uppercase tracking-widest font-bold opacity-60 mb-3 ml-4">Will you join us?</label>
                                        <div class="grid grid-cols-1 gap-4">

                                            <button type="button" 
                                                wire:click="$set('statue', 'Coming')"
                                                class="p-4 rounded-2xl border transition-all {{ $statue === 'Coming' ? 'bg-white text-black border-white' : 'bg-white/5 border-white hover:bg-white/50' }}">
                                                Yes, I'll be there. ❤️
                                            </button>

                                            <button type="button" 
                                                wire:click="$set('statue', 'Pending')"
                                                class="p-4 rounded-2xl border transition-all {{ $statue === 'Pending' ? 'bg-white text-black border-white' : 'bg-white/5 border-white hover:bg-white/50' }}">
                                                Deciding... ⏳
                                            </button>

                                            <button type="button" 
                                                wire:click="$set('statue', 'Not Coming')"
                                                class="p-4 rounded-2xl border transition-all {{ $statue === 'Not Coming' ? 'bg-white text-black border-white' : 'bg-white/5 border-white hover:bg-white/50' }}">
                                                Sorry, I can't.
                                            </button>

                                        </div>
                                    </div>

                                    <div class="text-left">
                                        <label class="block text-[10px] uppercase tracking-widest font-bold opacity-60 mb-2 ml-4">Your Message</label>
                                        <textarea wire:model.defer="Greeting" rows="5"
                                            class="w-full bg-white/30 border border-white/20 rounded-2xl p-4  focus:outline-none focus:bg-white/20 transition resize-none"
                                            placeholder="Write your blessings here..."></textarea>
                                    </div>

                                    <button type="submit" 
                                        class="w-full py-4 bg-white  font-bold rounded-2xl hover:bg-opacity-90 transition shadow-xl uppercase tracking-widest text-sm">
                                        Send Message
                                    </button>
                                </form>
                            </div>
                        </section>

                        {{-- Messages from Guests --}}
                        <section class="reveal text-center px-6 max-w-2xl mx-auto" wire:key="guest-messages">
                            <div class="bg-white/10 backdrop-blur-lg rounded-[2.5rem] p-10 shadow-2xl border border-white/20">
                                <h2 class="text-3xl mb-8 tracking-tight">Messages & Blessings</h2>

                                <div class="space-y-6">
                                    @foreach($guests as $guest)
                                    <div class="bg-white/50 p-6 rounded-2xl border border-white text-left transition hover:bg-white/10">
                                        <p class="font-light leading-relaxed">{{ $guest->Greeting }}</p>
                                        <span class="block mt-4 text-[10px] uppercase tracking-widest font-bold opacity-60">__ {{ $guest->group }}</span>
                                    </div>
                                    @endforeach

                                    <div class="py-5 border-t border-white/10">
                                        <p class="opacity-40 text-xs tracking-widest uppercase">
                                            Messages and blessings will appear here ❤️
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- Social Media Links --}}
                        <section class="reveal text-center">
                            <h2 class="text-xs uppercase tracking-[0.6em] mb-5 opacity-50 font-bold">Stay Connected</h2>

                            <div class="flex flex-wrap justify-center gap-6 md:gap-10">
                        
                                <a href="#" target="_blank" 
                                class="reveal group flex items-center justify-center w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg transition-all duration-300 hover:bg-[#1877F2] hover:scale-110 hover:border-transparent">
                                    <i class="fa-brands fa-facebook-f text-xl opacity-70 group-hover:opacity-100 group-hover:text-white transition-all"></i>
                                </a>

                                <a href="#" target="_blank" 
                                class="reveal group flex items-center justify-center w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg transition-all duration-300 hover:bg-gradient-to-tr hover:from-[#f9ce34] hover:via-[#ee2a7b] hover:to-[#6228d7] hover:scale-110 hover:border-transparent">
                                    <i class="fa-brands fa-instagram text-2xl opacity-70 group-hover:opacity-100 group-hover:text-white transition-all"></i>
                                </a>

                                <a href="#" target="_blank" 
                                class="reveal group flex items-center justify-center w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg transition-all duration-300 hover:bg-[#26A5E4] hover:scale-110 hover:border-transparent">
                                    <i class="fa-brands fa-telegram text-2xl opacity-70 group-hover:opacity-100 group-hover:text-white transition-all"></i>
                                </a>

                                <a href="#" 
                                class="reveal group flex items-center justify-center w-12 h-12 rounded-full bg-white/10 backdrop-blur-md border border-white/20 shadow-lg transition-all duration-300 hover:bg-[#4ADE80] hover:scale-110 hover:border-transparent">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

@push('scripts')
<script>
    gsap.registerPlugin(ScrollTrigger);

    function initScrollMusic() {
        gsap.registerPlugin(ScrollTrigger);

        const music = document.getElementById("bg-music");
        if (!music) return;

        ScrollTrigger.create({
            trigger: "#start-music-section",
            start: "top center", // when section reaches middle of screen
            once: true, // play only once
            onEnter: () => {
                music.play().catch(() => {
                    console.log("Autoplay blocked, waiting for interaction");

                    const playOnInteraction = () => {
                        music.play();
                        document.removeEventListener("click", playOnInteraction);
                        document.removeEventListener("touchstart", playOnInteraction);
                    };

                    document.addEventListener("click", playOnInteraction);
                    document.addEventListener("touchstart", playOnInteraction);
                });
            }
        });
    }

    // Livewire safe
    document.addEventListener("livewire:load", () => {
        initScrollMusic();
    });

    document.addEventListener("livewire:navigated", () => {
        initScrollMusic();
    });

    // count date
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