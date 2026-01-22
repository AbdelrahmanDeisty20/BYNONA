<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <x-navbars.sidebar activePage="offers"></x-navbars.sidebar>
    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage='Edit Offer'></x-navbars.navs.auth>
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
                                <h6 class="mb-3">Edit Offer</h6>
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

                        <form id="updateOfferForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Title (English)</label>
                                    <input type="text" name="title_en" class="form-control border border-2 p-2"
                                        value="{{ old('title_en', $offer->title_en) }}">
                                    <span class="text-danger error-title_en"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Title (Arabic)</label>
                                    <input type="text" name="title_ar" class="form-control border border-2 p-2"
                                        value="{{ old('title_ar', $offer->title_ar) }}">
                                    <span class="text-danger error-title_ar"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Description (English)</label>
                                    <textarea name="desc_en" class="form-control border border-2 p-2">{{ old('desc_en', $offer->desc_en) }}</textarea>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Description (Arabic)</label>
                                    <textarea name="desc_ar" class="form-control border border-2 p-2">{{ old('desc_ar', $offer->desc_ar) }}</textarea>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Product</label>
                                    <select name="product_id" class="form-control border border-2 p-2">
                                        <option value="">-- Select Product --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ $offer->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-product_id"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Retail Price</label>
                                    <input type="number" step="0.01" name="retail_price" class="form-control border border-2 p-2"
                                        value="{{ old('retail_price', $offer->retail_price) }}">
                                    <span class="text-danger error-retail_price"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Wholesale Price</label>
                                    <input type="number" step="0.01" name="wholesale_price" class="form-control border border-2 p-2"
                                        value="{{ old('wholesale_price', $offer->wholesale_price) }}">
                                    <span class="text-danger error-wholesale_price"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="datetime-local" name="start" class="form-control border border-2 p-2"
                                        value="{{ old('start', \Carbon\Carbon::parse($offer->start)) }}">
                                    <span class="text-danger error-start"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="datetime-local" name="end" class="form-control border border-2 p-2"
                                        value="{{ old('start', \Carbon\Carbon::parse($offer->end)) }}">
                                    <span class="text-danger error-end"></span>
                                </div>

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
                $("#updateOfferForm").validate({
                    rules: {
                        title_en: { required: true, maxlength: 255 },
                        title_ar: { required: true, maxlength: 255 },
                        product_id: { required: true },
                        retail_price: { number: true },
                        wholesale_price: { number: true },
                        start: { required: true, date: true },
                        end: { required: true, date: true }
                    },
                    messages: {
                        title_en: { required: "{{ __('English title is required.') }}", maxlength: "{{ __('Max 255 characters.') }}" },
                        title_ar: { required: "{{ __('Arabic title is required.') }}", maxlength: "{{ __('Max 255 characters.') }}" },
                        product_id: { required: "{{ __('Product must be selected.') }}" },
                        retail_price: {  number: "{{ __('Must be a number.') }}" },
                        wholesale_price: {  number: "{{ __('Must be a number.') }}" },
                        start: { required: "{{ __('Start date is required.') }}", date: "{{ __('Invalid date.') }}" },
                        end: { required: "{{ __('End date is required.') }}", date: "{{ __('Invalid date.') }}" }
                    },
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        let name = element.attr("name");
                        $(".error-" + name).html(error);
                    },
                    submitHandler: function(form) {
                        $('span[class^="error-"]').html('');
                        let btn = $(form).find('button[type="submit"]');
                        btn.prop('disabled', true).text('Updating ...');

                        $.ajax({
                            url: "{{ route('offers.update', $offer->id) }}",
                            type: "POST",
                            data: new FormData(form),
                            processData: false,
                            contentType: false,
                            headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                            success: function(response) {
                                Notiflix.Notify.success(response.message || 'Offer updated successfully!');
                                setTimeout(function() {
                                    window.location.href = "{{ route('offers.index') }}";
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
                                btn.prop('disabled', false).text('Update');
                            }
                        });
                    }
                });
            })
        </script>
    @endpush
</x-layout>
