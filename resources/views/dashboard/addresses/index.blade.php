<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="addresses"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Addresses') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">

                        {{-- Header --}}
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 flex-grow-1">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Addresses Table') }}</h6>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('User') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Address Name') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Full Address') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Created At') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($addresses as $address)
                                            <tr>
                                                <td class="px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $address->user->first_name ?? 'N/A' }} {{ $address->user->last_name ?? '' }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $address->user->email ?? '' }}</p>
                                                    </div>
                                                </td>
                                                <td class="px-2 py-1 text-sm">{{ $address->name_address }}</td>
                                                <td class="px-2 py-1 text-sm">
                                                    <span class="text-wrap" style="max-width: 400px; display: inline-block;">
                                                        {{ $address->address }}
                                                    </span>
                                                </td>
                                                <td class="text-center text-sm">
                                                    {{ $address->created_at?->format('d/m/Y') ?? '---' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-end flex-wrap mt-2 pe-3">
                                    {{ $addresses->links('pagination::bootstrap-5') }}
                                </div>
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
