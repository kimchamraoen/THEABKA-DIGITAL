<x-app-layout>
    @section('title', 'User Analytics')
    @section('page-title', 'User Analytics')

    @push('styles')
        <style>
            .analytics-shell {
                --muted: #8892a4;
                --text: #f8fafc;
                --border: rgba(255, 255, 255, 0.08);
                --teal: #00d4aa;
                --gold: #f6c90e;
                --purple: #7c5cbf;
                --rose: #ff6b6b;
                --sky: #4fc3f7;
                color: var(--text);
                min-height: calc(100vh - 2rem);
                padding: 24px 8px;
            }

            .analytics-container {
                max-width: 1260px;
                margin: 0 auto;
            }

            .analytics-header {
                margin-bottom: 18px;
            }

            .analytics-title {
                margin: 0;
                font-size: 2rem;
                font-weight: 800;
                letter-spacing: 0.01em;
            }

            .analytics-subtitle {
                margin: 6px 0 0;
                color: var(--muted);
                font-size: 0.9rem;
            }

            .analytics-stat-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 20px;
                margin-bottom: 20px;
                overflow: visible !important;
            }

            .analytics-chart-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
                overflow: visible !important;
            }

            .analytics-card {
                border-radius: 1rem;
                box-shadow: 0 16px 32px rgba(1, 8, 30, 0.3);
                overflow: visible !important;
            }

            .stat-card {
                padding: 20px;
                display: flex;
                flex-direction: column;
                min-height: 190px;
                position: relative;
                overflow: visible !important;
            }

            .stat-top {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 10px;
            }

            .stat-label {
                color: var(--muted);
                font-size: 0.9rem;
            }

            .stat-value {
                margin: 0;
                font-size: 2rem;
                font-weight: 800;
                color: #fff;
                line-height: 1.15;
            }

            .trend-badge {
                font-size: 0.78rem;
                font-weight: 700;
                border-radius: 999px;
                padding: 5px 10px;
                border: 1px solid transparent;
            }

            .trend-up {
                color: #2de38f;
                background: rgba(45, 227, 143, 0.12);
                border-color: rgba(45, 227, 143, 0.3);
            }

            .trend-down {
                color: #ff7b8e;
                background: rgba(255, 123, 142, 0.12);
                border-color: rgba(255, 123, 142, 0.3);
            }

            .sparkline-wrap {
                margin-top: auto;
                height: 68px;
                position: relative;
            }

            .sparkline-wrap::before {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 10px;
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0));
                pointer-events: none;
            }

            .chart-card {
                padding: 20px;
            }

            .chart-card-wide {
                grid-column: 1 / -1;
            }

            .chart-heading {
                display: flex;
                justify-content: space-between;
                align-items: baseline;
                margin-bottom: 16px;
            }

            .chart-title {
                margin: 0;
                color: #fff;
                font-size: 14px;
                font-weight: 600;
                letter-spacing: 0.02em;
            }

            .chart-meta {
                color: var(--muted);
                font-size: 12px;
            }

            .chart-wrap {
                height: 290px;
            }

            .chart-wrap-tall {
                height: 340px;
            }

            .legend-row {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 12px;
            }

            .legend-chip {
                font-size: 12px;
                color: #c5ccdb;
                border: 1px solid var(--border);
                border-radius: 999px;
                padding: 5px 10px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: rgba(255, 255, 255, 0.02);
            }

            .legend-dot {
                width: 9px;
                height: 9px;
                border-radius: 50%;
            }

            .provider-list {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .provider-row {
                border-radius: 12px;
                border: 1px solid var(--border);
                background: rgba(255, 255, 255, 0.02);
                padding: 12px;
            }

            .provider-row-head {
                display: flex;
                justify-content: space-between;
                font-size: 13px;
                margin-bottom: 8px;
            }

            .provider-name {
                color: #fff;
                font-weight: 600;
            }

            .provider-value {
                color: var(--muted);
            }

            .provider-progress {
                width: 100%;
                height: 8px;
                border-radius: 99px;
                background: rgba(255, 255, 255, 0.08);
                overflow: hidden;
            }

            .provider-progress-fill {
                width: 0;
                height: 100%;
                border-radius: 99px;
                transition: width 1s ease;
            }

            @media (max-width: 1200px) {
                .analytics-stat-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .analytics-chart-grid {
                    grid-template-columns: 1fr;
                }

                .chart-card-wide {
                    grid-column: auto;
                }
            }

            @media (max-width: 760px) {
                .analytics-shell {
                    padding: 18px 8px;
                }

                .analytics-stat-grid {
                    grid-template-columns: 1fr;
                    gap: 14px;
                }

                .chart-wrap,
                .chart-wrap-tall {
                    height: 280px;
                }
            }
        </style>
    @endpush

    <div class="analytics-shell">
        <div class="analytics-container">
            <div class="analytics-header">
                <h1 class="analytics-title">User Analytics</h1>
                <p class="analytics-subtitle">Login behavior, platform usage, and growth across the last 30 days.</p>
            </div>

            <div class="analytics-stat-grid">
                <section class="glass-card analytics-card rounded-2xl stat-card">
                    <div class="stat-top">
                        <div>
                            <p class="stat-value" id="stat-total-users">0</p>
                            <p class="stat-label">Total Users</p>
                        </div>
                        <span class="trend-badge trend-up" id="trend-total-users">▲ +0.0%</span>
                    </div>
                    <div class="sparkline-wrap">
                        <canvas id="spark-users" style="width:100% !important; height:100% !important;"></canvas>
                    </div>
                </section>

                <section class="glass-card analytics-card rounded-2xl stat-card">
                    <div class="stat-top">
                        <div>
                            <p class="stat-value" id="stat-logins-today">0</p>
                            <p class="stat-label">Logins Today</p>
                        </div>
                        <span class="trend-badge trend-up" id="trend-logins">▲ +0.0%</span>
                    </div>
                    <div class="sparkline-wrap">
                        <canvas id="spark-logins" style="width:100% !important; height:100% !important;"></canvas>
                    </div>
                </section>

                <section class="glass-card analytics-card rounded-2xl stat-card">
                    <div class="stat-top">
                        <div>
                            <p class="stat-value" id="stat-countries">0</p>
                            <p class="stat-label">Countries Reached</p>
                        </div>
                        <span class="trend-badge trend-up" id="trend-countries">▲ +0.0%</span>
                    </div>
                    <div class="sparkline-wrap">
                        <canvas id="spark-countries" style="width:100% !important; height:100% !important;"></canvas>
                    </div>
                </section>

                <section class="glass-card analytics-card rounded-2xl stat-card">
                    <div class="stat-top">
                        <div>
                            <p class="stat-value" id="stat-mobile">0%</p>
                            <p class="stat-label">Mobile Users %</p>
                        </div>
                        <span class="trend-badge trend-down" id="trend-mobile">▼ -0.0%</span>
                    </div>
                    <div class="sparkline-wrap">
                        <canvas id="spark-mobile" style="width:100% !important; height:100% !important;"></canvas>
                    </div>
                </section>
            </div>

            <div class="analytics-chart-grid">
                <section class="glass-card analytics-card rounded-2xl chart-card">
                    <div class="chart-heading">
                        <h2 class="chart-title">Login Provider Breakdown</h2>
                        <span class="chart-meta">Last 30 days</span>
                    </div>
                    <div id="provider-list" class="provider-list"></div>
                </section>

                <section class="glass-card analytics-card rounded-2xl chart-card">
                    <div class="chart-heading">
                        <h2 class="chart-title">Device Types</h2>
                        <span class="chart-meta">Share by platform</span>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="chart-devices" style="width:100% !important; height:100% !important;"></canvas>
                    </div>
                </section>

                <section class="glass-card analytics-card rounded-2xl chart-card">
                    <div class="chart-heading">
                        <h2 class="chart-title">Browser Usage</h2>
                        <span class="chart-meta">Top browsers</span>
                    </div>
                    <div id="browser-legend" class="legend-row"></div>
                    <div class="chart-wrap">
                        <canvas id="chart-browsers" style="width:100% !important; height:100% !important;"></canvas>
                    </div>
                </section>

                <section class="glass-card analytics-card rounded-2xl chart-card">
                    <div class="chart-heading">
                        <h2 class="chart-title">OS Distribution</h2>
                        <span class="chart-meta">Operating systems</span>
                    </div>
                    <div id="os-legend" class="legend-row"></div>
                    <div class="chart-wrap">
                        <canvas id="chart-os" style="width:100% !important; height:100% !important;"></canvas>
                    </div>
                </section>

                <section class="glass-card analytics-card rounded-2xl chart-card chart-card-wide">
                    <div class="chart-heading">
                        <h2 class="chart-title">Top 10 Cities</h2>
                        <span class="chart-meta">Most active locations</span>
                    </div>
                    <div class="chart-wrap-tall">
                        <canvas id="chart-locations" style="width:100% !important; height:100% !important;"></canvas>
                    </div>
                </section>

                <section class="glass-card analytics-card rounded-2xl chart-card chart-card-wide">
                    <div class="chart-heading">
                        <h2 class="chart-title">New Users Over 30 Days</h2>
                        <span class="chart-meta">Daily registrations</span>
                    </div>
                    <div class="chart-wrap-tall">
                        <canvas id="registrationChart" style="width:100% !important; height:100% !important;"></canvas>
                    </div>
                </section>

                <section class="glass-card analytics-card rounded-2xl chart-card chart-card-wide">
                    <div class="chart-heading">
                        <h2 class="chart-title">Login Activity Over 30 Days</h2>
                        <span class="chart-meta">Daily sign-ins</span>
                    </div>
                    <div class="chart-wrap-tall">
                        <canvas id="loginChart" style="width:100% !important; height:100% !important;"></canvas>
                    </div>
                </section>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            window.analyticsCharts = window.analyticsCharts || {};
            window.analyticsInitToken = window.analyticsInitToken || 0;

            function bootAnalytics(retries) {
                retries = retries || 0;
                if (typeof Chart === 'undefined') {
                    if (retries < 20) {
                        setTimeout(function() {
                            bootAnalytics(retries + 1);
                        }, 120);
                    }
                    return;
                }

                initCharts().catch(function(error) {
                    console.error('Analytics initialization failed:', error);
                });
            }

            // Important for wire:navigate: script may run after livewire:navigated fired.
            // Trigger an immediate pass when this script is evaluated.
            if (document.readyState !== 'loading') {
                setTimeout(bootAnalytics, 0);
            }

            document.addEventListener('DOMContentLoaded', function() {
                bootAnalytics();
            });

            document.addEventListener('livewire:load', bootAnalytics);
            document.addEventListener('livewire:update', bootAnalytics);
            document.addEventListener('livewire:navigated', function() {
                requestAnimationFrame(function() {
                    bootAnalytics();
                });
            });

            function destroyChart(key) {
                if (window.analyticsCharts[key] instanceof Chart) {
                    window.analyticsCharts[key].destroy();
                    delete window.analyticsCharts[key];
                }
            }

            function setChart(key, chart) {
                destroyChart(key);
                window.analyticsCharts[key] = chart;
                if (chart && typeof chart.resize === 'function') {
                    chart.resize();
                }
            }

            function fetchJson(url) {
                return fetch(url, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json' }
                }).then(function(response) {
                    if (!response.ok) {
                        throw new Error('Request failed: ' + url + ' (' + response.status + ')');
                    }

                    var contentType = response.headers.get('content-type') || '';
                    if (!contentType.includes('application/json')) {
                        throw new Error('Expected JSON response from ' + url);
                    }

                    return response.json();
                });
            }

            function numericSeries(rows, key) {
                return (rows || []).map(function(row) {
                    return Number(row[key] || 0);
                });
            }

            function trendPercent(values) {
                if (!values || values.length < 2) {
                    return 0;
                }
                var current = Number(values[values.length - 1] || 0);
                var previous = Number(values[values.length - 2] || 0);
                if (previous === 0) {
                    return current > 0 ? 100 : 0;
                }
                return ((current - previous) / previous) * 100;
            }

            function applyTrend(id, value) {
                var el = document.getElementById(id);
                if (!el) {
                    return;
                }
                var rounded = Math.abs(value).toFixed(1);
                var isUp = value >= 0;
                el.classList.remove('trend-up', 'trend-down');
                el.classList.add(isUp ? 'trend-up' : 'trend-down');
                el.textContent = (isUp ? '▲ +' : '▼ -') + rounded + '%';
            }

            function gradientFill(ctx, color, alphaTop) {
                var gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, color.replace('1)', alphaTop + ')'));
                gradient.addColorStop(1, color.replace('1)', '0)'));
                return gradient;
            }

            function renderLegend(targetId, rows, labelKey, palette) {
                var host = document.getElementById(targetId);
                if (!host) {
                    return;
                }
                host.innerHTML = (rows || []).map(function(row, idx) {
                    var label = row[labelKey] || 'Unknown';
                    var color = palette[idx % palette.length];
                    return '<span class="legend-chip"><span class="legend-dot" style="background:' + color + '"></span>' + label + '</span>';
                }).join('');
            }

            function renderProviderBreakdown(rows) {
                var host = document.getElementById('provider-list');
                if (!host) {
                    return;
                }

                var gradients = {
                    email: 'linear-gradient(90deg, #00d4aa, #4fc3f7)',
                    telegram: 'linear-gradient(90deg, #229ED9, #7c5cbf)',
                    facebook: 'linear-gradient(90deg, #4267B2, #7c5cbf)',
                    google: 'linear-gradient(90deg, #f6c90e, #ff6b6b)',
                    github: 'linear-gradient(90deg, #7c5cbf, #4fc3f7)',
                    other: 'linear-gradient(90deg, #7c5cbf, #00d4aa)'
                };

                var total = (rows || []).reduce(function(sum, row) {
                    return sum + Number(row.total || 0);
                }, 0);

                host.innerHTML = (rows || []).map(function(row) {
                    var name = String(row.provider || 'Other');
                    var key = name.toLowerCase();
                    var count = Number(row.total || 0);
                    var pct = total > 0 ? Math.round((count / total) * 100) : 0;
                    var fill = gradients[key] || gradients.other;

                    return [
                        '<div class="provider-row">',
                        '<div class="provider-row-head">',
                        '<span class="provider-name">' + name + '</span>',
                        '<span class="provider-value">' + count + ' (' + pct + '%)</span>',
                        '</div>',
                        '<div class="provider-progress">',
                        '<div class="provider-progress-fill" style="background:' + fill + ';" data-width="' + pct + '"></div>',
                        '</div>',
                        '</div>'
                    ].join('');
                }).join('');

                requestAnimationFrame(function() {
                    host.querySelectorAll('.provider-progress-fill').forEach(function(el) {
                        el.style.width = el.getAttribute('data-width') + '%';
                    });
                });
            }

            var doughnutCenterText = {
                id: 'doughnutCenterText',
                afterDraw: function(chart, args, opts) {
                    if (!opts || !opts.text) {
                        return;
                    }
                    var meta = chart.getDatasetMeta(0);
                    if (!meta || !meta.data || !meta.data.length) {
                        return;
                    }
                    var ctx = chart.ctx;
                    var x = meta.data[0].x;
                    var y = meta.data[0].y;
                    ctx.save();
                    ctx.fillStyle = '#f8fafc';
                    var family = getComputedStyle(document.body).fontFamily || 'sans-serif';
                    ctx.font = '700 18px ' + family;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(opts.text, x, y);
                    ctx.restore();
                }
            };

            async function initCharts() {
                if (typeof Chart === 'undefined') {
                    return;
                }

                var root = document.getElementById('chart-devices');
                if (!root) {
                    return;
                }

                var runToken = ++window.analyticsInitToken;

                Chart.defaults.color = '#8892a4';
                Chart.defaults.font.family = 'inherit';
                Chart.defaults.plugins.legend.labels.boxWidth = 12;
                Chart.defaults.plugins.legend.labels.padding = 20;
                Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15,20,40,0.95)';
                Chart.defaults.plugins.tooltip.borderColor = 'rgba(255,255,255,0.1)';
                Chart.defaults.plugins.tooltip.borderWidth = 1;
                Chart.defaults.plugins.tooltip.padding = 12;
                Chart.defaults.plugins.tooltip.cornerRadius = 8;

                Object.keys(window.analyticsCharts).forEach(function(key) {
                    destroyChart(key);
                });

                var responses = await Promise.all([
                    fetchJson('/admin/analytics/summary'),
                    fetchJson('/admin/analytics/providers'),
                    fetchJson('/admin/analytics/devices'),
                    fetchJson('/admin/analytics/browsers'),
                    fetchJson('/admin/analytics/os'),
                    fetchJson('/admin/analytics/locations'),
                    fetchJson('/admin/analytics/registrations/timeline'),
                    fetchJson('/admin/analytics/logins/timeline')
                ]);

                if (runToken !== window.analyticsInitToken) {
                    return;
                }

                var summary = responses[0] || {};
                var providers = responses[1] || [];
                var devices = responses[2] || [];
                var browsers = responses[3] || [];
                var os = responses[4] || [];
                var locations = responses[5] || [];
                var registrations = responses[6] || [];
                var logins = responses[7] || [];

                document.getElementById('stat-total-users').textContent = Number(summary.total_users || 0).toLocaleString();
                document.getElementById('stat-logins-today').textContent = Number(summary.logins_today || 0).toLocaleString();
                document.getElementById('stat-countries').textContent = Number(summary.countries_reached || 0).toLocaleString();
                document.getElementById('stat-mobile').textContent = Number(summary.mobile_percent || 0).toFixed(0) + '%';

                var regSeries = numericSeries(registrations, 'total');
                var loginSeries = numericSeries(logins, 'total');
                var countrySeries = numericSeries(locations, 'total').slice(0, 8);
                var mobileSeries = numericSeries(devices, 'total').slice(0, 8);

                applyTrend('trend-total-users', trendPercent(regSeries));
                applyTrend('trend-logins', trendPercent(loginSeries));
                applyTrend('trend-countries', trendPercent(countrySeries));
                applyTrend('trend-mobile', trendPercent(mobileSeries));

                renderProviderBreakdown(providers);

                var sparkConfigs = [
                    { key: 'spark-users', data: regSeries.slice(-12), color: 'rgba(0, 212, 170, 1)' },
                    { key: 'spark-logins', data: loginSeries.slice(-12), color: 'rgba(246, 201, 14, 1)' },
                    { key: 'spark-countries', data: countrySeries.slice(-12), color: 'rgba(124, 92, 191, 1)' },
                    { key: 'spark-mobile', data: mobileSeries.slice(-12), color: 'rgba(255, 107, 107, 1)' }
                ];

                sparkConfigs.forEach(function(cfg) {
                    var canvas = document.getElementById(cfg.key);
                    if (!canvas) {
                        return;
                    }
                    var ctx = canvas.getContext('2d');
                    var gradient = gradientFill(ctx, cfg.color, '0.45');
                    var points = cfg.data.length ? cfg.data : [0, 0, 0, 0, 0, 0];

                    setChart(cfg.key, new Chart(canvas, {
                        type: 'line',
                        data: {
                            labels: points.map(function(_, i) { return i + 1; }),
                            datasets: [{
                                data: points,
                                borderColor: cfg.color,
                                backgroundColor: gradient,
                                fill: true,
                                borderWidth: 2,
                                pointRadius: 0,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false }, tooltip: { enabled: false } },
                            scales: { x: { display: false }, y: { display: false } }
                        }
                    }));
                });

                var donutPalette = ['#7c5cbf', '#00d4aa', '#f6c90e', '#ff6b6b', '#4fc3f7'];
                var neonBars = ['#7c5cbf', '#00d4aa', '#f6c90e', '#ff6b6b', '#4fc3f7'];

                var totalDevices = numericSeries(devices, 'total').reduce(function(sum, value) {
                    return sum + value;
                }, 0);

                setChart('chart-devices', new Chart(document.getElementById('chart-devices'), {
                    type: 'doughnut',
                    data: {
                        labels: devices.map(function(item) { return item.device_type || 'Unknown'; }),
                        datasets: [{
                            data: numericSeries(devices, 'total'),
                            backgroundColor: devices.map(function(_, idx) { return donutPalette[idx % donutPalette.length]; }),
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { position: 'bottom' },
                            doughnutCenterText: { text: totalDevices.toLocaleString() }
                        }
                    },
                    plugins: [doughnutCenterText]
                }));

                renderLegend('browser-legend', browsers, 'browser', neonBars);
                setChart('chart-browsers', new Chart(document.getElementById('chart-browsers'), {
                    type: 'bar',
                    data: {
                        labels: browsers.map(function(item) { return item.browser || 'Unknown'; }),
                        datasets: [{
                            label: 'Users',
                            data: numericSeries(browsers, 'total'),
                            backgroundColor: browsers.map(function(_, idx) { return neonBars[idx % neonBars.length]; }),
                            borderWidth: 0,
                            barThickness: 'flex',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { color: 'rgba(255,255,255,0.05)' } },
                            y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } }
                        }
                    }
                }));

                renderLegend('os-legend', os, 'os', donutPalette);
                var totalOs = numericSeries(os, 'total').reduce(function(sum, value) {
                    return sum + value;
                }, 0);

                setChart('chart-os', new Chart(document.getElementById('chart-os'), {
                    type: 'doughnut',
                    data: {
                        labels: os.map(function(item) { return item.os || 'Unknown'; }),
                        datasets: [{
                            data: numericSeries(os, 'total'),
                            backgroundColor: os.map(function(_, idx) { return donutPalette[idx % donutPalette.length]; }),
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { position: 'bottom' },
                            doughnutCenterText: { text: totalOs.toLocaleString() }
                        }
                    },
                    plugins: [doughnutCenterText]
                }));

                setChart('chart-locations', new Chart(document.getElementById('chart-locations'), {
                    type: 'bar',
                    data: {
                        labels: locations.map(function(item) {
                            return (item.city || 'Unknown') + ', ' + (item.country || 'N/A');
                        }),
                        datasets: [{
                            label: 'Logins',
                            data: numericSeries(locations, 'total'),
                            backgroundColor: locations.map(function(_, idx) { return neonBars[idx % neonBars.length]; }),
                            borderWidth: 0,
                            barThickness: 'flex',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                            y: { grid: { color: 'rgba(255,255,255,0.05)' } }
                        }
                    }
                }));

                var regCanvas = document.getElementById('registrationChart');
                var regCtx = regCanvas.getContext('2d');
                var regGradient = regCtx.createLinearGradient(0, 0, 0, 300);
                regGradient.addColorStop(0, 'rgba(124,92,191,0.5)');
                regGradient.addColorStop(1, 'rgba(124,92,191,0)');

                setChart('registrationChart', new Chart(regCanvas, {
                    type: 'line',
                    data: {
                        labels: registrations.map(function(item) { return item.date; }),
                        datasets: [{
                            label: 'Registrations',
                            data: numericSeries(registrations, 'total'),
                            borderColor: '#7c5cbf',
                            fill: true,
                            backgroundColor: regGradient,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { color: 'rgba(255,255,255,0.05)' } },
                            y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } }
                        }
                    }
                }));

                var loginCanvas = document.getElementById('loginChart');
                var loginCtx = loginCanvas.getContext('2d');
                var loginGradient = loginCtx.createLinearGradient(0, 0, 0, 300);
                loginGradient.addColorStop(0, 'rgba(0,212,170,0.5)');
                loginGradient.addColorStop(1, 'rgba(0,212,170,0)');

                setChart('loginChart', new Chart(loginCanvas, {
                    type: 'line',
                    data: {
                        labels: logins.map(function(item) { return item.date; }),
                        datasets: [{
                            label: 'Logins',
                            data: numericSeries(logins, 'total'),
                            borderColor: '#00d4aa',
                            fill: true,
                            backgroundColor: loginGradient,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { color: 'rgba(255,255,255,0.05)' } },
                            y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } }
                        }
                    }
                }));
            }
        </script>
    @endpush
</x-app-layout>
