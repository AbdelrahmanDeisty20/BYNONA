<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />
    <x-navbars.sidebar activePage="orders"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Order Details') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary mb-0">
                    <i class="material-icons text-sm">arrow_back</i> {{ __('Back to Orders') }}
                </a>
                <div class="d-flex gap-2">
                    @if($order->status !== 'processing' && $order->status !== 'completed' && $order->status !== 'cancelled')
                        <form method="POST" action="{{ route('orders.processingOrder', $order->id) }}">
                            @csrf @method('PUT')
                            <button type="submit" class="btn bg-gradient-info mb-0" onclick="return confirm('{{ __('Are you sure you want to process this order?') }}')">{{ __('Process Order') }}</button>
                        </form>
                    @endif

                    @if($order->status !== 'completed' && $order->status !== 'cancelled')
                        <form method="POST" action="{{ route('orders.completeOrder', $order->id) }}">
                            @csrf @method('PUT')
                            <button type="submit" class="btn bg-gradient-success mb-0" onclick="return confirm('{{ __('Are you sure you want to complete this order?') }}')">{{ __('Complete Order') }}</button>
                        </form>
                    @endif

                    @if($order->status !== 'cancelled' && $order->status !== 'completed')
                        <button type="button" class="btn bg-gradient-warning mb-0" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                            {{ __('Cancel Order') }}
                        </button>
                    @endif

                    <form method="POST" action="{{ route('orders.destroy', $order->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn bg-gradient-danger mb-0" onclick="return confirm('{{ __('Are you sure you want to delete?') }}')">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>

            <div class="row">
                {{-- Customer Info --}}
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                            <h6 class="mb-0">{{ __('Customer Information') }}</h6>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-group">
                                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">{{ __('Full Name') }}:</strong> {{ $order->user_name ?? ($order->user->first_name ?? 'N/A') }}</li>
                                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">{{ __('Mobile') }}:</strong> {{ $order->user_phone }}</li>
                                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">{{ __('Governorate') }}:</strong> {{ $order->governorate->name ?? 'N/A' }}</li>
                                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">{{ __('Address') }}:</strong> {{ $order->user_address }}</li>
                                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">{{ __('Account Email') }}:</strong> {{ $order->user->email ?? 'N/A' }}</li>
                                @if($order->notice)
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-danger">{{ __('Cancellation Reason') }}:</strong> {{ $order->notice }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="col-md-8 mt-md-0 mt-4">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <h6 class="mb-0">{{ __('Order Summary') }} #{{ $order->order_number }}</h6>
                                </div>
                                <div class="col-6 text-end">
                                    @php
                                        $statusClass = [
                                            'pending' => 'secondary',
                                            'processing' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ][$order->status] ?? 'dark';
                                    @endphp
                                    <span class="badge badge-sm bg-gradient-{{ $statusClass }}">{{ strtoupper($order->status) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <p class="text-xs font-weight-bold mb-0">{{ __('Order Date') }}:</p>
                                    <p class="text-sm">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="text-xs font-weight-bold mb-0">{{ __('Order Type') }}:</p>
                                    <span class="badge badge-sm bg-gradient-{{ $order->type == 'wholesale' ? 'warning' : 'info' }}">{{ strtoupper($order->type) }}</span>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Product') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ __('Qty') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Unit Price') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Subtotal') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $item->product->name ?? 'N/A' }}</h6>
                                                            @if($item->variantAttributes && $item->variantAttributes->count() > 0)
                                                                <div class="text-xxs text-muted">
                                                                    @foreach($item->variantAttributes as $attr)
                                                                        <span>{{ $attr->key }}: {{ $attr->value }}</span>@if(!$loop->last) | @endif
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $item->pivot->quantity }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <p class="text-xs font-weight-bold mb-0">{{ number_format($item->pivot->price, 2) }} {{ __('EGP') }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-xs font-weight-bold">{{ number_format($item->pivot->quantity * $item->pivot->price, 2) }} {{ __('EGP') }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 border-top pt-3">
                                <div class="row">
                                    <div class="col-8 text-end">
                                        <p class="text-sm mb-1 text-secondary">{{ __('Subtotal') }}:</p>
                                        <p class="text-sm mb-1 text-secondary">{{ __('Shipping') }}:</p>
                                        <h5 class="mb-0 text-dark font-weight-bolder">{{ __('Total') }}:</h5>
                                    </div>
                                    <div class="col-4 text-end">
                                        <p class="text-sm mb-1 text-dark font-weight-bold">{{ number_format($order->total_price - $shippingCost, 2) }} {{ __('EGP') }}</p>
                                        <p class="text-sm mb-1 text-dark font-weight-bold">{{ number_format($shippingCost, 2) }} {{ __('EGP') }}</p>
                                        <h5 class="mb-0 text-success font-weight-bolder">{{ number_format($order->total_price, 2) }} {{ __('EGP') }}</h5>
                                    </div>
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

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" role="dialog" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-normal" id="cancelOrderModalLabel">{{ __('Cancel Order') }}</h5>
                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="cancelOrderForm" method="POST" action="{{ route('orders.cancelOrder', $order->id) }}">
                    @csrf 
                    @method('PUT')
                    <div class="modal-body">
                        <div class="input-group input-group-static mb-4">
                            <label>{{ __('Cancellation Reason') }}</label>
                            <textarea name="notice" class="form-control" rows="3" placeholder="{{ __('Enter reason for cancellation...') }}" required>{{ old('notice') }}</textarea>
                            @error('notice')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn bg-gradient-primary">{{ __('Confirm Cancellation') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.js"></script>
    <script>
        $(document).ready(function() {
            @if(session('success'))
                Notiflix.Notify.success("{{ session('success') }}");
            @endif
        });
    </script>
@endpush
