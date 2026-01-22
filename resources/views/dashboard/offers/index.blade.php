<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="offers"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Offers') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">

                        {{-- Header --}}
                        <div
                            class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 flex-grow-1">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Offers Table') }}</h6>
                            </div>
                        </div>

                        {{-- Add Button --}}
                        <div class="me-3 my-3 text-end">
                            <a class="btn bg-gradient-dark mb-0" href="{{ route('offers.create') }}">
                                <i class="material-icons text-sm">add</i>&nbsp;&nbsp;{{ __('Add New Offer') }}
                            </a>
                        </div>

                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            {{--  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Title (AR)
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Title (EN)
                                            </th>  --}}
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                {{ __('Product Variant') }}
                                            </th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Original Price') }}
                                            </th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Discount (%)') }}
                                            </th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Start') }}
                                            </th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('End') }}
                                            </th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>

                                    <tbody id="offersTable">
                                        @foreach ($offers as $offer)
                                            <tr id="offer-{{ $offer->id }}">
                                                {{--  <td class="px-2 py-1">
                                                    {{ $offer->title_ar }}
                                                </td>
                                                <td class="px-2 py-1">
                                                    {{ $offer->title_en }}
                                                </td>  --}}

                                                <td class="px-2 py-1">
    @if($offer->variant && $offer->variant->product)
        <div class="d-flex flex-column">
            <h6 class="mb-0 text-sm">
                {{ $offer->variant->product->name }}
            </h6>

            @if($offer->variant->variantAttributes->count())
                <div class="text-xs text-muted">
                    @foreach($offer->variant->variantAttributes as $attr)
                        <span class="badge bg-gradient-secondary me-1">
                            {{ $attr->key }}: {{ $attr->value }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <span class="text-muted">{{ __('N/A') }}</span>
    @endif
</td>


                                                <td class="text-center">
                                                    @if($offer->variant && $offer->variant->product)
                                                        @if($offer->variant->product->type === 'retail')
                                                            {{ $offer->variant->retail_price }} {{ __('EGP') }} ({{ __('Retail') }})
                                                        @else
                                                            {{ $offer->variant->wholesale_price }} {{ __('EGP') }} ({{ __('Wholesale') }})
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    @if($offer->variant && $offer->variant->product)
                                                        @if($offer->variant->product->type === 'retail')
                                                            {{ $offer->discount_retail }}%
                                                        @else
                                                            {{ $offer->discount_wholesale }}%
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    {{ $offer->start }}
                                                </td>

                                                <td class="text-center">
                                                    {{ $offer->end }}
                                                </td>

                                                <td class="align-middle">
                                                    <a class="btn btn-success btn-link"
                                                       href="{{ route('offers.edit', $offer->id) }}"
                                                       title="{{ __('Edit') }}">
                                                        <i class="material-icons">edit</i>
                                                    </a>

                                                    <form action="{{ route('offers.destroy', $offer->id) }}"
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
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- Pagination --}}
                                <div class="d-flex justify-content-end flex-wrap mt-2">
                                    {{ $offers->links('pagination::bootstrap-5') }}
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
                    if (confirm("{{ __('Are you sure you want to delete this offer?') }}")) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>
</x-layout>
