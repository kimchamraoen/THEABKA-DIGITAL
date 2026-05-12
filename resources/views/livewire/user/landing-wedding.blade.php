<div class="relative min-h-screen w-full flex items-center justify-center font-serif overflow-hidden">
    @php
            // Ensure $background_images exists and convert it to an array for the loop
            // Replace '$background_images' with whatever variable name your component uses
            $files = is_array($background_images) ? $background_images : [$background_images];
    @endphp
    <div class="invitation-container relative z-20 w-full max-w-xl h-[100dvh] shadow-2xl overflow-hidden">

        <div id="weddingCard" class="relative h-full w-full z-10">

            <!-- Background Video -->
            <div class="absolute inset-0 z-0">
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
                <!-- <div class="absolute inset-0 bg-white/40 z-10"></div> -->
            </div>

            <!-- Content -->
            <div class="relative z-20 h-full flex flex-col items-center justify-between p-6 py-12">

                <div class="text-center space-y-6 mt-16">
                    <p class="uppercase tracking-[0.4em] text-stone-600 text-xs font-bold">The Wedding of</p>

                    <div class="flex flex-col items-center">
                        <h1 class="text-6xl md:text-7xl text-stone-900 drop-shadow-sm"
                            style="font-family: 'Great Vibes', cursive;">
                            {{ $invitation->groom_name }}
                        </h1>

                        <span class="text-3xl text-stone-500 my-2"
                            style="font-family: 'Great Vibes', cursive;">&</span>

                        <h1 class="text-6xl md:text-7xl text-stone-900 drop-shadow-sm"
                            style="font-family: 'Great Vibes', cursive;">
                            {{ $invitation->bride_name }}
                        </h1>
                    </div>
                </div>

                <div class="w-full max-w-xs text-center space-y-5">
                    <p class="uppercase tracking-widest text-[10px] text-stone-500 font-bold">
                        Request your patience at their wedding
                    </p>

                    <div class="border-y-2 border-stone-800 py-3 flex items-center justify-center gap-4">
                        <div class="text-xs font-bold uppercase tracking-widest text-stone-700">Sunday</div>
                        <div class="text-5xl font-light border-x border-stone-300 px-6 text-stone-900">21</div>
                        <div class="text-xs font-bold uppercase tracking-widest text-stone-700">07 PM</div>
                    </div>

                    <p class="text-sm tracking-[0.6em] font-bold text-stone-800 uppercase">
                        December 2026
                    </p>
                </div>

                <!-- BUTTON -->
                <button
                    id="openButton"
                    type="button"
                    class="group relative overflow-hidden p-4 px-14 text-xs tracking-[0.3em] uppercase font-bold text-black bg-white/80 rounded-full transition-all hover:bg-white active:scale-95 shadow-2xl z-30">

                    <span class="relative z-10">Open Invitation</span>
                </button>

            </div>
        </div>

    </div>

    <!-- STYLES -->
    <style>
        #weddingCard {
        /* 5s duration for a slow, cinematic feel */
        transition: transform 2s ease-in-out, opacity 2s ease-in-out;
        /* Ensures it zooms from the center */
        transform-origin: center center;
        will-change: transform, opacity;
    }

    #weddingCard.opened {
        /* Scale up to 3 (300% size) to create the zoom-in effect */
        transform: scale(1.3); 
        opacity: 20; /* Fade out as it zooms */
    }
    </style>

    <!-- SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const button = document.getElementById('openButton');
            const card = document.getElementById('weddingCard');

            let isNavigating = false;

            button.addEventListener('click', function () {

                if (isNavigating) return;
                isNavigating = true;

                card.classList.add('opened');

                setTimeout(() => {
                    window.location.href = "/invitation/guest/" + "{{ $uuid }}" + "/detail";
                },1000); 
            });

        });
    </script>

</div>