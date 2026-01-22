<x-layout bodyClass="bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <!-- Navbar -->
                <x-navbars.navs.guest signin='static-sign-in' signup='static-sign-up'></x-navbars.navs.guest>
                <!-- End Navbar -->
            </div>
        </div>
    </div>
    <main class="main-content  mt-0">
        <div class="page-header align-items-start min-vh-100">
            <div class="container my-auto">
                <div class="row">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                    <div class="text-center">
                                        <img src="{{ asset('dashboard/assets/img/bynona-logo-original.png') }}" alt="Bynona Logo" style="width: 100%; max-width: 150px; height: auto; border-radius: 5px; margin-bottom: 5px;">
                                    </div>
                                    <h4 class="text-dark font-weight-bolder text-center mt-2 mb-0">{{ __('Sign in') }}</h4>
                                    <div class="row mt-3">
                                        <div class="col-2 text-center ms-auto">
                                            <a class="btn btn-link px-3" href="javascript:;">
                                                <i class="fa fa-facebook text-dark text-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-2 text-center px-1">
                                            <a class="btn btn-link px-3" href="javascript:;">
                                                <i class="fa fa-github text-dark text-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col-2 text-center me-auto">
                                            <a class="btn btn-link px-3" href="javascript:;">
                                                <i class="fa fa-google text-dark text-lg"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form role="form" class="text-start" id="loginForm">
                                    @csrf
                                    <div class="input-group input-group-outline my-3">
                                        <label class="form-label">{{ __('Email') }}</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                    <span class="text-danger error-email text-xs d-block mb-2"></span>

                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">{{ __('Password') }}</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                    <span class="text-danger error-password text-xs d-block mb-2"></span>

                                    <div class="form-check form-switch d-flex align-items-center mb-3">
                                        <input class="form-check-input" type="checkbox" id="rememberMe">
                                        <label class="form-check-label mb-0 ms-2" for="rememberMe">{{ __('Remember me') }}</label>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn bg-gradient-primary text-dark w-100 my-4 mb-2">{{ __('Sign in') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.guest></x-footers.guest>
        </div>
    </main>
    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.js"></script>
        <script>
            $(document).ready(function() {
                $("#loginForm").validate({
                    rules: {
                        email: {
                            required: true,
                            email: true
                        },
                        password: {
                            required: true
                        }
                    },
                    messages: {
                        email: {
                            required: "{{ __('this field is require') }}",
                            email: "{{ __('must be email') }}"
                        },
                        password: {
                            required: "{{ __('this field is require') }}"
                        }
                    },
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        let name = element.attr("name");
                        $(".error-" + name).html(error);
                    },

                    submitHandler: function(form) {

                        $('span[class^="error-"]').html('');

                        let btn = $(form).find('button[type="submit"]');
                        btn.prop('disabled', true).text("{{ __('Signing in...') }}");

                        $.ajax({
                            url: "{{ route('dashboard.loginSend') }}",
                            method: "POST",
                            data: new FormData(form),
                            contentType: false,
                            processData: false,

                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },

                            success: function(response) {
                                Notiflix.Notify.success(response.message ||
                                    "{{ __('Login successful!') }}");

                                form.reset();

                                setTimeout(function() {
                                    window.location.href =
                                        "{{ route('dashbord.dashboard') }}";
                                }, 800);
                            },

                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;

                                    $.each(errors, function(key, value) {
                                        $('.error-' + key).text(value[0]);
                                    });

                                    Notiflix.Notify.failure(
                                        "{{ __('Please correct errors') }}");
                                } else {
                                    Notiflix.Report.failure("{{ __('Error') }}",
                                        "{{ __('Something went wrong') }}", "{{ __('Close') }}");
                                }
                            },

                            complete: function() {
                                btn.prop('disabled', false).text("{{ __('Sign in') }}");
                            }
                        });
                    }

                })
            });
        </script>
    @endpush
</x-layout>
