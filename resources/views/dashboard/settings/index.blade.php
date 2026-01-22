<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="shiipings"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Shipping') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">

                        {{-- Header --}}
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">
                                {{ __('Shipping Table') }}
                            </h6>
                        </div>

                        {{-- Add Button --}}
                        <div class="me-3 my-3 text-end">
                            <a class="btn bg-gradient-dark mb-0" href="{{ route('settings.create') }}">
                                <i class="material-icons text-sm">add</i>
                                {{ __('Add New Shipping') }}
                            </a>
                        </div>

                        {{-- Table --}}
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">

                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Governorate') }}
                                            </th>

                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Shipping') }}
                                            </th>

                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Created At') }}
                                            </th>

                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>

                                    <tbody id="settingsTable">
                                        @forelse ($settings as $setting)
                                            <tr id="setting-{{ $setting->id }}">

                                                {{-- Governorate --}}
                                                <td class="px-2 py-1">
                                                    {{ $setting->governorate?->name ?? '-' }}
                                                </td>

                                                {{-- Shipping --}}
                                                <td class="px-2 py-1">
                                                    {{ $setting->shipping }} {{ __('EGP') }}
                                                </td>

                                                {{-- Created --}}
                                                <td class="text-center">
                                                    {{ $setting->created_at->format('d/m/Y') }}
                                                </td>

                                                {{-- Actions --}}
                                                <td class="align-middle">
                                                    <a class="btn btn-success btn-link"
                                                       href="{{ route('settings.edit', $setting->id) }}">
                                                        <i class="material-icons">edit</i>
                                                    </a>

                                                    <form action="{{ route('settings.destroy', $setting->id) }}"
                                                          method="POST"
                                                          class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                                class="btn btn-sm btn-danger delete-btn">
                                                            <i class="bi bi-trash"></i>
                                                            {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    {{ __('No settings found') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                {{-- Pagination --}}
                                <div class="d-flex justify-content-end flex-wrap mt-2">
                                    {{ $settings->links('pagination::bootstrap-5') }}
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    if (confirm("{{ __('Are you sure you want to delete this setting?') }}")) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>
</x-layout>
