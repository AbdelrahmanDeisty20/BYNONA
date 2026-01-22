<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <x-navbars.sidebar activePage="productBanners" />

    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <x-navbars.navs.auth titlePage="{{ __('Create Product Banner') }}" />

        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1503602642458-232111445657?auto=format&fit=crop&w=1920&q=80'); background-size: cover;">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>

            <div class="card card-body mx-3 mx-md-4 mt-n6">

                <div class="card card-plain h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-3">{{ __('Add Product Banner') }}</h6>
                    </div>

                    <div class="card-body p-3">

                        <form id="createBannerForm" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                    <label class="form-label">{{ __('Title (English)') }}</label>
                                    <input type="text" name="title_en" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-title_en"></span>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Title (Arabic)') }}</label>
                                    <input type="text" name="title_ar" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-title_ar"></span>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Description (English)') }}</label>
                                    <textarea name="desc_en" class="form-control border border-2 p-2"></textarea>
                                </div>
                                <span class="text-danger error-desc_en"></span>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea name="desc_ar" class="form-control border border-2 p-2"></textarea>
                                </div>
                                <span class="text-danger error-desc_ar"></span>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Price') }}</label>
                                    <input type="number" step="0.01" name="price" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-price"></span>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Image</label>
                                    <input type="file" name="image" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-image"></span>

                            </div>

                            <button type="submit" class="btn bg-gradient-dark mt-3">{{ __('Submit') }}</button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

        <x-footers.auth />
    </div>

    <x-plugins />

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.js"></script>

        <script>
            $(document).ready(function() {

                $("#createBannerForm").validate({
                    rules: {
                        title_en: { required: true, maxlength: 255 },
                        title_ar: { required: true, maxlength: 255 },
                        desc_en: { required: true },
                        desc_ar: { required: true },
                        price: { required: true, number: true },
                        image: { required: true, extension: "jpg|jpeg|png|webp" }
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
                            url: "{{ route('productBanners.store') }}",
                            method: "POST",
                            data: new FormData(form),
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('input[name="_token"]').val()
                            },

                            success: function(response) {
                                Notiflix.Notify.success(response.message || 'Banner created successfully!');
                                form.reset();

                                setTimeout(() => {
                                    window.location.href = "{{ route('productBanners.index') }}";
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
