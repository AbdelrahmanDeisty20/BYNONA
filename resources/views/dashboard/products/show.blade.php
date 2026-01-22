<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <style>
        .product-header-card {
            border: none;
            border-radius: 1.5rem;
            background: #fff;
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        .product-header-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #e91e63, #ff4081);
        }
        .variant-card {
            border: none;
            border-radius: 1.25rem;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .variant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.1);
        }
        .variant-gallery-container {
            background: #f8f9fa;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .variant-main-img {
            width: 100%;
            height: 300px;
            object-fit: contain;
            border-radius: 1rem;
            cursor: zoom-in;
            background: white;
            padding: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }
        .variant-sub-gallery {
            display: flex;
            gap: 8px;
            margin-top: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .sub-img-thumb {
            width: 50px;
            height: 50px;
            border-radius: 0.5rem;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }
        .sub-img-thumb:hover {
            border-color: #e91e63;
            transform: scale(1.1);
        }
        .variant-info-side {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .attr-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #7b809a;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .attr-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: #344767;
            margin-bottom: 1rem;
        }
        .price-tag-v3 {
            background: #fdf2f6;
            color: #e91e63;
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            display: inline-block;
        }
        .stock-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 1.5rem;
        }
        .stock-bar {
            height: 4px;
            width: 100px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }
        .stock-progress {
            height: 100%;
            border-radius: 10px;
        }
        .lang-card {
            background: #fafafa;
            border-radius: 1rem;
            padding: 1.5rem;
            border-left: 4px solid #e91e63;
            height: 100%;
        }
        .section-divider {
            height: 1px;
            background: #f0f2f5;
            margin: 2rem 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .section-divider-text {
            background: #f0f2f5;
            padding: 5px 20px;
            border-radius: 50rem;
            font-size: 0.65rem;
            font-weight: 800;
            color: #7b809a;
            text-transform: uppercase;
        }
    </style>

    <x-navbars.sidebar activePage="products"></x-navbars.sidebar>

    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <x-navbars.navs.auth :titlePage="__('Product Portfolio')"></x-navbars.navs.auth>

        <div class="container-fluid py-4 px-md-4">
            {{-- Unified Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
                    <li class="breadcrumb-item text-sm text-dark active font-weight-bolder">{{ $product->name }}</li>
                  </ol>
                </nav>
                <div class="d-flex gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
                        <i class="material-icons text-sm">west</i> {{ __('All Products') }}
                    </a>
                    <a href="{{ route('products.edit', $product->id) }}" class="btn bg-gradient-primary btn-sm mb-0">
                        <i class="material-icons text-sm">edit</i> {{ __('Edit Content') }}
                    </a>
                </div>
            </div>

            {{-- Product Summary Card --}}
            <div class="product-header-card">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="mb-2">
                            <span class="text-xs font-weight-bolder text-info text-uppercase letter-spacing-1">{{ $product->brand->name ?? __('Bynona Genuine') }}</span>
                            <span class="mx-2 text-secondary opacity-3">|</span>
                            <span class="text-xs font-weight-bolder text-secondary text-uppercase">{{ $product->type }}</span>
                        </div>
                        <h2 class="font-weight-black text-dark mb-3">{{ $product->name }}</h2>
                        <div class="mb-4">
                            @foreach($product->categories->unique('id') as $cat)
                                <span class="badge badge-sm bg-gradient-light text-dark me-1">{{ $cat->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-inline-block text-start p-3 bg-gray-100 border-radius-lg">
                            <div class="text-xxs font-weight-bolder text-uppercase text-secondary">{{ __('Variations') }}</div>
                            <div class="h4 font-weight-black mb-0">{{ $product->variants->count() }}</div>
                        </div>
                        <div class="d-inline-block text-start p-3 bg-gray-100 border-radius-lg ms-2">
                            <div class="text-xxs font-weight-bolder text-uppercase text-secondary">{{ __('Stock') }}</div>
                            <div class="h4 font-weight-black mb-0 text-primary">{{ $product->variants->sum('stock') }}</div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4 pt-4 border-top">
                    <div class="col-md-6 mb-3">
                        <div class="lang-card">
                            <span class="text-xxs font-weight-black text-info text-uppercase d-block mb-3">{{ __('Full English Description') }}</span>
                            <p class="text-sm text-secondary mb-0" style="line-height: 1.8;">{{ $product->desc_en ?: __('No information provided in English.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="lang-card text-end" style="border-left: none; border-right: 4px solid #e91e63;">
                            <span class="text-xxs font-weight-black text-info text-uppercase d-block mb-3">{{ __('الوصف التفصيلي بالعربية') }}</span>
                            <p class="text-sm text-secondary mb-0" style="line-height: 1.8;">{{ $product->desc_ar ?: __('لم يتم إدخال تفاصيل بالعربية.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Variants Section Divider --}}
            <div class="section-divider">
                <span class="section-divider-text">{{ __('Available Variants & Specific Visuals') }}</span>
            </div>

            {{-- Detailed Variant Cards --}}
            @foreach($product->variants as $variant)
                <div class="variant-card">
                    <div class="row g-0">
                        {{-- Visuals --}}
                        <div class="col-lg-4">
                            <div class="variant-gallery-container h-100">
                                <div class="text-xxs font-weight-black text-uppercase text-info mb-2 opacity-7 text-center">
                                    <i class="material-icons text-xs align-middle me-1">image</i> {{ __('Main Visual') }}
                                </div>
                                
                                @if($variant->main_image)
                                    <img src="{{ $variant->image_path }}" class="variant-main-img" data-bs-toggle="modal" data-bs-target="#imgModalV3" onclick="setModalImgV3('{{ $variant->image_path }}')">
                                @else
                                    <div class="variant-main-img d-flex align-items-center justify-content-center opacity-3">
                                        <i class="material-icons" style="font-size: 4rem;">hide_image</i>
                                    </div>
                                @endif

                                @if(!empty($variant->images_path))
                                    <div class="mt-4 w-100">
                                        <div class="text-xxs font-weight-black text-uppercase text-secondary mb-2 opacity-7 text-center">
                                            <i class="material-icons text-xs align-middle me-1">collections</i> {{ __('Additional Gallery') }}
                                        </div>
                                        <div class="variant-sub-gallery">
                                            @foreach($variant->images_path as $subImg)
                                                <img src="{{ $subImg }}" class="sub-img-thumb shadow-sm" data-bs-toggle="modal" data-bs-target="#imgModalV3" onclick="setModalImgV3('{{ $subImg }}')">
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="mt-3 text-xxs font-weight-black text-secondary text-uppercase opacity-5">
                                    <i class="material-icons text-xs me-1">zoom_in</i> {{ __('Click to enlarge') }}
                                </div>
                            </div>
                        </div>

                        {{-- Specifications --}}
                        <div class="col-lg-8">
                            <div class="variant-info-side h-100">
                                <div class="row">
                                    <div class="col-md-7">
                                        <h5 class="text-dark font-weight-black mb-4 d-flex align-items-center">
                                            <i class="material-icons me-2 text-primary">tune</i> {{ __('Configuration Specs') }}
                                        </h5>
                                        <div class="row">
                                            @forelse($variant->variantAttributes as $attr)
                                                <div class="col-sm-6 mb-3">
                                                    <div class="attr-label">{{ $attr->key }}</div>
                                                    <div class="attr-value">{{ $attr->value }}</div>
                                                </div>
                                            @empty
                                                <div class="col-12">
                                                    <span class="text-sm text-secondary italic">{{ __('No custom attributes for this version.') }}</span>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="col-md-5 text-md-end border-start">
                                        <div class="price-tag-v3">
                                            <div class="text-xxs font-weight-black text-uppercase opacity-7 mb-1">{{ __('Price') }}</div>
                                            <div class="h3 font-weight-black mb-0">
                                                {{ number_format($product->type == 'retail' ? $variant->retail_price : $variant->wholesale_price, 2) }}
                                                <small class="text-xs">{{ __('EGP') }}</small>
                                            </div>
                                            @if($product->type == 'wholesale')
                                                <div class="text-xxs font-weight-black text-uppercase mt-1 text-info">
                                                    {{ __('Minimum Order') }}: {{ $variant->min_quantity }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="stock-indicator mt-4 ms-auto" style="width: fit-content;">
                                            <div class="text-end">
                                                <div class="text-xxs font-weight-black text-uppercase text-secondary mb-1">{{ __('Stock') }}</div>
                                                <div class="h5 font-weight-black mb-1">{{ $variant->stock }} <small class="text-xs font-weight-normal text-secondary">Units</small></div>
                                                @php
                                                    $stockPct = min(($variant->stock / 50) * 100, 100);
                                                    $stockCol = $variant->stock > 10 ? '#4caf50' : ($variant->stock > 0 ? '#fb8c00' : '#f44336');
                                                @endphp
                                                <div class="stock-bar">
                                                    <div class="stock-progress" style="width:{{ $stockPct }}%; background-color: {{ $stockCol }};"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                    <span class="text-xs font-weight-black text-uppercase {{ $variant->stock > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $variant->stock > 0 ? __('Variation is Active') : __('Out of Stock') }}
                                    </span>
                                    <form action="{{ route('dashboard.properties.destroy', $variant->id) }}" method="POST" class="d-inline delete-variant-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-link text-danger text-xs font-weight-black p-0 m-0 d-flex align-items-center delete-variant-btn">
                                            <i class="material-icons text-sm me-1">delete_forever</i> {{ __('Delete Variation') }}
                                        </button>
                                    </form>
 Riverside.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

        <x-footers.auth></x-footers.auth>
    </div>

    {{-- Universal Modal V3 --}}
    <div class="modal fade" id="imgModalV3" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-radius-xl shadow-xl overflow-hidden">
          <div class="modal-body p-0 position-relative text-center bg-black">
            <button type="button" class="btn-close btn-close-white position-absolute end-0 p-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 100;"></button>
            <img src="" id="modalImgV3" class="img-fluid" style="max-height: 85vh; object-fit: contain;">
          </div>
        </div>
      </div>
    </div>

    @push('js')
    <script>
        function setModalImgV3(src) {
            $('#modalImgV3').attr('src', src);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-variant-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm("{{ __('Are you sure you want to delete this variation?') }}")) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-layout>
