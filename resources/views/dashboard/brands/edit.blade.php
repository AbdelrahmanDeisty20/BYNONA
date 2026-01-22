<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <x-navbars.sidebar activePage="brands"></x-navbars.sidebar>

    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">

        <x-navbars.navs.auth titlePage="{{ __('Edit Brand') }}"></x-navbars.navs.auth>

        <div class="container-fluid px-2 px-md-4">

            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1920&q=80');">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>

            <div class="card card-body mx-3 mx-md-4 mt-n6">

                <div class="card card-plain h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-3">{{ __('Edit Brand') }}</h6>
                    </div>

                    <div class="card-body p-3">

                        <form id="updateBrandForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                {{-- Name English --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Name (English)') }}</label>
                                    <input type="text" name="name_en" class="form-control border border-2 p-2"
                                        value="{{ old('name_en', $brand->name_en) }}">
                                </div>
                                <span class="text-danger error-name_en"></span>

                                {{-- Name Arabic --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Name (Arabic)') }}</label>
                                    <input type="text" name="name_ar" class="form-control border border-2 p-2"
                                        value="{{ old('name_ar', $brand->name_ar) }}">
                                </div>
                                <span class="text-danger error-name_ar"></span>

                                <div class="mb-3 col-md-6">
                                    <input type="file" name="image" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-image"></span>

                            </div>

                            <button type="submit" class="btn bg-gradient-dark mt-3">{{ __('Update') }}</button>

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

                $("#updateBrandForm").validate({
                    rules: {
                        name_en: { required: true, maxlength: 255 },
                        name_ar: { required: true, maxlength: 255 },
                        image: { extension: "jpg|jpeg|png|webp" },
                    },

                    messages: {
                        name_en: { required: "English name is required." },
                        name_ar: { required: "Arabic name is required." },
                        image: { extension: "Allowed: jpg, jpeg, png, webp." },
                    },

                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        $(".error-" + element.attr("name")).html(error);
                    },

                    submitHandler: function(form) {
                        $('span[class^="error-"]').html('');

                        let btn = $(form).find('button[type="submit"]');
                        btn.prop('disabled', true).text('Updating...');

                        $.ajax({
                            url: "{{ route('brands.update', $brand->id) }}",
                            type: "POST",
                            data: new FormData(form),
                            processData: false,
                            contentType: false,
                            headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },

                            success: function(response) {
                                Notiflix.Notify.success(response.message || 'Brand updated successfully!');
                                setTimeout(() => {
                                    window.location.href = "{{ route('brands.index') }}";
                                }, 800);
                            },

                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors || {};
                                    $.each(errors, function(key, value) {
                                        $('.error-' + key).text(value[0]);
                                    });
                                    Notiflix.Notify.failure('Please fix validation errors.');
                                } else {
                                    Notiflix.Report.failure('Error', 'Something went wrong!', 'Close');
                                }
                            },

                            complete: function() {
                                btn.prop('disabled', false).text('Update');
                            }
                        });
                    }
                });
            });
        </script>
    @endpush

</x-layout>
