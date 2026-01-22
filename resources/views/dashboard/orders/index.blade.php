<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="orders"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Orders') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Orders Table') }}</h6>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center my-3 mx-3 flex-wrap">
                            {{-- Search Form --}}
                            <form method="GET" action="{{ route('orders.index') }}" class="d-flex mb-0">
                                <input type="text" name="q" value="{{ request('q') }}"
                                    placeholder="{{ __('Search Orders...') }}" class="form-control form-control-sm me-2 border p-2"
                                    style="width:250px;">
                                <button type="submit" class="btn btn-sm btn-primary mb-0">{{ __('Search') }}</button>
                                @if(request('q'))
                                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-secondary mb-0 ms-2">{{ __('Clear') }}</a>
                                @endif
                            </form>
                        </div>

                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">{{ __('Order #') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ __('User') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Type') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Total') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Status') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Created') }}</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td class="ps-3">
                                                    <span class="text-xs font-weight-bold">{{ $order->order_number }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <h6 class="mb-0 text-sm">{{ $order->user->first_name ?? 'N/A' }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $order->user_phone }}</p>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="badge badge-sm bg-gradient-{{ $order->type == 'wholesale' ? 'warning' : 'info' }}">
                                                        {{ strtoupper($order->type) }}
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-sm font-weight-bold text-success">{{ number_format($order->total_price, 2) }} {{ __('EGP') }}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @php
                                                        $statusClass = [
                                                            'pending' => 'secondary',
                                                            'processing' => 'info',
                                                            'completed' => 'success',
                                                            'cancelled' => 'danger'
                                                        ][$order->status] ?? 'dark';
                                                    @endphp
                                                    <span class="badge badge-sm bg-gradient-{{ $statusClass }}">
                                                        {{ strtoupper($order->status) }}
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-xs text-secondary">{{ $order->created_at->format('d/m/Y') }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-link text-primary p-2 mb-0" title="View Order">
                                                        <i class="material-icons text-lg">visibility</i>
                                                        <span class="text-xs font-weight-bold">{{ __('View Details') }}</span>
                                                    </a>
                                                    <form method="POST" action="{{ route('orders.destroy', $order->id) }}" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger p-2 mb-0" onclick="return confirm('{{ __('Delete this order?') }}')">
                                                            <i class="material-icons text-lg">delete</i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="px-3 pt-4">
                                    {{ $orders->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth />
        </div>
    </main>
    <x-plugins />
</x-layout>
