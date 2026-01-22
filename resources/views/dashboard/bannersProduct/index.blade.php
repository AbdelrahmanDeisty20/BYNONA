<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="productBanners" />

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Product Banners') }}" />

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">

                        {{-- Header --}}
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 flex-grow-1">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Product Banners Table') }}</h6>
                            </div>
                        </div>

                        <div class="me-3 my-3 text-end">
                            <a class="btn bg-gradient-dark mb-0" href="{{ route('productBanners.create') }}">
                                <i class="material-icons text-sm">add</i>&nbsp;&nbsp;{{ __('Add New Banner For Product') }}
                            </a>
                        </div>

                        {{-- Table --}}
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Title') }}
                                            </th>

                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Price') }}
                                            </th>

                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Image') }}
                                            </th>

                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Created At') }}
                                            </th>

                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($productBanners as $banner)
                                            <tr id="banner-{{ $banner->id }}">

                                                <td class="px-2 py-1">{{ $banner->title }}</td>

                                                <td class="px-2 py-1">{{ $banner->price ?? '-' }}</td>

                                                <td class="px-2 py-1">
                                                    <img src="{{$banner->image_path}}"
                                                         alt=""
                                                         style="width:60px;height:60px;border-radius:5px;object-fit:cover;">
                                                </td>

                                                <td class="text-center">
                                                    {{ $banner->created_at->format('d/m/Y') }}
                                                </td>

                                                <td class="align-middle">
                                                    <a rel="tooltip" class="btn btn-success btn-link"
                                                       href="{{ route('productBanners.edit', $banner->id) }}">
                                                        <i class="material-icons">edit</i>
                                                    </a>

                                                    <form action="{{ route('productBanners.destroy', $banner->id) }}"
                                                          method="POST"
                                                          class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-sm btn-danger delete-btn">
                                                            <i class="bi bi-trash"></i> {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>

                                <div class="d-flex justify-content-end flex-wrap mt-2">
                                    {{ $productBanners->links('pagination::bootstrap-5') }}
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm("{{ __('Are you sure you want to delete this banner?') }}")) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>

</x-layout>
