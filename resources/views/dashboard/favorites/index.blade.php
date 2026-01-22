<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="favorites"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Favorites') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">

                        {{-- Header --}}
                        <div
                            class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 flex-grow-1">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Favorites Table') }}</h6>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center my-3 flex-wrap px-3">
                            {{-- Search Form --}}
                            <form method="GET" action="{{ route('favorites.index') }}" class="d-flex mb-0">
                                <input type="text" name="search" value="{{ $search ?? '' }}"
                                    placeholder="{{ __('Search product...') }}" class="form-control form-control-sm me-2"
                                    style="width:200px;">
                                <button type="submit" class="btn btn-sm btn-primary">{{ __('Search') }}</button>
                            </form>
                        </div>

                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                @if($favorites->count())
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Product Name') }}</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Main Image') }}</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('User') }}</th>
                                                <th class="text-secondary opacity-7"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="favoritesTable">
                                            @foreach($favorites as $fav)
                                                <tr>
                                                    <td class="px-3 py-1">
                                                        <span class="text-secondary text-xs font-weight-bold">{{ $loop->iteration + $favorites->firstItem() - 1 }}</span>
                                                    </td>
                                                    <td class="px-3 py-1">
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <h6 class="mb-0 text-sm">{{ $fav->product->name ?? __('Deleted Product') }}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-1">
                                                        <img src="{{ optional($fav->product?->variants?->first())->image_path ?? asset('dashboard/assets/img/no-image.png') }}" class="avatar avatar-sm me-3 border-radius-lg" alt="product image" style="width: 50px; height: 50px; object-fit: cover;">
                                                    </td>
                                                    <td class="px-3 py-1">
                                                        <span class="text-secondary text-xs font-weight-bold">
                                                            @if($fav->user)
                                                                {{ $fav->user->first_name }} {{ $fav->user->last_name }}
                                                            @else
                                                                {{ __('Deleted User') }}
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <form action="{{ route('favorites.destroy', $fav->id) }}" method="POST"
                                                            class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger delete-btn mb-0">
                                                                <i class="material-icons text-sm">delete</i> {{ __('Delete') }}
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="d-flex justify-content-end flex-wrap mt-3 px-3">
                                        {{ $favorites->links('pagination::bootstrap-5') }}
                                    </div>
                                @else
                                    <div class="px-3 py-3 text-center">
                                        <p class="text-sm mb-0">{{ __('No favorite products found.') }}</p>
                                    </div>
                                @endif
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
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm("{{ __('Are you sure you want to delete this item?') }}")) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>
    
</x-layout>
