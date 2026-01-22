<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <x-navbars.sidebar activePage="user-profile"></x-navbars.sidebar>

    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">

        <x-navbars.navs.auth titlePage="{{ __('User Profile') }}"></x-navbars.navs.auth>

        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?auto=format&fit=crop&w=1920&q=80');">
                <span class="mask  bg-gradient-primary  opacity-6"></span>
            </div>

            <div class="card card-body mx-3 mx-md-4 mt-n6">

                <div class="row gx-4 mb-2">
                    <div class="col-auto">
                        {{--  <div class="avatar avatar-xl position-relative">
                            <img src="{{ asset('assets/img/bruce-mars.jpg') }}" alt="profile_image"
                                class="w-100 border-radius-lg shadow-sm">
                        </div>  --}}
                    </div>

                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <h5 class="mb-1">
                                {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                            </h5>
                            <p class="mb-0 text-sm">
                                {{ auth()->user()->user_type }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card card-plain h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-3">{{ __('Profile Information') }}</h6>
                    </div>

                    <div class="card-body p-3">

                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        <form id="userProfile">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('First Name') }}</label>
                                    <input type="text" name="first_name"
                                        value="{{ old('first_name', auth()->user()->first_name) }}"
                                        class="form-control border border-2 p-2">
                                    <span class="text-danger error-first_name"></span>

                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Last Name') }}</label>
                                    <input type="text" name="last_name"
                                        value="{{ old('last_name', auth()->user()->last_name) }}"
                                        class="form-control border border-2 p-2">
                                    <span class="text-danger error-last_name "></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Email') }}</label>
                                    <input type="email" name="email"
                                        value="{{ old('email', auth()->user()->email) }}"
                                        class="form-control border border-2 p-2">
                                    <span class="text-danger error-email "></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Phone') }}</label>
                                    <input type="text" name="phone"
                                        value="{{ old('phone', auth()->user()->phone) }}"
                                        class="form-control border border-2 p-2">
                                    <span class="text-danger error-phone "></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('User Type') }}</label>
                                    <input type="text" disabled value="{{ ucfirst(auth()->user()->user_type) }}"
                                        class="form-control border border-2 p-2 bg-light">
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Verified') }}</label>
                                    <input type="text" disabled
                                        value="{{ auth()->user()->is_verfived ? __('Yes') : __('No') }}"
                                        class="form-control border border-2 p-2 bg-light">
                                </div>

                            </div>

                            <button type="submit" class="btn bg-gradient-dark">{{ __('Update') }}</button>
                        </form>

                    </div>
                </div>

            </div>
        </div>

        <x-footers.auth></x-footers.auth>
    </div>

    <x-plugins></x-plugins>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.js"></script>

        <script>
            $(document).ready(function() {
                $("#userProfile").validate({
                    first_name: {
                        minlength: 3
                    },
                    last_name: {
                        minlength: 3
                    },
                    last_name: {
                        min: 3
                    },
                    email: {
                        email: true,
                    },
                    phone: {
                        digits: true,
                        pattern: /^01[0-9]{9}$/
                    },
                    messages: {
                        first_name: {
                            minlength: "First name must be at least 3 characters."
                        },
                        last_name: {
                            minlength: "Last name must be at least 3 characters."
                        },
                        email: {
                            email: "Please enter a valid email address."
                        },
                        phone: {
                            digits: "Phone number must contain only digits.",
                            pattern: "Phone number must start with 01 and be exactly 11 digits."
                        }
                    },
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        let name = element.attr("name");
                        $(".error-" + name).html(error);
                    },
                    submitHandler: function(form) {
                        let btn = $(form).find('button[type="submit"]');
                        btn.prop('disabled', true).text('updating...');
                        $.ajax({
                            url: "{{ route('dashboard.profileUpdate') }}",
                            type: "POST",

                            data: new FormData(form),
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Notiflix.Notify.success(response.message ||
                                    'User created successfully!');
                                form.reset();
                                setTimeout(() => {
                                    window.location.href =
                                        "{{ route('dashboard.userProfile') }}";
                                }, 700);
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    $.each(errors, function(key, value) {
                                        $('.error-' + key).text(value[
                                        0]);
                                    });

                                    Notiflix.Notify.failure('Please fix the form errors.');
                                } else {
                                    Notiflix.Notify.failure('Something went wrong!');
                                }
                            },

                            complete: function() {
                                btn.prop('disabled', false).text('Submit');
                            }
                        })
                    }
                })
            })
        </script>
    @endpush

</x-layout>
