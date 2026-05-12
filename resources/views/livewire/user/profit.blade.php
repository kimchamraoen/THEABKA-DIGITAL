<div>
    <x-app-layout>
        @section('title', __('app.dashboard.title'))
        @section('page-title', __('app.dashboard.title'))
        <div class="px-4 pb-6">
            {{-- Page Title --}}
            <div class="mb-5">
                <h1 class="text-2xl font-bold">{{ __('app.dashboard.Profit') }}</h1>
                <p class="text-sm opacity-50 mt-0.5">{{ __('app.dashboard.welcome_back') }} {{ auth()->user()->name }}</p>
            </div>

            {{-- Stats Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $income = \App\Models\Guest::where('user_id', auth()->id())
                        ->get()
                        ->sum(function ($guest) {
                            $rate = 4100;

                            $amount = (float) $guest->gift_money;

                            $note = strtoupper($guest->note);

                            return $note === 'KHR' ? $amount / $rate : $amount;
                        });

                    $expense = \App\Models\Expence::where('user_id', auth()->id())->sum('amount');

                    $stats = [
                        [   
                            'label' => __('app.dashboard.wedding_profit'), 
                            'count' => round($income - $expense, 2),
                            'color' => 'amber', 
                            'icon' => 'M6 8h12a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2Zm0 0V6h12v2M8 12h8M12 10.5v3'
                        ],
                        [
                            'label' => __('app.dashboard.wedding_income'), 
                            'count' => $income,
                            'color' => 'emerald', 
                            'icon' => 'M6 8c0-1.1.9-2 2-2h10.5a2.5 2.5 0 0 1 0 5H8a2 2 0 0 1-2-2Zm12.5 1.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2ZM8 6H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-9.5H21a4 4 0 0 1-4 4H8a3 3 0 0 1-3-3V8c0-1.1.9-2 3-2Zm7.5 8.5 3-3 3 3-1.5 1.5-1.2-1.2V22h-2v-7.2l-1.2 1.2-1.1-1.1Z'
                        ],
                        [
                            'label' => __('app.dashboard.wedding_expense'), 
                            'count' => $expense,
                            'color' => 'rose', 
                            'icon' => 'M12 3 3 10v10h6v-6h6v6h6V10l-9-7Zm6.5 8.75c-.6 1.7-2.2 3.25-5.5 3.25s-4.9-1.55-5.5-3.25M8 12.5c.9-2.2 2.4-3.5 4-3.5s3.1 1.3 4 3.5'
                        ],
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
                                {{ number_format($stat['count'], 2) }}
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
        var data = google.visualization.arrayToDataTable([
            ['Category', 'Amount'],
            ['Income', {{$income}}],
            ['Expense', {{$expense}}]
        ]);

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
    var data = google.visualization.arrayToDataTable([
        ['Category', 'Amount', { role: 'annotation' }],
        ['Income', {{$income}}, '{{ number_format($income) }}'],
        ['Expense', {{$expense}}, '{{ number_format($expense) }}']
    ]);

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