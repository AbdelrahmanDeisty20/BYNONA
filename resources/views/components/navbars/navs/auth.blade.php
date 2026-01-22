@props(['titlePage', 'activePage' => ''])

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">{{ __('Pages') }}</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ $titlePage }}</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">{{ $titlePage }}</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                <a class="nav-link text-body {{ ($activePage ?? '') == 'notifications' ? ' active' : '' }} d-flex align-items-center"
                    href="{{route("dashboard.notifications.index")}}">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center position-relative" style="width: 24px; height: 24px;">
                        <i class="material-icons opacity-10">notifications</i>
                        <span id="bynona-nav-badge" class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger d-none" style="font-size: 0.5rem; padding: 0.25em 0.4em; z-index: 10; border: 1px solid white;">
                            0
                        </span>
                    </div>
                    <span class="nav-link-text ms-1">{{ __('Notifications') }}</span>
                </a>
            </div>
            <form method="POST" action="" class="d-none" id="logout-form">
                @csrf
            </form>
            <ul class="navbar-nav  justify-content-end">
                <li class="nav-item d-flex align-items-center">
                    <a href="{{route("dashboard.logout")}}" class="nav-link text-body font-weight-bold px-0">
                        <i class="fa fa-user me-sm-1"></i>
                        <span class="d-sm-inline d-none">{{ __('Logout') }}</span>
                    </a>
                </li>
                
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>
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
                <li class="nav-item px-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0">
                        <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navBadge = document.getElementById('bynona-nav-badge');
 
        function updateNotifications() {
            fetch('{{ route("dashboard.notifications.fetch") }}')
                .then(response => response.json())
                .then(data => {
                    // Update Badge
                    if (data.unread_count > 0) {
                        if (navBadge) {
                            navBadge.innerText = data.unread_count;
                            navBadge.classList.remove('d-none');
                        }
                    } else {
                        if (navBadge) navBadge.classList.add('d-none');
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }

        // Initial fetch
        updateNotifications();

        // Poll every 30 seconds
        setInterval(updateNotifications, 30000);
    });
</script>
