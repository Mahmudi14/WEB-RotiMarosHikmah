@extends('layouts.master')

@section('page_title', 'Dashboard Keuangan')

@section('content')
    @php
        $topProductSummary = $topProducts->first();

        $chartMetric = $filters['chart_metric'];
        $chartMetricLabel = $chartMetrics[$chartMetric] ?? 'Pendapatan Bersih';

        $chartValueKey = match ($chartMetric) {
            'gross_sales' => 'total_pendapatan',
            'expenses' => 'total_pengeluaran',
            default => 'pendapatan_bersih',
        };

        $chartBarClass = match ($chartMetric) {
            'gross_sales' => 'bg-[#1F444C]',
            'expenses' => 'bg-[#A92A35]',
            default => 'bg-[#F4B044]',
        };

        $chartMaxValue = max(
            (float) $incomeChart->map(fn($row) => abs((float) data_get($row, $chartValueKey, 0)))->max(),
            1,
        );

        $maxCashierIncome = max((float) $cashierPerformance->max('total_pendapatan'), 1);
        $totalExpenseBreakdown = max((float) $expenseBreakdown->sum('total_nominal'), 1);

        $compactRupiah = function ($value) {
            $value = abs((float) $value);

            if ($value >= 1000000000) {
                return rtrim(rtrim(number_format($value / 1000000000, 1, ',', '.'), '0'), ',') . 'M';
            }

            if ($value >= 1000000) {
                return rtrim(rtrim(number_format($value / 1000000, 1, ',', '.'), '0'), ',') . 'JT';
            }

            if ($value >= 1000) {
                return rtrim(rtrim(number_format($value / 1000, 1, ',', '.'), '0'), ',') . 'K';
            }

            return number_format($value, 0, ',', '.');
        };
    @endphp

    <div class="space-y-6">
        {{-- Header --}}
        <div class="overflow-hidden rounded-3xl bg-[#1F444C] p-6 text-white shadow-lg shadow-[#1F444C]/10">
            <div class="relative">
                <div class="absolute -right-10 -top-12 h-32 w-32 rounded-full bg-[#F4B044]/20"></div>
                <div class="absolute -bottom-16 -left-12 h-36 w-36 rounded-full bg-white/10"></div>

                <div
                    class="relative flex flex-col gap-5 min-[1024px]:flex-row min-[1024px]:items-center min-[1024px]:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-[#F4D3B0]">
                            Keuangan / Analisis
                        </p>

                        <h1 class="mt-2 text-2xl font-black tracking-tight">
                            Analisis Pendapatan
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm font-medium leading-relaxed text-[#F7F6F4]/80">
                            Lihat penjualan, pendapatan bersih, kasir, menu terlaris, dan pengeluaran berdasarkan tampilan
                            data.
                        </p>
                    </div>

                    <a href="{{ route('admin.transactions.index') }}"
                        class="inline-flex h-12 items-center justify-center rounded-2xl bg-[#F4B044] px-5 text-sm font-black text-[#2B1A10] shadow-lg shadow-[#F4B044]/20 transition hover:-translate-y-0.5 hover:shadow-xl min-[1024px]:shrink-0">
                        Riwayat Transaksi
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]"
            x-data="{
                cashierOpen: false,
                chartViewOpen: false,
                yearOpen: false,
                monthOpen: false,
            
                selectedCashier: @js((string) ($filters['cashier_id'] ?? '')),
                selectedChartView: @js($filters['chart_view']),
                selectedYear: @js((string) ($filters['year'] ?? '')),
                selectedMonth: @js((string) ($filters['month'] ?? '')),
            
                cashiers: @js($cashiers->mapWithKeys(fn($cashier) => [(string) $cashier->id => $cashier->name])->toArray()),
                chartViews: @js($chartViews),
                years: @js($years->mapWithKeys(fn($year) => [(string) $year => (string) $year])->toArray()),
                months: @js(collect($months)->mapWithKeys(fn($label, $value) => [(string) $value => $label])->toArray()),
            
                currentYear: @js((string) now()->year),
                currentMonth: @js((string) now()->month),
            
                get selectedCashierLabel() {
                    return this.selectedCashier ? this.cashiers[this.selectedCashier] : 'Semua Kasir';
                },
            
                get selectedChartViewLabel() {
                    return this.chartViews[this.selectedChartView] || 'Per Bulan';
                },
            
                get selectedYearLabel() {
                    return this.selectedYear ? this.years[this.selectedYear] : 'Semua Tahun';
                },
            
                get selectedMonthLabel() {
                    return this.selectedMonth ? this.months[this.selectedMonth] : 'Semua Bulan';
                },
            
                get showYearFilter() {
                    return ['daily', 'monthly', 'overall'].includes(this.selectedChartView);
                },
            
                get showMonthFilter() {
                    return ['daily', 'overall'].includes(this.selectedChartView);
                },
            
                closeDropdowns(except = '') {
                    if (except !== 'cashier') this.cashierOpen = false;
                    if (except !== 'chartView') this.chartViewOpen = false;
                    if (except !== 'year') this.yearOpen = false;
                    if (except !== 'month') this.monthOpen = false;
                },
            
                chooseChartView(value) {
                    this.selectedChartView = value;
            
                    if (value === 'daily') {
                        if (!this.selectedYear) this.selectedYear = this.currentYear;
                        if (!this.selectedMonth) this.selectedMonth = this.currentMonth;
                    }
            
                    if (value === 'monthly') {
                        if (!this.selectedYear) this.selectedYear = this.currentYear;
                        this.selectedMonth = '';
                    }
            
                    if (value === 'yearly') {
                        this.selectedYear = '';
                        this.selectedMonth = '';
                    }
            
                    this.chartViewOpen = false;
                }
            }">
            <form method="GET" action="{{ route('finance.dashboard') }}" class="space-y-3">
                <input id="chart_metric_input" type="hidden" name="chart_metric" value="{{ $filters['chart_metric'] }}">

                {{-- Row 1: Kasir | Terapkan | Reset --}}
                <div
                    class="grid gap-3 min-[1024px]:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)_180px_160px] min-[1024px]:items-center">
                    {{-- Info Scope --}}
                    <div class="min-w-0 rounded-2xl bg-[#F7F6F4] px-4 py-3">
                        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#6B3E12]/70">
                            Ruang Lingkup
                        </p>

                        <p class="mt-1 truncate text-sm font-black text-[#2B1A10]">
                            {{ $scopeLabel }}
                        </p>
                    </div>
                    {{-- Kasir --}}
                    <div class="min-w-0">
                        <input type="hidden" name="cashier_id" x-model="selectedCashier">

                        <div class="relative">
                            <button type="button" @click="cashierOpen = !cashierOpen; closeDropdowns('cashier')"
                                class="flex h-12 w-full items-center justify-between gap-3 rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0" />
                                        </svg>
                                    </span>

                                    <div class="min-w-0">
                                        <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#6B3E12]/70">
                                            Kasir
                                        </p>

                                        <p class="truncate text-sm font-black text-[#2B1A10]" x-text="selectedCashierLabel">
                                        </p>
                                    </div>
                                </div>

                                <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition"
                                    :class="cashierOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    stroke-width="2.4" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="cashierOpen" x-cloak @click.outside="cashierOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute z-40 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white p-2 shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                                <button type="button" @click="selectedCashier = ''; cashierOpen = false"
                                    class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedCashier === ''
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                    <span>Semua Kasir</span>

                                    <svg x-show="selectedCashier === ''" x-cloak class="h-5 w-5" fill="none"
                                        stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                @foreach ($cashiers as $cashier)
                                    <button type="button"
                                        @click="selectedCashier = @js((string) $cashier->id); cashierOpen = false"
                                        class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedCashier === @js((string) $cashier->id) ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span>{{ $cashier->name }}</span>

                                        <svg x-show="selectedCashier === @js((string) $cashier->id)" x-cloak
                                            class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.8"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Terapkan --}}
                    <button type="submit"
                        class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-[#1F444C] px-5 text-sm font-black text-white shadow-lg shadow-[#1F444C]/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Terapkan
                    </button>

                    {{-- Reset --}}
                    <a href="{{ route('finance.dashboard') }}"
                        class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl border border-[#F4D3B0] bg-white px-5 text-sm font-black text-[#6B3E12] transition hover:bg-[#F7F6F4]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0013.803-3.7M7.977 14.652H2.985m18.03-5.304-3.181-3.183a8.25 8.25 0 00-13.803 3.7" />
                        </svg>
                        Reset
                    </a>
                </div>

                {{-- Row 2: Bulan | Tahun | Tampilan --}}
                <div class="grid gap-3 md:grid-cols-3">
                    {{-- Bulan - kiri --}}
                    <div class="min-w-0 md:col-start-1" x-show="showMonthFilter" x-cloak>
                        <input type="hidden" name="month" x-model="selectedMonth">

                        <div class="relative">
                            <button type="button" @click="monthOpen = !monthOpen; closeDropdowns('month')"
                                class="flex h-12 w-full items-center justify-between gap-3 rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                                        </svg>
                                    </span>

                                    <div class="min-w-0">
                                        <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#6B3E12]/70">
                                            Bulan
                                        </p>

                                        <p class="truncate text-sm font-black text-[#2B1A10]" x-text="selectedMonthLabel">
                                        </p>
                                    </div>
                                </div>

                                <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition"
                                    :class="monthOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    stroke-width="2.4" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="monthOpen" x-cloak @click.outside="monthOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute z-40 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white p-2 shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                                <button type="button" x-show="selectedChartView === 'overall'"
                                    @click="selectedMonth = ''; monthOpen = false"
                                    class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedMonth === ''
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                    <span>Semua Bulan</span>

                                    <svg x-show="selectedMonth === ''" x-cloak class="h-5 w-5" fill="none"
                                        stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                @foreach ($months as $value => $label)
                                    <button type="button"
                                        @click="selectedMonth = @js((string) $value); monthOpen = false"
                                        class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedMonth === @js((string) $value) ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span>{{ $label }}</span>

                                        <svg x-show="selectedMonth === @js((string) $value)" x-cloak
                                            class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.8"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Tahun - tengah --}}
                    <div class="min-w-0 md:col-start-2" x-show="showYearFilter" x-cloak>
                        <input type="hidden" name="year" x-model="selectedYear">

                        <div class="relative">
                            <button type="button" @click="yearOpen = !yearOpen; closeDropdowns('year')"
                                class="flex h-12 w-full items-center justify-between gap-3 rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6.75h15A1.5 1.5 0 0121 8.25v10.5a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 18.75V8.25a1.5 1.5 0 011.5-1.5z" />
                                        </svg>
                                    </span>

                                    <div class="min-w-0">
                                        <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#6B3E12]/70">
                                            Tahun
                                        </p>

                                        <p class="truncate text-sm font-black text-[#2B1A10]" x-text="selectedYearLabel">
                                        </p>
                                    </div>
                                </div>

                                <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition"
                                    :class="yearOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    stroke-width="2.4" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="yearOpen" x-cloak @click.outside="yearOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute z-40 mt-3 max-h-72 w-full overflow-y-auto rounded-2xl border border-[#F4D3B0]/80 bg-white p-2 shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                                <button type="button" x-show="selectedChartView === 'overall'"
                                    @click="selectedYear = ''; selectedMonth = ''; yearOpen = false"
                                    class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                    :class="selectedYear === ''
                                        ?
                                        'bg-[#F4B044] text-[#2B1A10]' :
                                        'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                    <span>Semua Tahun</span>

                                    <svg x-show="selectedYear === ''" x-cloak class="h-5 w-5" fill="none"
                                        stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                @foreach ($years as $year)
                                    <button type="button"
                                        @click="selectedYear = @js((string) $year); yearOpen = false"
                                        class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedYear === @js((string) $year) ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span>{{ $year }}</span>

                                        <svg x-show="selectedYear === @js((string) $year)" x-cloak
                                            class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.8"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Tampilan - kanan --}}
                    <div class="min-w-0 md:col-start-3">
                        <input type="hidden" name="chart_view" x-model="selectedChartView">

                        <div class="relative">
                            <button type="button" @click="chartViewOpen = !chartViewOpen; closeDropdowns('chartView')"
                                class="flex h-12 w-full items-center justify-between gap-3 rounded-2xl border border-[#F4D3B0] bg-[#F7F6F4] px-4 text-left text-sm font-bold text-[#2B1A10] shadow-sm transition hover:bg-white focus:border-[#F4B044] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#F4B044]/20">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#F4B044]/20 text-[#6B3E12]">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 3v18h18M7 15h2v3H7v-3Zm4-6h2v9h-2V9Zm4 3h2v6h-2v-6Z" />
                                        </svg>
                                    </span>

                                    <div class="min-w-0">
                                        <p class="text-[10px] font-black uppercase tracking-[0.16em] text-[#6B3E12]/70">
                                            Tampilan
                                        </p>

                                        <p class="truncate text-sm font-black text-[#2B1A10]"
                                            x-text="selectedChartViewLabel"></p>
                                    </div>
                                </div>

                                <svg class="h-5 w-5 shrink-0 text-[#6B3E12] transition"
                                    :class="chartViewOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    stroke-width="2.4" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="chartViewOpen" x-cloak @click.outside="chartViewOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute z-40 mt-3 w-full overflow-hidden rounded-2xl border border-[#F4D3B0]/80 bg-white p-2 shadow-[0_25px_70px_-35px_rgba(31,68,76,0.55)]">
                                @foreach ($chartViews as $value => $label)
                                    <button type="button" @click="chooseChartView(@js($value))"
                                        class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-left text-sm font-bold transition"
                                        :class="selectedChartView === @js($value) ?
                                            'bg-[#F4B044] text-[#2B1A10]' :
                                            'text-[#2B1A10] hover:bg-[#F4B044]/15'">
                                        <span>{{ $label }}</span>

                                        <svg x-show="selectedChartView === @js($value)" x-cloak
                                            class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.8"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid gap-4 md:grid-cols-2 min-[1280px]:grid-cols-4">
            <div
                class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">Total Penjualan</p>
                <p class="mt-3 text-xl font-black text-[#1F444C]">Rp
                    {{ number_format($summary['gross_sales'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                    {{ number_format($summary['transactions'], 0, ',', '.') }} transaksi selesai</p>
            </div>

            <div
                class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">Pengeluaran</p>
                <p class="mt-3 text-xl font-black text-[#A92A35]">Rp
                    {{ number_format($summary['expenses'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">Pengeluaran pada ruang lingkup ini</p>
            </div>

            <div
                class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">Pendapatan Bersih</p>
                <p
                    class="mt-3 text-xl font-black {{ $summary['net_income'] >= 0 ? 'text-[#1F444C]' : 'text-[#A92A35]' }}">
                    Rp {{ number_format($summary['net_income'], 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">Penjualan - pengeluaran</p>
            </div>

            <div
                class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">Rata-rata Transaksi</p>
                <p class="mt-3 text-xl font-black text-[#1F444C]">Rp
                    {{ number_format($summary['average_transaction'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs font-semibold text-[#6B3E12]">Rata-rata transaksi selesai</p>
            </div>

            <div
                class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">Tunai</p>
                <p class="mt-3 text-xl font-black text-[#1F444C]">Rp
                    {{ number_format($summary['cash_sales'], 0, ',', '.') }}</p>
            </div>

            <div
                class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">QRIS</p>
                <p class="mt-3 text-xl font-black text-[#1F444C]">Rp
                    {{ number_format($summary['qris_sales'], 0, ',', '.') }}</p>
            </div>

            <div
                class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">Transfer</p>
                <p class="mt-3 text-xl font-black text-[#1F444C]">Rp
                    {{ number_format($summary['transfer_sales'], 0, ',', '.') }}</p>
            </div>

            <div
                class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">Diskon & Pajak</p>
                <p class="mt-3 text-sm font-black text-[#A92A35]">Diskon: Rp
                    {{ number_format($summary['discounts'], 0, ',', '.') }}</p>
                <p class="mt-1 text-sm font-black text-[#1F444C]">Pajak: Rp
                    {{ number_format($summary['taxes'], 0, ',', '.') }}</p>
            </div>

            <div
                class="rounded-3xl border border-[#F4D3B0]/70 bg-white p-5 shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)] md:col-span-2 min-[1280px]:col-span-4">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-[#6B3E12]">Menu Paling Laris</p>

                @if ($topProductSummary)
                    <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xl font-black text-[#2B1A10]">{{ $topProductSummary->nama_produk }}</p>
                            <p class="mt-1 text-sm font-semibold text-[#6B3E12]">
                                {{ $topProductSummary->nama_kategori ?: '-' }}</p>
                        </div>

                        <div class="text-left sm:text-right">
                            <p class="text-lg font-black text-[#1F444C]">
                                {{ number_format((int) $topProductSummary->total_qty, 0, ',', '.') }}x terjual</p>
                            <p class="mt-1 text-sm font-black text-[#6B3E12]">Rp
                                {{ number_format((float) $topProductSummary->total_subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @else
                    <p class="mt-3 text-sm font-semibold text-[#6B3E12]">Belum ada produk terjual pada ruang lingkup ini.
                    </p>
                @endif
            </div>
        </div>

        {{-- Vertical Bar Chart --}}
        <div x-data="{
            selectedMetric: @js($filters['chart_metric']),
            chartView: @js($filters['chart_view']),
            rows: @js(
    $incomeChart
        ->values()
        ->map(
            fn($row) => [
                'label' => $row->label,
                'total_transaksi' => (int) $row->total_transaksi,
                'total_pendapatan' => (float) $row->total_pendapatan,
                'total_pengeluaran' => (float) $row->total_pengeluaran,
                'pendapatan_bersih' => (float) $row->pendapatan_bersih,
            ],
        )
        ->all(),
),
        
            metrics: {
                net_income: {
                    label: 'Pendapatan Bersih',
                    key: 'pendapatan_bersih',
                    color: 'bg-[#F4B044]',
                },
                expenses: {
                    label: 'Pengeluaran',
                    key: 'total_pengeluaran',
                    color: 'bg-[#A92A35]',
                },
                gross_sales: {
                    label: 'Pendapatan Kotor',
                    key: 'total_pendapatan',
                    color: 'bg-[#1F444C]',
                },
            },
        
            get activeMetric() {
                return this.metrics[this.selectedMetric] || this.metrics.net_income;
            },
        
            get maxValue() {
                const values = this.rows.map((row) => Math.abs(Number(row[this.activeMetric.key]) || 0));
                return Math.max(...values, 1);
            },
        
            get shouldFitChart() {
                return this.chartView === 'monthly' || this.rows.length <= 12;
            },
        
            get chartColumnWidth() {
                if (this.shouldFitChart) {
                    return null;
                }
        
                if (this.rows.length <= 24) {
                    return 50;
                }
        
                return 44;
            },
        
            get chartBarWidth() {
                if (this.chartView === 'monthly') {
                    return 30;
                }
        
                if (this.rows.length <= 12) {
                    return 30;
                }
        
                if (this.rows.length <= 24) {
                    return 24;
                }
        
                return 20;
            },
        
            get chartGap() {
                if (this.shouldFitChart) {
                    return 0;
                }
        
                if (this.rows.length <= 24) {
                    return 3;
                }
        
                return 2;
            },
        
            get chartContentStyle() {
                if (this.shouldFitChart) {
                    return 'width: 100%; min-width: 100%;';
                }
        
                const totalWidth =
                    (this.rows.length * this.chartColumnWidth) +
                    (Math.max(this.rows.length - 1, 0) * this.chartGap) +
                    48;
        
                return `width: ${totalWidth}px; min-width: ${totalWidth}px;`;
            },
        
            get chartColumnStyle() {
                if (this.shouldFitChart) {
                    return `width: calc(100% / ${Math.max(this.rows.length, 1)});`;
                }
        
                return `width: ${this.chartColumnWidth}px;`;
            },
        
            get chartContentWidth() {
                return (this.rows.length * this.chartColumnWidth) +
                    (Math.max(this.rows.length - 1, 0) * this.chartGap);
            },
        
            get chartFrameWidth() {
                // 48 = padding kanan kiri dari px-6
                return this.chartContentWidth + 48;
            },
        
            displayLabel(row) {
                let label = String(row.label || '');
        
                if (this.chartView === 'monthly') {
                    return label.replace(/\s+\d{4}$/, '');
                }
        
                return label;
            },
        
            value(row) {
                return Number(row[this.activeMetric.key]) || 0;
            },
        
            barHeight(row) {
                const value = Math.abs(this.value(row));
        
                if (value === 0) {
                    return 0;
                }
        
                return Math.max(4, Math.min(100, (value / this.maxValue) * 100));
            },
        
            barClass(row) {
                return this.value(row) < 0 ? 'bg-[#A92A35]' : this.activeMetric.color;
            },
        
            rupiah(value) {
                return new Intl.NumberFormat('id-ID').format(Math.round(Math.abs(Number(value) || 0)));
            },
        
            compactRupiah(value) {
                value = Math.abs(Number(value) || 0);
        
                if (value >= 1000000000) {
                    return (value / 1000000000).toFixed(value % 1000000000 === 0 ? 0 : 1).replace('.', ',') + 'M';
                }
        
                if (value >= 1000000) {
                    return (value / 1000000).toFixed(value % 1000000 === 0 ? 0 : 1).replace('.', ',') + 'JT';
                }
        
                if (value >= 1000) {
                    return (value / 1000).toFixed(value % 1000 === 0 ? 0 : 1).replace('.', ',') + 'K';
                }
        
                return new Intl.NumberFormat('id-ID').format(value);
            },
        
            setMetric(metric) {
                this.selectedMetric = metric;
        
                const input = document.getElementById('chart_metric_input');
        
                if (input) {
                    input.value = metric;
                }
        
                const url = new URL(window.location.href);
                url.searchParams.set('chart_metric', metric);
                window.history.replaceState({}, '', url);
            }
        }"
            class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
            <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                <div
                    class="flex flex-col gap-4 min-[1024px]:flex-row min-[1024px]:items-center min-[1024px]:justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">
                            Grafik Batang
                        </p>

                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">
                            Grafik <span x-text="activeMetric.label"></span> - {{ $scopeLabel }}
                        </h2>
                    </div>

                    {{-- Metric switch tanpa reload --}}
                    <div class="flex w-fit flex-wrap gap-2 rounded-2xl bg-white p-1 shadow-sm">
                        <template x-for="(metric, key) in metrics" :key="key">
                            <button type="button" @click="setMetric(key)"
                                class="inline-flex h-10 items-center justify-center rounded-xl px-4 text-xs font-black transition"
                                :class="selectedMetric === key ?
                                    'bg-[#F4B044] text-[#2B1A10] shadow-md shadow-[#F4B044]/20' :
                                    'text-[#6B3E12] hover:bg-[#F7F6F4]'"
                                x-text="metric.label">
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <div class="p-5">
                <template x-if="rows.length > 0">
                    <div class="overflow-x-auto overscroll-x-contain">
                        <div class="pb-2" :style="chartContentStyle">
                            <div
                                class="relative overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white px-6 pb-8 pt-8">

                                {{-- Grid background --}}
                                <div class="pointer-events-none absolute inset-x-6 bottom-[96px] top-8 rounded-2xl"
                                    style="background-image: linear-gradient(to top, rgba(244, 211, 176, 0.65) 1px, transparent 1px); background-size: 100% 20%;">
                                </div>

                                {{-- Bars --}}
                                <div class="relative z-10 flex h-[330px] w-full items-end" :style="`gap: ${chartGap}px`">
                                    <template x-for="row in rows" :key="row.label">
                                        <div class="flex min-w-0 shrink-0 flex-col items-center justify-end"
                                            :style="chartColumnStyle">
                                            {{-- Bar --}}
                                            <div class="flex h-[235px] w-full items-end justify-center">
                                                <div class="group relative flex h-full items-end justify-center"
                                                    :style="`width: ${chartBarWidth}px`">
                                                    <div class="w-full rounded-t-lg transition hover:opacity-80"
                                                        :class="barClass(row)" :style="`height: ${barHeight(row)}%`">
                                                    </div>

                                                    {{-- Tooltip --}}
                                                    <div
                                                        class="pointer-events-none absolute bottom-full left-1/2 z-30 mb-3 hidden w-max -translate-x-1/2 rounded-xl bg-[#2B1A10] px-3 py-2 text-xs font-black text-white shadow-xl group-hover:block">
                                                        <span x-text="activeMetric.label"></span>:
                                                        <span
                                                            x-text="`${value(row) < 0 ? '-' : ''}Rp ${rupiah(value(row))}`"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Value under bar --}}
                                            <div class="mt-2 h-5 text-center">
                                                <template x-if="value(row) !== 0">
                                                    <p class="whitespace-nowrap text-[11px] font-black"
                                                        :class="value(row) < 0 ? 'text-[#A92A35]' : 'text-[#2B1A10]'"
                                                        x-text="`${value(row) < 0 ? '-' : ''}${compactRupiah(value(row))}`">
                                                    </p>
                                                </template>

                                                <template x-if="value(row) === 0">
                                                    <p class="text-[11px] font-black text-[#6B3E12]/50">
                                                        -
                                                    </p>
                                                </template>
                                            </div>

                                            {{-- X label --}}
                                            <div class="mt-2 w-full border-t border-[#F4D3B0]/70 pt-3 text-center">
                                                <p class="w-full truncate px-0.5 text-center text-[11px] font-black text-[#2B1A10]"
                                                    x-text="displayLabel(row)">
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Legend --}}
                            <div class="mt-5 flex flex-wrap items-center justify-center gap-4">
                                <div class="inline-flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-sm" :class="activeMetric.color"></span>

                                    <span class="text-xs font-black text-[#6B3E12]" x-text="activeMetric.label"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="rows.length === 0">
                    <div class="flex min-h-[260px] items-center justify-center text-center">
                        <div>
                            <h3 class="text-base font-black text-[#2B1A10]">
                                Belum Ada Data Grafik
                            </h3>

                            <p class="mt-2 text-sm font-semibold text-[#6B3E12]">
                                Data akan muncul setelah ada transaksi atau pengeluaran.
                            </p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Bottom Content --}}
        <div class="grid gap-6 min-[1024px]:grid-cols-[minmax(0,1fr)_420px]">
            {{-- Cashier Performance --}}
            <div
                class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                    <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">Kasir</p>
                    <h2 class="mt-1 text-lg font-black text-[#2B1A10]">Performa Kasir</h2>
                </div>

                <div class="divide-y divide-[#F4D3B0]/60">
                    @forelse ($cashierPerformance as $cashier)
                        @php
                            $barWidth = min(100, ((float) $cashier->total_pendapatan / $maxCashierIncome) * 100);
                        @endphp

                        <div class="px-5 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-black text-[#2B1A10]">{{ $cashier->cashier_name }}</p>
                                    <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                        {{ number_format($cashier->total_transaksi, 0, ',', '.') }} transaksi
                                    </p>
                                </div>

                                <p class="shrink-0 whitespace-nowrap text-sm font-black text-[#1F444C]">
                                    Rp {{ number_format((float) $cashier->total_pendapatan, 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#F4D3B0]/45">
                                <div class="h-full rounded-full bg-[#1F444C]" style="width: {{ $barWidth }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center">
                            <p class="text-sm font-semibold text-[#6B3E12]">Belum ada performa kasir.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Side --}}
            <div class="space-y-6">
                {{-- Top Products --}}
                <div
                    class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">Produk</p>
                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">Produk Terlaris</h2>
                    </div>

                    <div class="divide-y divide-[#F4D3B0]/60">
                        @forelse ($topProducts as $product)
                            <div class="flex items-start justify-between gap-3 px-5 py-4">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-black text-[#2B1A10]">{{ $product->nama_produk }}</p>
                                    <p class="mt-1 truncate text-xs font-semibold text-[#6B3E12]">
                                        {{ $product->nama_kategori ?: '-' }}</p>
                                </div>

                                <div class="shrink-0 text-right">
                                    <p class="text-sm font-black text-[#1F444C]">
                                        {{ number_format((int) $product->total_qty, 0, ',', '.') }}x</p>
                                    <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                        Rp {{ number_format((float) $product->total_subtotal, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-10 text-center">
                                <p class="text-sm font-semibold text-[#6B3E12]">Belum ada produk terjual.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Expense Breakdown --}}
                <div
                    class="overflow-hidden rounded-3xl border border-[#F4D3B0]/70 bg-white shadow-[0_20px_60px_-35px_rgba(31,68,76,0.45)]">
                    <div class="border-b border-[#F4D3B0]/70 bg-[#F7F6F4] px-5 py-4">
                        <p class="text-xs font-black uppercase tracking-[0.22em] text-[#6B3E12]">Pengeluaran</p>
                        <h2 class="mt-1 text-lg font-black text-[#2B1A10]">Rincian Pengeluaran</h2>
                    </div>

                    <div class="divide-y divide-[#F4D3B0]/60">
                        @forelse ($expenseBreakdown as $expense)
                            @php
                                $percentage = min(
                                    100,
                                    ((float) $expense->total_nominal / $totalExpenseBreakdown) * 100,
                                );
                            @endphp

                            <div class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-black text-[#2B1A10]">
                                            {{ $expense->kategori_pengeluaran }}</p>
                                        <p class="mt-1 text-xs font-semibold text-[#6B3E12]">
                                            {{ number_format($expense->total_data, 0, ',', '.') }} data
                                        </p>
                                    </div>

                                    <p class="shrink-0 whitespace-nowrap text-sm font-black text-[#A92A35]">
                                        Rp {{ number_format((float) $expense->total_nominal, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#A92A35]/10">
                                    <div class="h-full rounded-full bg-[#A92A35]" style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-10 text-center">
                                <p class="text-sm font-semibold text-[#6B3E12]">Belum ada pengeluaran.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
