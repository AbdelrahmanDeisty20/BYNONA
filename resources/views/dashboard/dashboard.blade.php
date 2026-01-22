<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="dashboard"/>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

        <x-navbars.navs.auth titlePage="{{ __('E-Commerce Dashboard') }}"/>

        <div class="container-fluid py-4">

            {{-- ================= KPIs ================= --}}
            <div class="row">
                {{-- Today Orders --}}
                <div class="col-xl-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg bg-gradient-primary shadow text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">shopping_cart</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">{{ __('Today Orders') }}</p>
                                <h4 class="mb-0">{{ $todayOrders }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Today Sales --}}
                <div class="col-xl-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg bg-gradient-success shadow text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">payments</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">{{ __('Today Sales') }}</p>
                                <h4 class="mb-0">{{ number_format($todaySales,2) }} {{ __('EGP') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Customers --}}
                <div class="col-xl-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg bg-gradient-info shadow text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">group</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">{{ __('Customers') }}</p>
                                <h4 class="mb-0">{{ $customersCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top Product --}}
                <div class="col-xl-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg bg-gradient-dark shadow text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">star</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">{{ __('Top Product Today') }}</p>
                                <h6 class="mb-0">
                                    {{ $topProductToday?->name ?? '—' }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= Charts ================= --}}
            <div class="row mt-4">

                {{-- Weekly Orders --}}
                <div class="col-lg-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-header p-0 mt-n4 mx-3 bg-transparent">
                            <div class="bg-gradient-primary shadow border-radius-lg py-3">
                                <canvas id="weeklyOrdersChart" height="170"></canvas>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6>{{ __('Weekly Orders') }}</h6>
                            <p class="text-sm">{{ __('Orders count this week') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Monthly Sales --}}
                <div class="col-lg-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-header p-0 mt-n4 mx-3 bg-transparent">
                            <div class="bg-gradient-success shadow border-radius-lg py-3">
                                <canvas id="monthlySalesChart" height="170"></canvas>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6>{{ __('Monthly Sales') }}</h6>
                            <p class="text-sm">{{ __('Total sales per month') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Top Products --}}
                <div class="col-lg-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-header p-0 mt-n4 mx-3 bg-transparent">
                            <div class="bg-gradient-dark shadow border-radius-lg py-3">
                                <canvas id="topProductsChart" height="170"></canvas>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6>{{ __('Top Selling Products') }}</h6>
                            <p class="text-sm">{{ __('By quantity sold') }}</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ================= Table ================= --}}
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>{{ __('Top Selling Products') }}</h6>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th class="text-center">{{ __('Quantity') }}</th>
                                    <th class="text-center">{{ __('Revenue') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($topProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-center">{{ $product->total_qty }}</td>
                                        <td class="text-center">{{ number_format($product->revenue,2) }} {{ __('EGP') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth/>
        </div>
    </main>

    @push('js')
    <script src="{{ asset('dashboard/assets/js/plugins/chartjs.min.js') }}"></script>

    <script>
        new Chart(document.getElementById('weeklyOrdersChart'), {
            type: 'bar',
            data: {
                labels: ["{{ __('Mon') }}","{{ __('Tue') }}","{{ __('Wed') }}","{{ __('Thu') }}","{{ __('Fri') }}","{{ __('Sat') }}","{{ __('Sun') }}"],
                datasets: [{
                    data: [
                        {{ $weeklyOrders['Monday'] ?? 0 }},
                        {{ $weeklyOrders['Tuesday'] ?? 0 }},
                        {{ $weeklyOrders['Wednesday'] ?? 0 }},
                        {{ $weeklyOrders['Thursday'] ?? 0 }},
                        {{ $weeklyOrders['Friday'] ?? 0 }},
                        {{ $weeklyOrders['Saturday'] ?? 0 }},
                        {{ $weeklyOrders['Sunday'] ?? 0 }},
                    ],
                    backgroundColor: 'rgba(255,255,255,.8)',
                    label: "{{ __('Orders') }}"
                }]
            },
            options: { plugins:{legend:{display:false}} }
        });

        new Chart(document.getElementById('monthlySalesChart'), {
            type: 'line',
            data: {
                labels: ["{{ __('Apr') }}","{{ __('May') }}","{{ __('Jun') }}","{{ __('Jul') }}","{{ __('Aug') }}","{{ __('Sep') }}","{{ __('Oct') }}","{{ __('Nov') }}","{{ __('Dec') }}"],
                datasets: [{
                    data: [
                        {{ $monthlySales[4] ?? 0 }},
                        {{ $monthlySales[5] ?? 0 }},
                        {{ $monthlySales[6] ?? 0 }},
                        {{ $monthlySales[7] ?? 0 }},
                        {{ $monthlySales[8] ?? 0 }},
                        {{ $monthlySales[9] ?? 0 }},
                        {{ $monthlySales[10] ?? 0 }},
                        {{ $monthlySales[11] ?? 0 }},
                        {{ $monthlySales[12] ?? 0 }},
                    ],
                    borderWidth: 4,
                    borderColor: '#fff'
                }]
            },
            options: { plugins:{legend:{display:false}} }
        });

        new Chart(document.getElementById('topProductsChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($topProducts->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($topProducts->pluck('total_qty')) !!},
                    borderColor: '#fff',
                    borderWidth: 4
                }]
            },
            options: { plugins:{legend:{display:false}} }
        });
    </script>
    @endpush
</x-layout>
