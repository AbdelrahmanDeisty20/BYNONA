<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <x-navbars.sidebar activePage="offers"></x-navbars.sidebar>

    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <x-navbars.navs.auth titlePage="{{ __('Create Offer') }}"></x-navbars.navs.auth>

        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2');">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>

            <div class="card card-body mx-3 mx-md-4 mt-n6">
                <div class="card card-plain h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-3">{{ __('Add Offer') }}</h6>
                    </div>

                    <div class="card-body p-3">
                        <form id="createOfferForm">
                            @csrf
                            <div class="row">

                                {{-- Titles --}}
                                {{--  <div class="mb-3 col-md-6">
                                    <label class="form-label">Title (English)</label>
                                    <input type="text" name="title_en" class="form-control border border-2 p-2">
                                    <span class="text-danger error-title_en"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Title (Arabic)</label>
                                    <input type="text" name="title_ar" class="form-control border border-2 p-2">
                                    <span class="text-danger error-title_ar"></span>
                                </div>  --}}

                                {{-- Descriptions --}}
                                {{--  <div class="mb-3 col-md-6">
                                    <label class="form-label">Description (English)</label>
                                    <textarea name="desc_en" class="form-control border border-2 p-2"></textarea>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Description (Arabic)</label>
                                    <textarea name="desc_ar" class="form-control border border-2 p-2"></textarea>
                                </div>  --}}

                                {{-- Product Variant (Property) --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Product Variant') }}</label>
                                    <select name="property_id" class="form-control border border-2 p-2">
                                        <option value="">{{ __('-- Select Product Variant --') }}</option>
                                        @foreach ($properties as $property)
                                            <option value="{{ $property->id }}" 
                                                data-type="{{ $property->product->type ?? 'retail' }}"
                                                data-retail="{{ $property->retail_price }}"
                                                data-wholesale="{{ $property->wholesale_price }}">
                                                {{ $property->product->name_en ?? 'N/A' }} 
                                                @if($property->variantAttributes && $property->variantAttributes->count() > 0)
                                                    (
                                                    @foreach($property->variantAttributes ?? [] as $attr)
                                                        {{ $attr->key_en }}: {{ $attr->value_en }}{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                    )
                                                @endif
                                                - [{{ $property->product->type ?? 'N/A' }}]
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-property_id"></span>
                                </div>

                                {{-- Discounts --}}
                                <div class="mb-3 col-md-3" id="retail_discount_container">
                                    <label class="form-label">{{ __('Retail Discount (%)') }} <span id="retail_info" class="text-xs text-primary"></span></label>
                                    <input type="number" name="discount_retail" class="form-control border border-2 p-2">
                                    <span class="text-danger error-discount_retail"></span>
                                </div>

                                <div class="mb-3 col-md-3" id="wholesale_discount_container">
                                    <label class="form-label">{{ __('Wholesale Discount (%)') }} <span id="wholesale_info" class="text-xs text-primary"></span></label>
                                    <input type="number" name="discount_wholesale" class="form-control border border-2 p-2">
                                    <span class="text-danger error-discount_wholesale"></span>
                                </div>

                                {{-- Dates --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Start Date') }}</label>
                                    <input type="datetime-local" name="start" class="form-control border border-2 p-2">
                                    <span class="text-danger error-start"></span>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('End Date') }}</label>
                                    <input type="datetime-local" name="end" class="form-control border border-2 p-2">
                                    <span class="text-danger error-end"></span>
                                </div>

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
            $(document).ready(function () {
                function toggleDiscountFields() {
                    let selected = $('select[name="property_id"] option:selected');
                    let type = selected.data('type');
                    let retail = selected.data('retail');
                    let wholesale = selected.data('wholesale');

                    if (type === 'retail') {
                        $('#retail_discount_container').show();
                        $('#wholesale_discount_container').hide();
                        $('#retail_info').text('({{ __('Price') }}: ' + retail + ' {{ __('EGP') }})');
                        $('#wholesale_info').text('');
                    } else if (type === 'wholesale') {
                        $('#retail_discount_container').hide();
                        $('#wholesale_discount_container').show();
                        $('#retail_info').text('');
                        $('#wholesale_info').text('({{ __('Price') }}: ' + wholesale + ' {{ __('EGP') }})');

                    } else {
                        $('#retail_discount_container').hide();
                        $('#wholesale_discount_container').hide();
                    }
                }

                $('select[name="property_id"]').on('change', toggleDiscountFields);
                toggleDiscountFields();

                $("#createOfferForm").validate({
                    errorElement: "span", // مهم عشان ياخد اللون الاحمر
                    rules: {
                        property_id: { required: true },
                        discount_retail: { number: true },
                        discount_wholesale: { number: true },
                        start: { required: true },
                        end: { required: true }
                    },
                    messages: {
                        title_en: "{{ __('English title is required') }}",
                        title_ar: "{{ __('Arabic title is required') }}",
                        property_id: "{{ __('Product variant is required') }}",
                        discount_retail: "{{ __('Must be a number') }}",
                        discount_wholesale: "{{ __('Must be a number') }}",
                        start: "{{ __('Start date is required') }}",
                        end: "{{ __('End date is required') }}"
                    },
                    errorPlacement: function(error, element) {
                        let name = element.attr("name");
                        $(".error-" + name).html(error); // هنا بيركب الرسالة جوه span الاحمر
                    },
                    submitHandler: function(form) {
                        $('span[class^="error-"]').html('');
                        let btn = $(form).find('button[type="submit"]');
                        btn.prop('disabled', true).text('Submitting...');

                        $.ajax({
                            url: "{{ route('offers.store') }}",
                            type: "POST",
                            data: new FormData(form),
                            processData: false,
                            contentType: false,
                            headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                            success: function(res) {
                                Notiflix.Notify.success(res.message || 'Offer created successfully');
                                form.reset();
                                setTimeout(() => {
                                    window.location.href = "{{ route('offers.index') }}";
                                }, 700);
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    $.each(xhr.responseJSON.errors, function(key, val) {
                                        $('.error-' + key).text(val[0]); // هنا نفس طريقة البراند
                                    });
                                    Notiflix.Notify.failure('Please fix validation errors.');
                                } else {
                                    Notiflix.Notify.failure('Something went wrong');
                                }
                            },
                            complete: function() {
                                btn.prop('disabled', false).text('Submit');
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
</x-layout>
