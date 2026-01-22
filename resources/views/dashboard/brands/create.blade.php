<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <x-navbars.sidebar activePage="brands"></x-navbars.sidebar>

    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="{{ __('Create Brand') }}"></x-navbars.navs.auth>
        <!-- End Navbar -->

        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1920&q=80');">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>
            <div class="card card-body mx-3 mx-md-4 mt-n6">

                <div class="card card-plain h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-md-8 d-flex align-items-center">
                                <h6 class="mb-3">{{ __('Add Brand') }}</h6>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible text-white" role="alert">
                                <span class="text-sm">{{ session('status') }}</span>
                                <button type="button" class="btn-close text-lg py-3 opacity-10"
                                    data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (Session::has('demo'))
                            <div class="alert alert-danger alert-dismissible text-white" role="alert">
                                <span class="text-sm">{{ Session::get('demo') }}</span>
                                <button type="button" class="btn-close text-lg py-3 opacity-10"
                                    data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form id="createBrandForm" enctype="multipart/form-data">
                            @csrf

                            <div class="row">

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Name (English)') }}</label>
                                    <input type="text" name="name_en" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-name_en"></span>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Name (Arabic)') }}</label>
                                    <input type="text" name="name_ar" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-name_ar"></span>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Logo</label>
                                    <input type="file" name="image" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-logo"></span>

                            </div>

                            <button type="submit" class="btn bg-gradient-dark mt-3">{{ __('Submit') }}</button>

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

                $("#createBrandForm").validate({
                    rules: {
                        name_en: { required: true, maxlength: 255 },
                        name_ar: { required: true, maxlength: 255 },
                        logo: { required: true, extension: "jpg|jpeg|png|webp" }
                    },

                    messages: {
                        name_en: { required: "English name is required." },
                        name_ar: { required: "Arabic name is required." },
                        logo: { required: "Logo is required.", extension: "Allowed: jpg, jpeg, png, webp." }
                    },

                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        let name = element.attr("name");
                        $(".error-" + name).html(error);
                    },

                    submitHandler: function(form) {
                        $('span[class^="error-"]').html('');

                        let btn = $(form).find('button[type="submit"]');
                        btn.prop('disabled', true).text('Submitting ...');

                        $.ajax({
                            url: "{{ route('brands.store') }}",
                            method: "POST",
                            data: new FormData(form),
                            processData: false,
                            contentType: false,
                            headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },

                            success: function(response) {
                                Notiflix.Notify.success(response.message || 'Brand created successfully!');
                                form.reset();

                                setTimeout(() => {
                                    window.location.href = "{{ route('brands.index') }}";
                                }, 800);
                            },

                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    $.each(errors, function(key, value) {
                                        $('.error-' + key).text(value[0]);
                                    })
                                    Notiflix.Notify.failure('Please fix validation errors.');
                                } else {
                                    Notiflix.Report.failure('Error', 'Something went wrong!', 'Close');
                                }
                            },

                            complete: function() {
                                btn.prop('disabled', false).text('Submit');
                            }
                        })
                    }
                });

            });
        </script>
    @endpush

</x-layout>
