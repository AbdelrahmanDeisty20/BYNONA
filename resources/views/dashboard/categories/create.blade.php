<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <x-navbars.sidebar activePage="categories"></x-navbars.sidebar>
    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage='Create Category'></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
                <span class="mask  bg-gradient-primary  opacity-6"></span>
            </div>
            <div class="card card-body mx-3 mx-md-4 mt-n6">

                <div class="card card-plain h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-md-8 d-flex align-items-center">
                                <h6 class="mb-3">Add Category</h6>
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
                        @if (Session::has('demo'))
                            <div class="row">
                                <div class="alert alert-danger alert-dismissible text-white" role="alert">
                                    <span class="text-sm">{{ Session::get('demo') }}</span>
                                    <button type="button" class="btn-close text-lg py-3 opacity-10"
                                        data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                        <form id="createCategoryForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Name (English)</label>
                                    <input type="text" name="name_en" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-name_en"></span>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Name (Arabic)</label>
                                    <input type="text" name="name_ar" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-name_ar"></span>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Image</label>
                                    <input type="file" name="image" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-image"></span>

                                <!--<div class="mb-3 col-md-6">-->
                                <!--    <label class="form-label">Main Category</label>-->
                                <!--    <select name="parent_id" class="form-control border border-2 p-2">-->
                                <!--        <option value="">-- Select Main Category --</option>-->

                                <!--        @foreach ($categories as $category)-->
                                <!--            <option value="{{ $category->id }}">{{ $category->name }}</option>-->
                                <!--        @endforeach-->

                                <!--    </select>-->
                                <!--</div>-->

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
                $("#createCategoryForm").validate({
                    rules: {
                        name_en: {
                            required: true,
                            maxlength: 255
                        },
                        name_ar: {
                            required: true,
                            maxlength: 255
                        },
                        image: {
                            required: true,
                            extension: "jpg|jpeg|png|webp"
                        },
                        // parent_id: {
                        //     required: false
                        // }
                    },

                    messages: {
                        name_en: {
                            required: "English name is required.",
                            maxlength: "English name must not exceed 255 characters."
                        },
                        name_ar: {
                            required: "Arabic name is required.",
                            maxlength: "Arabic name must not exceed 255 characters."
                        },
                        image: {
                            required: "Image is required.",
                            extension: "Image must be one of: jpg, jpeg, png, webp."
                        }
                    },
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        let name = element.attr("name");
                        $(".error-" + name).html(error);
                    },
                    submitHandler: function(form) {
                        // clear old errors
                        $('span[class^="error-"]').html('');

                        let btn = $(form).find('button[type="submit"]');
                        btn.prop('disabled', true).text('Submitting ...');

                        $.ajax({
                            url: "{{ route('categories.store') }}",
                            method: "POST",
                            data: new FormData(form),
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('input[name="_token"]').val()
                            },
                            success: function(response) {
                                Notiflix.Notify.success(response.message || 'Category created successfully!');

                                form.reset();

                                setTimeout(function() {
                                    window.location.href = "{{ route('categories.index') }}";
                                }, 800);
                            },

                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors || {};

                                    $.each(errors, function(key, value) {
                                        $('.error-' + key).text(value[0]);
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
