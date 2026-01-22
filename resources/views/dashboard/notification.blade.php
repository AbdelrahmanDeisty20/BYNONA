<x-layout bodyClass="g-sidenav-show bg-gray-200">

    <x-navbars.sidebar activePage="notifications"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-navbars.navs.auth titlePage="{{ __('Notifications') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-lg-8 col-md-10 mx-auto">

                    <div class="card mt-4">
                        <div class="card-header p-3">
                            <h5 class="mb-0">{{ __('Notifications') }}</h5>
                        </div>

                        <div class="card-body p-3 pb-0">

                            {{-- لو فاضي --}}
                            @if ($notifications->count() == 0)
                                <p class="text-center">{{ __('No notifications found') }}</p>
                            @else
                                {{-- جدول الإشعارات --}}
                            @endif



                            {{-- عرض الإشعارات --}}
                            @foreach ($notifications as $noti)
                                @php
                                    if ($noti->order_id) {
                                        $type = 'primary';
                                    } elseif ($noti->offer_id) {
                                        $type = 'success';
                                    } elseif ($noti->product_id) {
                                        $type = 'warning';
                                    } elseif ($noti->payment_id) {
                                        $type = 'danger';
                                    } else {
                                        $type = 'info';
                                    }
                                @endphp

                                <div class="alert alert-{{ $type }} alert-dismissible text-white"
                                    role="alert">
                                    <span class="text-sm">{{ $noti->body }}</span>

                                    <button type="button" class="btn-close text-lg py-3 opacity-10"
                                        data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endforeach

                            {{-- Pagination --}}
                            <div class="mt-3">
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

</x-layout>
