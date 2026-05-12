<div>
    <x-app-layout>
        @section('title', __('app.dashboard.title'))
        @section('page-title', __('app.dashboard.title'))
        <div class="px-4 pb-6">
            {{-- Page Title --}}
            <div class="mb-5">
                <h1 class="text-2xl font-bold">{{ __('app.dashboard.Income') }}</h1>
                <p class="text-sm opacity-50 mt-0.5">{{ __('app.dashboard.welcome_back') }} {{ auth()->user()->name }}</p>
            </div>

            {{-- Stats Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $stats = [
                        ['label' => __('app.dashboard.totle_guests'), 'count' => \App\Models\Guest::where('gift_money', '!=', null)->where('user_id', auth()->id())->count(), 'color' => 'blue', 'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z', 'type' => 'guests'],
                        ['label' => __('app.dashboard.wedding_gift_dollar'), 'count' => \App\Models\Guest::where('user_id', auth()->id())->where('note', 'USD')->sum('gift_money'), 'color' => 'emerald', 'icon' => 'M12 6v12m-3-2.818.803.225a7.5 7.5 0 0 0 3.933 0c1.33-.374 2.232-1.397 2.232-2.522 0-1.205-.83-2.122-2.603-2.551L10.74 9.87c-1.774-.429-2.603-1.346-2.603-2.551 0-1.125.902-2.148 2.232-2.522a7.5 7.5 0 0 1 3.933 0L15 5.062', 'type' => 'money'],
                        ['label' => __('app.dashboard.wedding_gift_riel'), 'count' => \App\Models\Guest::where('user_id', auth()->id())->where('note', 'KHR')->sum('gift_money'), 'color' => 'rose', 'icon' => 'M12 20c-2 0-3.5-1.5-3.5-3.5 0-1.5 1-2.5 2-3m1.5 6.5V9m-5 4h10M13.5 9l-4-4 2-2 4 4-2 2Z', 'type' => 'money'],
                        ['label' => __('app.dashboard.wedding_gift_total'), 'count' => \App\Models\Guest::where('user_id', auth()->id())
                            ->get()
                            ->sum(function ($guest) {
                                $rate = 4100;

                                (float) $amount = (float) $guest->gift_money;

                                $note = strtoupper($guest->note);

                                return $note === 'KHR' ? (float) $amount / $rate : (float) $amount;
                            }), 'color' => 'amber', 'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75-6.75a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 .75.75v10.5a.75.75 0 0 1-.75.75H3.75a.75.75 0 0 1-.75-.75V9.75Z',
                            'type' => 'money'
                        ]
                    ];
                @endphp

                @foreach($stats as $stat)
                <div class="relative overflow-hidden group glass-card rounded-3xl p-6 transition-all duration-300 hover:translate-y-[-4px] hover:shadow-2xl border border-white/10 bg-white/5 backdrop-blur-md">
                    {{-- Decorative Background Glow --}}
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-{{ $stat['color'] }}-500/10 rounded-full blur-2xl group-hover:bg-{{ $stat['color'] }}-500/20 transition-colors"></div>
                    
                    <div class="flex flex-col">
                        <span class="text-xs font-medium uppercase tracking-wider text-{{ $stat['color'] }}-400 mb-1">
                            {{ $stat['label'] }}
                        </span>
                        <div class="flex items-end justify-between">
                            <h3 class="text-4xl font-extrabold tracking-tight text-white">
                                @if($stat['type'] === 'money')
                                    {{ number_format($stat['count'], 2) }}
                                @else
                                    {{ number_format($stat['count']) }}
                                @endif
                            </h3>
                            <div class="p-2 rounded-xl bg-{{ $stat['color'] }}-500/10 text-{{ $stat['color'] }}-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $stat['icon'] }}" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Graph Section --}}
            <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="glass-card rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md overflow-hidden">
                    <!-- <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center">
                        <h3 class="font-semibold text-lg text-white">Distribution Overview</h3>
                        <button class="text-xs text-white/50 hover:text-white transition-colors">View Details →</button>
                    </div> -->
                    <div class="pb-5 flex justify-center">
                        {{-- Centered the chart and gave it a cleaner container --}}
                        <div id="donutchart" class="w-full max-w-md" style="height: 20rem;"></div>
                    </div>
                </div>
                <div class="glass-card rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md overflow-hidden">
                    <!-- <div class="px-6 py-4 border-b border-white/5 flex justify-between items-center">
                        <h3 class="font-semibold text-lg text-white">Distribution Overview</h3>
                        <button class="text-xs text-white/50 hover:text-white transition-colors">View Details →</button>
                    </div> -->
                    <div class="pb-5 flex justify-center">
                        {{-- Centered the chart and gave it a cleaner container --}}
                        <div id="columnchart_values" class="w-full max-w-md" style="height: 20rem;"></div>
                    </div>
                </div>
            </div>
            
        </div>
    </x-app-layout>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    google.charts.setOnLoadCallback(columnChart);

    // Donut chart
    function drawChart() {
        var data = google.visualization.arrayToDataTable(@json($chartData));

        var options = {
            pieHole: 0.7, // Thinner donut looks more modern
            backgroundColor: 'transparent',
            colors: ['#34d399', '#fb7185', '#fbbf24', '#fbbf24'], // Tailwind-matching colors
            legend: { position: 'bottom', textStyle: { color: '#fff', fontSize: 12 } },
            chartArea: { width: '90%', height: '80%' },
            pieSliceBorderColor: "none"
        };

        var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
        chart.draw(data, options);
    }

    // Column chart
    function columnChart() {
        var data = google.visualization.arrayToDataTable(@json($chartData));

        var options = {
            backgroundColor: 'transparent',
            chartArea: { width: '85%', height: '75%' },
            bar: { groupWidth: "65%" }, // Slightly narrower for a cleaner look
            legend: { position: "none" },
            hAxis: {
                textStyle: { color: '#ffffff', fontSize: 12, opacity: 0.8 },
                gridlines: { color: 'transparent' }
            },
            vAxis: {
                textStyle: { color: '#ffffff', fontSize: 11, opacity: 0.5 },
                gridlines: { color: 'rgba(255, 255, 255, 0.05)' },
                baselineColor: 'rgba(255, 255, 255, 0.1)'
            },
            annotations: {
                alwaysOutside: true,
                textStyle: {
                    fontSize: 12,
                    bold: true,
                    color: '#fff'
                }
            },
            animation: {
                duration: 1200,
                easing: 'out',
                startup: true
            }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
        chart.draw(data, options);
    }
</script>