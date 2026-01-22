<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <x-navbars.sidebar activePage="banners"></x-navbars.sidebar>

    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">

        <x-navbars.navs.auth titlePage='Edit Banner'></x-navbars.navs.auth>

        <div class="container-fluid px-2 px-md-4">

            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1485988412941-77a35537dae4?q=80');">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>

            <div class="card card-body mx-3 mx-md-4 mt-n6">

                <div class="card card-plain h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-3">Edit Banner</h6>
                    </div>

                    <div class="card-body p-3">

                        <form id="updateForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                {{-- Title English --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Title (English)</label>
                                    <input type="text" name="title_en" class="form-control border border-2 p-2"
                                        value="{{ old('title_en', $banner->title_en) }}">
                                </div>
                                <span class="text-danger error-title_en"></span>

                                {{-- Title Arabic --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Title (Arabic)</label>
                                    <input type="text" name="title_ar" class="form-control border border-2 p-2"
                                        value="{{ old('title_ar', $banner->title_ar) }}">
                                </div>
                                <span class="text-danger error-title_ar"></span>

                                {{-- Desc English --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Short Description (English)</label>
                                    <textarea name="short_desc_en" class="form-control border border-2 p-2" rows="2">{{ old('short_desc_en', $banner->short_desc_en) }}</textarea>
                                </div>
                                <span class="text-danger error-short_desc_en"></span>

                                {{-- Desc Arabic --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Short Description (Arabic)</label>
                                    <textarea name="short_desc_ar" class="form-control border border-2 p-2" rows="2">{{ old('short_desc_ar', $banner->short_desc_ar) }}</textarea>
                                </div>
                                <span class="text-danger error-short_desc_ar"></span>

                                {{-- Image --}}
                                <div class="mb-3 col-md-6">
                                    <input type="file" name="image" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-image"></span>

                            </div>

                            <button type="submit" class="btn bg-gradient-dark">Update</button>
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

                $("#updateForm").validate({
                    rules: {
                        title_en: { required: true, maxlength: 255 },
                        title_ar: { required: true, maxlength: 255 },
                        short_desc_en: { required: true },
                        short_desc_ar: { required: true },
                        image: { extension: "jpg|jpeg|png|webp" },
                    },

                    messages: {
                        title_en: { required: "العنوان الإنجليزي مطلوب" },
                        title_ar: { required: "العنوان العربي مطلوب" },
                        short_desc_en: { required: "الوصف الإنجليزي مطلوب" },
                        short_desc_ar: { required: "الوصف العربي مطلوب" },
                        image: { extension: "صيغة الصورة يجب أن تكون jpg أو jpeg أو png أو webp" },
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
                            url: "{{ route('banners.update', $banner->id) }}",
                            type: "POST",
                            data: new FormData(form),
                            processData: false,
                            contentType: false,

                            success: function(response) {
                                Notiflix.Notify.success(response.message || 'Banner updated successfully!');
                                setTimeout(() => {
                                    window.location.href = "{{ route('banners.index') }}";
                                }, 800);
                            },

                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors || {};
                                    $.each(errors, function(key, value) {
                                        $('.error-' + key).text(value[0]);
                                    });
                                    Notiflix.Notify.failure('Please correct the errors.');
                                } else {
                                    Notiflix.Report.failure('Error',
                                        'Something went wrong.', 'Close');
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
