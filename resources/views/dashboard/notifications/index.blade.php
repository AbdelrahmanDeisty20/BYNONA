<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="notifications"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="{{ __('Notifications') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-lg-10 col-md-12 mx-auto">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Notifications') }}</h6>
                                <button onclick="markAllAsRead()" class="btn btn-sm btn-outline-white mb-0">{{ __('Mark all as read') }}</button>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <!-- Filters -->
                            <div class="px-4 py-2 border-bottom d-flex gap-2 flex-wrap">
                                <a href="{{ route('dashboard.notifications.index', ['type' => 'all']) }}" class="btn btn-sm {{ request('type') == 'all' || !request('type') ? 'btn-primary' : 'btn-outline-primary' }}">{{ __('All') }}</a>
                                <a href="{{ route('dashboard.notifications.index', ['type' => 'order']) }}" class="btn btn-sm {{ request('type') == 'order' ? 'btn-info' : 'btn-outline-info' }}">{{ __('Orders') }}</a>
                                <a href="{{ route('dashboard.notifications.index', ['type' => 'low_stock_admin']) }}" class="btn btn-sm {{ request('type') == 'low_stock_admin' ? 'btn-warning' : 'btn-outline-warning' }}">{{ __('Products') }}</a>
                                
                                <div class="ms-auto">
                                    <a href="{{ route('dashboard.notifications.index', array_merge(request()->all(), ['unread' => 1])) }}" class="btn btn-sm {{ request('unread') ? 'btn-dark' : 'btn-outline-dark' }}">{{ __('Unread only') }}</a>
                                </div>
                            </div>

                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Notification') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ __('Type') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Date') }}</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($notifications as $noti)
                                        <tr class="{{ $noti->is_read ? '' : 'bg-light' }}" id="notification-{{ $noti->id }}">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        @php
                                                            $icon = 'notifications';
                                                            $color = 'primary';
                                                            if($noti->type == 'order') { $icon = 'shopping_cart'; $color = 'info'; }
                                                            elseif(in_array($noti->type, ['product', 'low_stock', 'low_stock_admin', 'out_of_stock_admin'])) { $icon = 'inventory_2'; $color = 'warning'; }
                                                        @endphp
                                                        <div class="icon icon-sm icon-shape bg-gradient-{{ $color }} shadow-{{ $color }} text-center border-radius-xl mt-n4 me-3">
                                                            <i class="material-icons opacity-10">{{ $icon }}</i>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        @php
                                                            $url = '#';
                                                            if($noti->type == 'order' && isset($noti->data['order_id'])) {
                                                                $url = route('orders.show', $noti->data['order_id']);
                                                            } elseif(in_array($noti->type, ['product', 'low_stock', 'low_stock_admin', 'out_of_stock_admin'])) {
                                                                if(isset($noti->data['product_id'])) {
                                                                    $url = route('products.edit', $noti->data['product_id']);
                                                                }
                                                            }
                                                        @endphp
                                                        <a href="{{ $url }}" class="text-decoration-none">
                                                            <h6 class="mb-0 text-sm {{ $noti->is_read ? 'text-secondary font-weight-normal' : 'font-weight-bold text-dark' }}">{{ $noti->title }}</h6>
                                                            <p class="text-xs text-secondary mb-0 mt-1">{{ $noti->body }}</p>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-xs font-weight-bold">{{ $noti->type ? __($noti->type) : __('General') }}</span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold">{{ $noti->created_at->diffForHumans() }}</span>
                                            </td>
                                            <td class="align-middle">
                                                @if(!$noti->is_read)
                                                    <button onclick="markAsRead({{ $noti->id }})" class="btn btn-link text-primary text-gradient px-3 mb-0">
                                                        <i class="material-icons text-sm me-2">done_all</i>{{ __('Read') }}
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <p class="text-secondary mb-0">{{ __('No notifications found') }}</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="px-4 py-3">
                                {{ $notifications->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>

    @push('js')
    <script>
        function markAsRead(id) {
            fetch(`/admin/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const row = document.getElementById(`notification-${id}`);
                    row.classList.remove('bg-light');
                    row.querySelector('h6').classList.remove('font-weight-bold');
                    row.querySelector('h6').classList.add('text-secondary', 'font-weight-normal');
                    row.querySelector('button')?.remove();
                }
            });
        }

        function markAllAsRead() {
            fetch('/admin/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }
    </script>
    @endpush
</x-layout>
