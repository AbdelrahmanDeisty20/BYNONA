@props(['activePage'])

<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 p-0 d-flex align-items-center justify-content-center" href=" {{ route('dashbord.dashboard') }} " style="height: 100%;">
            <img src="{{ asset('dashboard/assets/img/bynona-logo-original.png') }}" class="navbar-brand-img" style="max-height: 35px; width: auto; border-radius: 4px;" alt="main_logo">
        </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse d-block w-auto p-1 " id="sidenav-collapse-main">
        <style>
            .caret-icon {
                transition: transform 0.3s ease;
                pointer-events: none;
            }
            .nav-link:not(.collapsed) .caret-icon {
                transform: rotate(180deg);
            }
            .nav-link::after {
                display: none !important;
            }
            .collapse .nav .nav-item .nav-link:hover {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 0.375rem;
            }
        </style>
        <ul class="navbar-nav">
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">{{ __('User Management') }}
                </h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'user-profile' ? 'active bg-gradient-primary' : '' }} "
                    href="{{ route("dashboard.userProfile") }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">person</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('User Profile') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'user-management' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route("users.index") }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">people</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('User Management') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'addresses' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route("addresses.index") }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">location_on</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Addresses') }}</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">{{ __('Pages') }}</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'dashboard' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route("dashbord.dashboard") }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Dashboard') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'categories' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('categories.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">category</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Categories') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'brands' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('brands.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">business</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Brands') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'favorites' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('favorites.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">favorite</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Favorites') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ in_array($activePage, ['products']) ? 'active bg-gradient-primary' : 'collapsed' }}"
                    href="#productsExample" aria-controls="productsExample" role="button"
                    aria-expanded="{{ in_array($activePage, ['products']) ? 'true' : 'false' }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">inventory_2</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Products') }}</span>
                    <i class="material-icons ms-auto caret-icon" style="font-size: 1.2rem;">expand_more</i>
                </a>
                <div class="collapse {{ in_array($activePage, ['products']) ? 'show' : '' }}" id="productsExample">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item">
                            <a class="nav-link text-white {{ $activePage == 'products' ? 'active font-weight-bold' : '' }}"
                                href="{{ route('products.index') }}">
                                <span class="sidenav-normal"> - {{ __('All Products') }} </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white {{ $activePage == 'attributes' ? 'active font-weight-bold' : '' }}"
                                href="{{ route('attributes.index') }}">
                                <span class="sidenav-normal"> - {{ __('Manage Attributes') }} </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'reviews' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('reviews.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">rate_review</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Reviews') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'offers' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('offers.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">local_offer</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Offers') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'orders' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('orders.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">shopping_basket</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Orders') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'banners' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('banners.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">view_carousel</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Banners') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'productBanners' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('productBanners.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">collections</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Product Banners') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'shiipings' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route("settings.index") }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">local_shipping</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Shipping') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'payments' ? ' active bg-gradient-primary' : '' }}  "
                    href="{{ route("payments.index") }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">paid</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Payments') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ $activePage == 'contacts' ? ' active bg-gradient-primary' : '' }} "
                    href="{{ route('contacts.index') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">contact_phone</i>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Contacts') }}</span>
                </a>
            </li>



        



        </ul>
    </div>
    {{--  <div class="sidenav-footer position-absolute w-100 bottom-0 ">
        <div class="mx-3">
            <a class="btn bg-gradient-primary w-100" href="https://www.creative-tim.com/product/material-dashboard-laravel" target="_blank">Free Download</a>
        </div>
        <div class="mx-3">
            <a class="btn bg-gradient-primary w-100" href="../../documentation/getting-started/installation.html" target="_blank">View documentation</a>
        </div>
        <div class="mx-3">
            <a class="btn bg-gradient-primary w-100"
                href="https://www.creative-tim.com/product/material-dashboard-pro-laravel" target="_blank" type="button">Upgrade
                to pro</a>
        </div>
    </div>  --}}
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.querySelector('a[href="#productsExample"]');
        const collapse = document.getElementById('productsExample');
        
        if (toggle && collapse) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isHidden = !collapse.classList.contains('show');
                
                if (isHidden) {
                    collapse.classList.add('show');
                    this.classList.remove('collapsed');
                    this.setAttribute('aria-expanded', 'true');
                } else {
                    collapse.classList.remove('show');
                    this.classList.add('collapsed');
                    this.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
</script>
