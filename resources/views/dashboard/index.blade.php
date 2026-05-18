@extends('layouts.app')

@section('content')
    <div class="relative min-h-screen overflow-hidden bg-[#F4F6FA]">
        <x-animated-wave-bg />

        <div class="relative z-10 px-8 py-8">
            {{-- HEADER --}}
            <div class="flex flex-col items-center text-center">
                <div class="bg-[#B9C8F0] text-black font-semibold px-16 py-3 rounded-full shadow-sm">
                    Dashboard
                </div>

                <h1 class="mt-8 text-5xl font-extrabold leading-tight">
                    Welcome to GCG Assessment System <br>
                    PT INKA (Persero)
                </h1>

                <div class="flex items-center gap-4 mt-10">
                    <span class="font-bold text-xl">Tahun :</span>

                    <input
                        type="number"
                        id="yearInput"
                        value="{{ (int) $year }}"
                        step="1"
                        inputmode="numeric"
                        class="w-[160px] border-2 border-[#8FA8D6] bg-white px-6 py-3 rounded-full text-lg text-center outline-none focus:ring-2 focus:ring-[#8FA8D6]"
                        onchange="changeYear(this.value)"
                        onkeydown="handleYearEnter(event)"
                    >
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="grid grid-cols-12 gap-8 mt-12 items-start">
                {{-- LEFT --}}
                <div class="col-span-3 space-y-6">
                    @foreach ($leftAspeks as $aspek)
                        @php
                            $chart = $smallCharts[$aspek->id] ?? null;
                            $tooltipText = ($chart['label'] ?? ('ASPEK ' . $aspek->id)) . ' : ' . ($chart['name'] ?? '-');
                        @endphp

                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-5 shadow-md min-h-[250px] flex flex-col">
                            <div class="text-center">
                                <h3 class="text-[28px] font-extrabold cursor-pointer select-none inline-block uppercase leading-none"
                                    data-tooltip="{{ $tooltipText }}">
                                    {{ $chart['label'] ?? ('ASPEK ' . $aspek->id) }}
                                </h3>
                            </div>

                            <div class="mt-5 flex items-center justify-end text-[15px] font-semibold text-gray-600">
                                <span>Progress: {{ $chart['progress'] ?? 0 }}%</span>
                            </div>

                            <div class="mt-3 w-full h-3 bg-[#E5EDF9] rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300"
                                    style="width: {{ $chart['progress'] ?? 0 }}%; background: linear-gradient(90deg, #60A5FA 0%, #38BDF8 100%);">
                                </div>
                            </div>

                            <div class="mt-4 flex-1 relative h-[150px]">
                                <canvas id="smallChart_{{ $aspek->id }}" class="!w-full !h-full"></canvas>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- CENTER --}}
                <div class="col-span-6">
                    <div class="bg-white/90 backdrop-blur-sm rounded-3xl p-6 shadow-md min-h-[560px] flex flex-col">
                        <h2 class="text-center font-extrabold text-[20px] uppercase mb-6">
                            GRAFIK KESELURUHAN ASPEK (Tahun {{ $year }})
                        </h2>

                        <div class="relative flex-1 h-[460px]">
                            <canvas id="bigChart" class="!w-full !h-full"></canvas>
                        </div>
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="col-span-3 space-y-6">
                    @foreach ($rightAspeks as $aspek)
                        @php
                            $chart = $smallCharts[$aspek->id] ?? null;
                            $tooltipText = ($chart['label'] ?? ('ASPEK ' . $aspek->id)) . ' : ' . ($chart['name'] ?? '-');
                        @endphp

                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-5 shadow-md min-h-[250px] flex flex-col">
                            <div class="text-center">
                                <h3 class="text-[28px] font-extrabold cursor-pointer select-none inline-block uppercase leading-none"
                                    data-tooltip="{{ $tooltipText }}">
                                    {{ $chart['label'] ?? ('ASPEK ' . $aspek->id) }}
                                </h3>
                            </div>

                            <div class="mt-5 flex items-center justify-end text-[15px] font-semibold text-gray-600">
                                <span>Progress: {{ $chart['progress'] ?? 0 }}%</span>
                            </div>

                            <div class="mt-3 w-full h-3 bg-[#E5EDF9] rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300"
                                    style="width: {{ $chart['progress'] ?? 0 }}%; background: linear-gradient(90deg, #60A5FA 0%, #38BDF8 100%);">
                                </div>
                            </div>

                            <div class="mt-4 flex-1 relative h-[150px]">
                                <canvas id="smallChart_{{ $aspek->id }}" class="!w-full !h-full"></canvas>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-18px) translateX(10px); }
        }

        @keyframes floatMedium {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-14px) translateX(-12px); }
        }

        @keyframes floatFast {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(-10px) translateX(14px); }
        }

        @keyframes waveMoveX {
            0% { transform: translateX(0); }
            50% { transform: translateX(-40px); }
            100% { transform: translateX(0); }
        }

        @keyframes waveMoveXReverse {
            0% { transform: translateX(0); }
            50% { transform: translateX(40px); }
            100% { transform: translateX(0); }
        }

        @keyframes waveLineMove {
            0% { transform: translateX(0); }
            50% { transform: translateX(-25px); }
            100% { transform: translateX(0); }
        }

        .animate-float-slow {
            animation: floatSlow 8s ease-in-out infinite;
        }

        .animate-float-medium {
            animation: floatMedium 6s ease-in-out infinite;
        }

        .animate-float-fast {
            animation: floatFast 4.5s ease-in-out infinite;
        }

        .animate-wave-x {
            animation: waveMoveX 9s ease-in-out infinite;
        }

        .animate-wave-x-reverse {
            animation: waveMoveXReverse 11s ease-in-out infinite;
        }

        .animate-wave-line {
            animation: waveLineMove 7s ease-in-out infinite;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function changeYear(year) {
            year = String(year).trim();

            if (year === '' || isNaN(year)) {
                return;
            }

            window.location.href = "{{ route('dashboard') }}?year=" + encodeURIComponent(year);
        }

        function handleYearEnter(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                changeYear(event.target.value);
            }
        }

        // TOOLTIP
        (function () {
            if (document.getElementById('globalTooltip')) return;

            const tip = document.createElement('div');
            tip.id = 'globalTooltip';
            tip.style.position = 'fixed';
            tip.style.zIndex = '999999';
            tip.style.maxWidth = '360px';
            tip.style.background = 'rgba(0,0,0,0.90)';
            tip.style.color = '#fff';
            tip.style.fontSize = '12px';
            tip.style.fontFamily = 'Poppins, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial';
            tip.style.fontWeight = '700';
            tip.style.lineHeight = '1.35';
            tip.style.padding = '10px 12px';
            tip.style.borderRadius = '10px';
            tip.style.boxShadow = '0 10px 25px rgba(0,0,0,0.25)';
            tip.style.pointerEvents = 'none';
            tip.style.opacity = '0';
            tip.style.transform = 'translateY(4px)';
            tip.style.transition = 'opacity 150ms ease, transform 150ms ease';
            document.body.appendChild(tip);

            let activeEl = null;

            function showTooltip(el) {
                const text = el.getAttribute('data-tooltip');
                if (!text) return;

                activeEl = el;
                tip.textContent = text;
                tip.style.opacity = '1';
                tip.style.transform = 'translateY(0)';
                positionTooltip(el);
            }

            function hideTooltip() {
                activeEl = null;
                tip.style.opacity = '0';
                tip.style.transform = 'translateY(4px)';
            }

            function positionTooltip(el) {
                const rect = el.getBoundingClientRect();
                const tipRect = tip.getBoundingClientRect();

                let top = rect.bottom + 10;
                let left = rect.left + (rect.width / 2) - (tipRect.width / 2);

                const padding = 10;
                if (left < padding) left = padding;
                if (left + tipRect.width > window.innerWidth - padding) {
                    left = window.innerWidth - padding - tipRect.width;
                }

                if (top + tipRect.height > window.innerHeight - padding) {
                    top = rect.top - tipRect.height - 10;
                }

                if (top < padding) top = padding;

                tip.style.left = left + 'px';
                tip.style.top = top + 'px';
            }

            document.querySelectorAll('[data-tooltip]').forEach(el => {
                el.addEventListener('mouseenter', () => showTooltip(el));
                el.addEventListener('mousemove', () => {
                    if (activeEl === el) positionTooltip(el);
                });
                el.addEventListener('mouseleave', hideTooltip);

                el.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (activeEl === el) hideTooltip();
                    else showTooltip(el);
                });
            });

            document.addEventListener('click', () => hideTooltip());
            window.addEventListener('scroll', () => {
                if (activeEl) positionTooltip(activeEl);
            }, true);
            window.addEventListener('resize', () => {
                if (activeEl) positionTooltip(activeEl);
            });
        })();

        const selectedYear = @json((int) $year);
        const bigChartLabels = @json($bigChartLabels);
        const bigChartData = @json($bigChartData);
        const smallCharts = @json($smallCharts);

        function getNiceMax(values) {
            const nums = (values || []).map(v => Number(v) || 0);
            const maxVal = Math.max(...nums, 0);

            if (maxVal <= 1) return 1;
            if (maxVal <= 2) return 2;
            if (maxVal <= 5) return 5;
            if (maxVal <= 10) return 10;
            if (maxVal <= 20) return 20;
            if (maxVal <= 50) return 50;
            if (maxVal <= 100) return 100;

            return Math.ceil(maxVal / 10) * 10;
        }

        function getStepSize(maxVal) {
            if (maxVal <= 1) return 0.2;
            if (maxVal <= 2) return 0.5;
            if (maxVal <= 5) return 1;
            if (maxVal <= 10) return 2;
            if (maxVal <= 20) return 5;
            if (maxVal <= 50) return 10;
            return Math.ceil(maxVal / 5);
        }

        const bigChartMax = getNiceMax(bigChartData);
        const bigChartStep = getStepSize(bigChartMax);

        new Chart(document.getElementById('bigChart'), {
            type: 'bar',
            data: {
                labels: bigChartLabels,
                datasets: [{
                    label: 'Nilai Tahun ' + selectedYear,
                    data: bigChartData,
                    borderWidth: 1,
                    backgroundColor: 'rgba(96, 165, 250, 0.55)',
                    borderColor: 'rgba(59, 130, 246, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 10,
                        right: 10,
                        bottom: 0,
                        left: 10
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 14,
                                weight: '600'
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 14,
                                weight: '600'
                            }
                        },
                        grid: {
                            display: true
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: bigChartMax,
                        ticks: {
                            stepSize: bigChartStep,
                            font: {
                                size: 13
                            }
                        }
                    }
                }
            }
        });

        Object.keys(smallCharts).forEach(function (aspekId) {
            const el = document.getElementById('smallChart_' + aspekId);
            if (!el) return;

            const dataObj = smallCharts[aspekId] || {};
            const labels = dataObj.years || [];
            const scores = dataObj.scores || [];

            const chartMax = getNiceMax(scores);
            const chartStep = getStepSize(chartMax);

            new Chart(el, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: dataObj.label || 'Trend',
                        data: scores,
                        borderWidth: 3,
                        tension: 0.3,
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointBorderColor: 'rgba(59, 130, 246, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 5,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 6,
                            right: 6,
                            bottom: 0,
                            left: 0
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            },
                            grid: {
                                display: true
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: chartMax,
                            ticks: {
                                stepSize: chartStep,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush