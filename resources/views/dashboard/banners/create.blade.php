<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <x-navbars.sidebar activePage="banners"></x-navbars.sidebar>
    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage='Create Banner'></x-navbars.navs.auth>
        <!-- End Navbar -->

        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>

            <div class="card card-body mx-3 mx-md-4 mt-n6">
                <div class="card card-plain h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-md-8 d-flex align-items-center">
                                <h6 class="mb-3">Add Banner</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @if (session('status'))
                            <div class="row">
                                <div class="alert alert-success alert-dismissible text-white" role="alert">
                                    <span class="text-sm">{{ Session::get('status') }}</span>
                                    <button type="button" class="btn-close text-lg py-3 opacity-10"
                                        data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <form id="createBannerForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Title (English)</label>
                                    <input type="text" name="title_en" class="form-control border border-2 p-2">
                                    <span class="text-danger error-title_en"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Title (Arabic)</label>
                                    <input type="text" name="title_ar" class="form-control border border-2 p-2">
                                    <span class="text-danger error-title_ar"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Description (English)</label>
                                    <textarea name="short_desc_en" class="form-control border border-2 p-2"></textarea>
                                    <span class="text-danger error-short_desc_en"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Description (Arabic)</label>
                                    <textarea name="short_desc_ar" class="form-control border border-2 p-2"></textarea>
                                    <span class="text-danger error-short_desc_ar"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Image</label>
                                    <input type="file" name="image" class="form-control border border-2 p-2">
                                    <span class="text-danger error-image"></span>
                                </div>

                            </div>
                            <button type="submit" class="btn bg-gradient-dark">Submit</button>
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
                $("#createBannerForm").validate({
                    rules: {
                        title_en: { required: true, maxlength: 255 },
                        title_ar: { required: true, maxlength: 255 },
                        short_desc_en: { required: true },
                        short_desc_ar: { required: true },
                        image: { required: true, extension: "jpg|jpeg|png|webp" }
                    },
                    messages: {
                        title_en: { required: "English title is required.", maxlength: "Max 255 characters." },
                        title_ar: { required: "Arabic title is required.", maxlength: "Max 255 characters." },
                        short_desc_en: { required: "English description is required." },
                        short_desc_ar: { required: "Arabic description is required." },
                        image: { required: "Image is required.", extension: "Allowed formats: jpg, jpeg, png, webp." }
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
                            url: "{{ route('banners.store') }}",
                            method: "POST",
                            data: new FormData(form),
                            processData: false,
                            contentType: false,
                            headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                            success: function(response) {
                                Notiflix.Notify.success(response.message || 'Banner created successfully!');
                                form.reset();
                                setTimeout(function() {
                                    window.location.href = "{{ route('banners.index') }}";
                                }, 800);
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors || {};
                                    $.each(errors, function(key, value) {
                                        $('.error-' + key.replace(/\./g, '_')).text(value[0]);
                                    });
                                    Notiflix.Notify.failure('Please correct the errors and try again.');
                                } else {
                                    Notiflix.Report.failure('Error', 'Something went wrong, please try later.', 'Close');
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
