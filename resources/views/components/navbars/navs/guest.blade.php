<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <ul class="navbar-nav ms-auto justify-content-end">
                <li class="nav-item px-3 d-flex align-items-center">
                    @if(app()->getLocale() == 'ar')
                        <a href="{{ route('dashboard.lang.switch', 'en') }}" class="nav-link text-body font-weight-bold px-0">
                            <i class="fa fa-language me-sm-1"></i>
                            <span class="d-sm-inline d-none">{{ __('English') }}</span>
                        </a>
                    @else
                        <a href="{{ route('dashboard.lang.switch', 'ar') }}" class="nav-link text-body font-weight-bold px-0">
                            <i class="fa fa-language me-sm-1"></i>
                            <span class="d-sm-inline d-none">{{ __('العربية') }}</span>
                        </a>
                    @endif
                </li>
            </ul>
        </div>
    </div>
</nav>
