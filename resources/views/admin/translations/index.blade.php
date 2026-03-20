<x-app-layout>
    @php
        $targetLocales = $languages->pluck('locale')->filter(fn ($l) => $l !== $sourceLocale)->values();
        $totalKeys = count($translations);
        $totalTargetCells = max(1, $totalKeys * max(1, $targetLocales->count()));
        $filledTargetCells = 0;
        $customFontFaces = [];

        foreach ($translations as $row) {
            foreach ($targetLocales as $targetLocale) {
                if (!empty(trim($row[$targetLocale] ?? ''))) {
                    $filledTargetCells++;
                }
            }
        }

        $missingCount = max(0, $totalTargetCells - $filledTargetCells);

        $fontStyleByLocale = [];
        foreach ($languages as $language) {
            $style = "font-family: ui-sans-serif, system-ui, sans-serif;";

            if (($language->font_type ?? 'system') === 'google' && !empty($language->font_value)) {
                $fontName = e($language->font_value);
                $style = "font-family: '{$fontName}', ui-sans-serif, system-ui, sans-serif;";
            }

            if (($language->font_type ?? 'system') === 'custom' && !empty($language->font_value)) {
                $family = 'lang_font_' . $language->locale;
                $url = asset('storage/fonts/' . ltrim($language->font_value, '/'));
                $style = "font-family: '{$family}', ui-sans-serif, system-ui, sans-serif;";
                $customFontFaces[] = ['family' => $family, 'url' => $url];
            }

            $fontStyleByLocale[$language->locale] = $style;
        }
    @endphp

    <div x-data="translationManager()" x-init="init()" class="py-4 px-2 sm:px-4 lg:px-6 min-h-[calc(100vh-2rem)]">
        <div class="w-full max-w-7xl mx-auto">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <h1 class="text-xl font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 opacity-60" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802" />
                        </svg>
                        {{ __('app.translations.title') }}
                    </h1>
                    <p class="text-xs opacity-40 mt-0.5">{{ __('app.translations.description') }}</p>
                </div>
                <div class="text-xs font-mono opacity-60 bg-white/5 border border-white/10 rounded-lg px-3 py-1.5">
                    <span x-text="totalKeys">{{ $totalKeys }}</span> keys ·
                    <span class="text-emerald-400" x-text="translatedCells">{{ $filledTargetCells }}</span> translated ·
                    <span class="text-amber-400" x-text="missingCells">{{ $missingCount }}</span> missing
                </div>
            </div>

            @if (session('success'))
                <div class="mb-3 px-4 py-2.5 rounded-xl bg-green-500/15 border border-green-500/30 text-green-400 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex flex-col sm:flex-row gap-2 mb-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" x-model="search" @input="doSearch()"
                           placeholder="Search keys or translations..."
                           class="w-full pl-9 pr-8 py-2 rounded-xl bg-white/5 border border-white/10 text-sm focus:outline-none focus:border-blue-500/50 placeholder-white/25 transition">
                </div>

                <div class="flex gap-2">
                    <button type="button" @click="translateAllMissing()" :disabled="isTranslatingAll"
                            class="px-3 py-2 rounded-xl bg-purple-600/80 hover:bg-purple-500/80 border border-purple-500/30 text-white text-sm font-medium transition disabled:opacity-50">
                        <span x-show="!isTranslatingAll">🌐 Translate All Missing</span>
                        <span x-show="isTranslatingAll" x-cloak x-text="translateProgress"></span>
                    </button>
                    <button type="button" @click="addRow()"
                            class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 text-sm font-medium transition">
                        + {{ __('app.translations.add_new_key') }}
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.translations.update') }}" id="translationsForm">
                @csrf

                <div class="glass-card rounded-2xl overflow-hidden border border-white/10">
                    <div style="overflow:auto; max-height:calc(100vh - 300px);">
                        <table class="w-full text-left" style="border-collapse:collapse; min-width: 980px;">
                            <thead class="sticky top-0 z-10 backdrop-blur-xl border-b border-white/10 text-xs uppercase tracking-wider opacity-60" style="background:rgba(255,255,255,0.05);">
                                <tr>
                                    <th class="px-3 py-2.5 font-semibold" style="width:20%">KEY</th>
                                    @foreach ($languages as $language)
                                        <th class="px-3 py-2.5 font-semibold" style="min-width:220px;">
                                            {{ $language->flag ?: '🏳️' }} {{ $language->name }}
                                            @if ($language->locale === $sourceLocale)
                                                <span class="text-[10px] ml-1 opacity-60">(source)</span>
                                            @endif
                                        </th>
                                    @endforeach
                                    <th class="px-3 py-2.5 font-semibold text-center" style="width:8%">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody id="translationsBody">
                                @foreach ($translations as $key => $values)
                                    <tr class="translation-row border-b border-white/5 hover:bg-white/[0.03]" data-search="{{ strtolower($key . ' ' . implode(' ', array_values($values))) }}">
                                        <td class="px-3 py-1.5 align-middle">
                                            <input type="hidden" name="keys[]" value="{{ $key }}">
                                            <code class="text-[11px] opacity-70 font-mono break-all select-all">{{ $key }}</code>
                                        </td>

                                        @foreach ($languages as $language)
                                            <td class="px-3 py-1.5 align-middle">
                                                <div class="flex items-center gap-1.5">
                                                    <input
                                                        type="text"
                                                        name="values[{{ $language->locale }}][]"
                                                        value="{{ $values[$language->locale] ?? '' }}"
                                                        data-locale="{{ $language->locale }}"
                                                        @if ($language->locale === $sourceLocale) data-source="1" @endif
                                                        class="w-full bg-white/5 border border-white/10 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:border-blue-500/50 transition"
                                                        style="{{ $fontStyleByLocale[$language->locale] ?? '' }}"
                                                        @input="recount()"
                                                    >

                                                    @if ($language->locale !== $sourceLocale)
                                                        <button type="button"
                                                                class="p-1 rounded text-blue-400/70 hover:text-blue-400 hover:bg-blue-500/10 transition text-sm"
                                                                @click="translateCell($el.closest('tr'), '{{ $language->locale }}')"
                                                                title="Auto translate to {{ $language->name }}">
                                                            🌐
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach

                                        <td class="px-3 py-1.5 text-center align-middle">
                                            <button type="button" class="p-1 rounded text-red-400/50 hover:text-red-400 hover:bg-red-500/10 transition"
                                                    @click="deleteKey('{{ $key }}')">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="sticky bottom-0 z-20 mt-3 py-3 flex items-center justify-end gap-2" style="background: linear-gradient(to top, var(--bg-from, #0f172a) 60%, transparent);">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold transition">
                        {{ __('app.translations.save_all') }}
                    </button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.translations.destroy') }}" id="deleteForm" class="hidden">
                @csrf
                @method('DELETE')
                <input type="hidden" name="key" id="deleteKeyInput">
            </form>
        </div>
    </div>

    @foreach ($languages as $language)
        @if (($language->font_type ?? 'system') === 'google' && !empty($language->font_value))
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family={{ urlencode($language->font_value) }}&display=swap" rel="stylesheet">
        @endif
    @endforeach

    @foreach ($customFontFaces as $fontFace)
        <style>
            @font-face {
                font-family: '{{ $fontFace['family'] }}';
                src: url('{{ $fontFace['url'] }}') format('woff2'),
                     url('{{ $fontFace['url'] }}') format('woff'),
                     url('{{ $fontFace['url'] }}') format('truetype');
                font-weight: normal;
                font-style: normal;
            }
        </style>
    @endforeach

    <script>
        function translationManager() {
            return {
                search: '',
                totalKeys: {{ $totalKeys }},
                translatedCells: {{ $filledTargetCells }},
                missingCells: {{ $missingCount }},
                sourceLocale: @js($sourceLocale),
                locales: @js($languages->pluck('locale')->values()),
                targetLocales: @js($targetLocales),
                fontStyleByLocale: @js($fontStyleByLocale),
                isTranslatingAll: false,
                translateProgress: '',

                init() {
                    this.recount();
                },

                doSearch() {
                    const q = this.search.toLowerCase().trim();
                    document.querySelectorAll('.translation-row').forEach((row) => {
                        const hay = row.dataset.search || '';
                        row.style.display = (!q || hay.includes(q)) ? '' : 'none';
                    });
                },

                recount() {
                    let translated = 0;
                    let totalTargets = 0;

                    document.querySelectorAll('.translation-row').forEach((row) => {
                        let hay = [];
                        const keyInput = row.querySelector('input[name="keys[]"]');
                        const keyValue = keyInput ? keyInput.value : '';
                        row.querySelectorAll('input[type="text"]').forEach((input) => {
                            hay.push(input.value || '');
                            if ((input.dataset.locale || '') !== this.sourceLocale) {
                                totalTargets++;
                                if ((input.value || '').trim() !== '') {
                                    translated++;
                                }
                            }
                        });
                        row.dataset.search = `${(keyValue || '').toLowerCase()} ${hay.join(' ').toLowerCase()}`;
                    });

                    this.translatedCells = translated;
                    this.missingCells = Math.max(0, totalTargets - translated);
                    this.totalKeys = document.querySelectorAll('.translation-row').length;
                },

                async translateCell(row, targetLocale) {
                    const sourceInput = row.querySelector(`input[data-locale="${this.sourceLocale}"]`);
                    const targetInput = row.querySelector(`input[data-locale="${targetLocale}"]`);
                    if (!sourceInput || !targetInput || !sourceInput.value.trim()) return;

                    try {
                        const res = await fetch('{{ route('admin.translations.auto-translate') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ text: sourceInput.value, target_locale: targetLocale })
                        });

                        const data = await res.json();
                        if (!res.ok) throw new Error(data.error || 'Translation failed');

                        targetInput.value = data.translation || '';
                        targetInput.dispatchEvent(new Event('input', { bubbles: true }));
                        this.recount();
                    } catch (e) {
                        alert(e.message);
                    }
                },

                async translateAllMissing() {
                    this.isTranslatingAll = true;
                    this.translateProgress = 'Starting...';

                    try {
                        const res = await fetch('{{ route('admin.translations.auto-translate-all') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({})
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.error || 'Failed');

                        this.translateProgress = `${data.translated}/${data.total}`;
                        window.location.reload();
                    } catch (e) {
                        alert(e.message);
                    } finally {
                        this.isTranslatingAll = false;
                    }
                },

                addRow() {
                    const tbody = document.getElementById('translationsBody');
                    const tr = document.createElement('tr');
                    tr.className = 'translation-row border-b border-white/5 hover:bg-white/[0.03]';
                    tr.dataset.search = '';

                    const keyCell = document.createElement('td');
                    keyCell.className = 'px-3 py-1.5 align-middle';
                    keyCell.innerHTML = `
                        <input type="text" name="keys[]" placeholder="app.example.key" class="w-full bg-white/5 border border-white/10 rounded-lg px-2 py-1.5 text-xs font-mono focus:outline-none focus:border-blue-500/50">
                    `;
                    tr.appendChild(keyCell);

                    this.locales.forEach((locale) => {
                        const td = document.createElement('td');
                        td.className = 'px-3 py-1.5 align-middle';
                        const isSource = locale === this.sourceLocale;
                        td.innerHTML = `
                            <div class="flex items-center gap-1.5">
                                <input type="text" name="values[${locale}][]" data-locale="${locale}" ${isSource ? 'data-source="1"' : ''}
                                    class="w-full bg-white/5 border border-white/10 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:border-blue-500/50 transition">
                                ${isSource ? '' : `<button type="button" class="p-1 rounded text-blue-400/70 hover:text-blue-400 hover:bg-blue-500/10 transition text-sm">🌐</button>`}
                            </div>
                        `;

                        const textInput = td.querySelector('input');
                        if (textInput && this.fontStyleByLocale[locale]) {
                            textInput.setAttribute('style', this.fontStyleByLocale[locale]);
                        }

                        if (!isSource) {
                            td.querySelector('button').addEventListener('click', () => this.translateCell(tr, locale));
                        }

                        textInput.addEventListener('input', () => this.recount());
                        tr.appendChild(td);
                    });

                    const actionCell = document.createElement('td');
                    actionCell.className = 'px-3 py-1.5 text-center align-middle';
                    actionCell.innerHTML = '<button type="button" class="text-red-400/70 hover:text-red-400 text-xs">✕</button>';
                    actionCell.querySelector('button').addEventListener('click', () => {
                        tr.remove();
                        this.recount();
                    });
                    tr.appendChild(actionCell);

                    tbody.appendChild(tr);
                    this.recount();
                },

                deleteKey(key) {
                    if (!confirm(`Delete key: ${key}?`)) return;
                    document.getElementById('deleteKeyInput').value = key;
                    document.getElementById('deleteForm').submit();
                }
            };
        }
    </script>
</x-app-layout>
