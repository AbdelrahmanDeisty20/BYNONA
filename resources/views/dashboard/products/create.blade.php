<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <style>
        .variant-card {
            border: none;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            background-color: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .variant-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .variant-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            border-bottom: 1px solid #f0f2f5;
            padding-bottom: 0.75rem;
        }
        .attributes-section {
            background: #f8f9fa;
            padding: 1.25rem;
            border-radius: 0.75rem;
            margin-top: 1.25rem;
            border: 1px dashed #dee2e6;
        }
        .attribute-header {
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #7b809a;
            margin-bottom: 0.75rem;
            letter-spacing: 0.05rem;
        }
        .attribute-row {
            margin-bottom: 0.75rem;
            align-items: center;
        }
        .section-separator {
            height: 1px;
            background: linear-gradient(90deg, transparent, #dee2e6, transparent);
            margin: 2rem 0;
        }
        .input-group-static label { color: #344767 !important; font-weight: 600 !important; margin-bottom: 0.5rem; }
    </style>

    <x-navbars.sidebar activePage="products"></x-navbars.sidebar>

    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <x-navbars.navs.auth :titlePage="__('Create Product')"></x-navbars.navs.auth>

        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-250 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>

            <div class="card card-body mx-3 mx-md-4 mt-n6 shadow-blur overflow-hidden">
                <div class="row gx-4 mb-2">
                    <div class="col-auto">
                        <div class="avatar avatar-xl position-relative">
                            <i class="material-icons text-primary" style="font-size: 3rem;">add_business</i>
                        </div>
                    </div>
                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <h5 class="mb-1 font-weight-bolder">{{ __('Create New Product') }}</h5>
                            <p class="mb-0 font-weight-normal text-sm">{{ __('Add general info and define variants/specifications') }}</p>
                        </div>
                    </div>
                </div>

                <div class="card card-plain h-100">
                    <div class="card-body p-3">
                        <form id="createProductForm" enctype="multipart/form-data">
                            @csrf
                            
                            {{-- Product Info Section --}}
                            <div class="mb-5">
                                <h6 class="text-uppercase text-xs font-weight-bolder text-info mb-3 d-flex align-items-center">
                                    <i class="material-icons me-2">info_outline</i> {{ __('General Information') }}
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-static">
                                            <label>{{ __('Name (English)') }}</label>
                                            <input type="text" name="name_en" class="form-control" placeholder="e.g. iPhone 15 Pro">
                                        </div>
                                        <span class="text-danger error-name_en text-xs"></span>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-static">
                                            <label>{{ __('Name (Arabic)') }}</label>
                                            <input type="text" name="name_ar" class="form-control" placeholder="مثال: ايفون 15 برو">
                                        </div>
                                        <span class="text-danger error-name_ar text-xs"></span>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-static">
                                            <label>{{ __('Description (English)') }}</label>
                                            <textarea name="desc_en" class="form-control" rows="3"></textarea>
                                        </div>
                                        <span class="text-danger error-desc_en text-xs"></span>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="input-group input-group-static">
                                            <label>{{ __('Description (Arabic)') }}</label>
                                            <textarea name="desc_ar" class="form-control" rows="3"></textarea>
                                        </div>
                                        <span class="text-danger error-desc_ar text-xs"></span>
                                    </div>

                                    <div class="col-md-4 mb-4">
                                        <div class="input-group input-group-static">
                                            <label>{{ __('Categories') }}</label>
                                            <select name="category_id" class="form-control">
                                                <option value="">{{ __('-- Select Category --') }}</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="text-danger error-category_id text-xs"></span>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="input-group input-group-static">
                                            <label>{{ __('Brands') }}</label>
                                            <select name="brand_id" class="form-control">
                                                <option value="">{{ __('-- Select brand --') }}</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="text-danger error-brand_id text-xs"></span>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <div class="input-group input-group-static">
                                            <label>{{ __('Product Type') }}</label>
                                            <select name="type" id="product_type" class="form-control">
                                                <option value="retail">{{ __('Retail (قطاعي)') }}</option>
                                                <option value="wholesale">{{ __('Wholesale (جملة)') }}</option>
                                            </select>
                                        </div>
                                        <span class="text-danger error-type text-xs"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="section-separator"></div>

                            {{-- Variants Section --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-end mb-4">
                                    <div>
                                        <h6 class="text-uppercase text-xs font-weight-bolder text-info mb-1 d-flex align-items-center">
                                            <i class="material-icons me-2">layers</i> {{ __('Defined Variants') }}
                                        </h6>
                                        <p class="text-xs text-secondary mb-0">{{ __('Each product must have at least one variant.') }}</p>
                                    </div>
                                    <button type="button" id="addVariant" class="btn bg-gradient-dark btn-sm mb-0 shadow-sm">
                                        <i class="material-icons text-sm">add</i> {{ __('Add New Variant') }}
                                    </button>
                                </div>
                                
                                <span class="text-danger error-variants d-block mb-3 text-sm"></span>
                                <div id="variantsWrapper">
                                    {{-- Variants added via JS --}}
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-md-12 text-center">
                                    <button type="submit" id="submitBtn" class="btn bg-gradient-primary btn-lg px-5 shadow-primary">
                                        <i class="material-icons me-2">rocket_launch</i> {{ __('Create Everything Now') }}
                                    </button>
                                </div>
                            </div>
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
                const attributeDefinitions = @json($attributes);
                let variantCount = 0;

                function getVariantHtml(index) {
                    const isWholesale = $('#product_type').val() === 'wholesale';
                    return `
                    <div class="variant-card" data-index="${index}">
                        <div class="variant-header">
                            <div>
                                <span class="badge badge-sm bg-gradient-info me-2">#${index + 1}</span>
                                <span class="text-xs font-weight-bold text-uppercase text-secondary">{{ __('New Variant Definition') }}</span>
                            </div>
                            <button type="button" class="btn btn-link text-danger remove-variant p-0 m-0 text-xs font-weight-bold">
                                <i class="material-icons text-sm me-1">delete</i> {{ __('Remove') }}
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-4 retail-field" style="${isWholesale ? 'display:none;' : ''}">
                                <div class="input-group input-group-static">
                                    <label class="font-weight-bold">{{ __('Retail Price') }}</label>
                                    <input type="number" step="0.01" name="variants[${index}][retail_price]" class="form-control" placeholder="0.00">
                                </div>
                                <span class="text-danger error-variants-${index}-retail_price text-xxs"></span>
                            </div>
                            <div class="col-md-3 mb-4 wholesale-field" style="${!isWholesale ? 'display:none;' : ''}">
                                <div class="input-group input-group-static">
                                    <label class="font-weight-bold">{{ __('Wholesale Price') }}</label>
                                    <input type="number" step="0.01" name="variants[${index}][wholesale_price]" class="form-control" placeholder="0.00">
                                </div>
                                <span class="text-danger error-variants-${index}-wholesale_price text-xxs"></span>
                            </div>
                            <div class="col-md-3 mb-4 wholesale-field" style="${!isWholesale ? 'display:none;' : ''}">
                                <div class="input-group input-group-static">
                                    <label class="font-weight-bold">{{ __('Min Quantity') }}</label>
                                    <input type="number" name="variants[${index}][min_quantity]" class="form-control" value="1">
                                </div>
                                <span class="text-danger error-variants-${index}-min_quantity text-xxs"></span>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="input-group input-group-static">
                                    <label class="font-weight-bold">{{ __('Opening Stock') }}</label>
                                    <input type="number" name="variants[${index}][stock]" class="form-control" value="0">
                                </div>
                                <span class="text-danger error-variants-${index}-stock text-xxs"></span>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="input-group input-group-static">
                                    <label class="font-weight-bold">{{ __('Main Visual') }}</label>
                                    <input type="file" name="variants[${index}][main_image_file]" class="form-control">
                                </div>
                                <span class="text-danger error-variants-${index}-main_image_file text-xxs"></span>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="input-group input-group-static">
                                    <label class="font-weight-bold">{{ __('Additional Gallery') }}</label>
                                    <input type="file" name="variants[${index}][gallery_files][]" class="form-control" multiple>
                                </div>
                                <span class="text-danger error-variants-${index}-gallery_files text-xxs"></span>
                            </div>
                        </div>

                        <div class="attributes-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-xs font-weight-bolder text-dark m-0"><i class="material-icons text-xs me-1">settings</i> {{ __('Physical Attributes') }}</h6>
                                <button type="button" class="btn btn-outline-info btn-xs mb-0 add-attribute">
                                    <i class="material-icons text-xs me-1">add</i> {{ __('Add New') }}
                                </button>
                            </div>
                            <div class="row attribute-header d-none d-md-flex px-2">
                                <div class="col-md-5">{{ __('Label / Type') }}</div>
                                <div class="col-md-5">{{ __('Value / Choice') }}</div>
                                <div class="col-md-2"></div>
                            </div>
                            <div class="attributesWrapper"></div>
                        </div>
                    </div>`;
                }

                function getAttributeRowHtml(variantIndex, attrIndex) {
                    let keyOptions = attributeDefinitions.map(attr => `<option value="${attr.id}" data-en="${attr.name_en}" data-ar="${attr.name_ar}">${attr.name_en} / ${attr.name_ar}</option>`).join('');
                    
                    return `
                    <div class="row attribute-row px-2">
                        <div class="col-md-5">
                            <div class="input-group input-group-static mb-2">
                                <select class="form-control key-selector">
                                    <option value="">{{ __('-- Select --') }}</option>
                                    ${keyOptions}
                                </select>
                            </div>
                            <input type="hidden" name="variants[${variantIndex}][attributes][${attrIndex}][key_en]" class="key-en">
                            <input type="hidden" name="variants[${variantIndex}][attributes][${attrIndex}][key_ar]" class="key-ar">
                            <span class="text-danger error-variants-${variantIndex}-attributes-${attrIndex}-key_en text-xxs"></span>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group input-group-static mb-2">
                                <select class="form-control value-selector" disabled>
                                    <option value="">{{ __('-- Select --') }}</option>
                                </select>
                            </div>
                            <input type="hidden" name="variants[${variantIndex}][attributes][${attrIndex}][value_en]" class="value-en">
                            <input type="hidden" name="variants[${variantIndex}][attributes][${attrIndex}][value_ar]" class="value-ar">
                            <span class="text-danger error-variants-${variantIndex}-attributes-${attrIndex}-value_en text-xxs"></span>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-link text-danger remove-attribute p-0 m-0" title="{{ __('Remove Attribute') }}">
                                <i class="material-icons">delete_outline</i>
                            </button>
                        </div>
                    </div>`;
                }

                function addInitialVariant() {
                    let html = getVariantHtml(variantCount);
                    $('#variantsWrapper').append(html);
                    let variantCard = $(`.variant-card[data-index="${variantCount}"]`);
                    variantCard.find('.attributesWrapper').append(getAttributeRowHtml(variantCount, 0));
                    updateDisabledOptions(variantCard);
                    variantCount++;
                }

                // Initial variant
                addInitialVariant();

                $('#addVariant').click(function() {
                    addInitialVariant();
                });

                $(document).on('click', '.remove-variant', function() {
                    $(this).closest('.variant-card').remove();
                });

                $(document).on('click', '.add-attribute', function() {
                    let variantCard = $(this).closest('.variant-card');
                    let vIndex = variantCard.data('index');
                    let wrapper = variantCard.find('.attributesWrapper');
                    let aIndex = wrapper.find('.attribute-row').length;
                    wrapper.append(getAttributeRowHtml(vIndex, aIndex));
                    updateDisabledOptions(variantCard);
                });

                $(document).on('click', '.remove-attribute', function() {
                    $(this).closest('.attribute-row').remove();
                });

                $(document).on('change', '.key-selector', function() {
                    let row = $(this).closest('.attribute-row');
                    let variantCard = $(this).closest('.variant-card');
                    let keyId = $(this).val();
                    let valSelector = row.find('.value-selector');
                    let selected = $(this).find('option:selected');
                    
                    // Check if this key is already selected in other rows of same variant
                    let otherKeys = [];
                    variantCard.find('.key-selector').not(this).each(function() {
                        if ($(this).val()) otherKeys.push($(this).val());
                    });

                    if (keyId && otherKeys.includes(keyId)) {
                        Notiflix.Notify.warning('{{ __('This attribute is already added to this variant') }}');
                        $(this).val('');
                        valSelector.html('<option value="">{{ __('-- Select --') }}</option>').prop('disabled', true);
                        row.find('.key-en, .key-ar, .value-en, .value-ar').val('');
                        return;
                    }

                    row.find('.key-en').val(selected.data('en') || '');
                    row.find('.key-ar').val(selected.data('ar') || '');

                    if (!keyId) {
                        valSelector.html('<option value="">{{ __('-- Select --') }}</option>').prop('disabled', true);
                        row.find('.value-en, .value-ar').val('');
                        return;
                    }

                    let definition = attributeDefinitions.find(d => d.id == keyId);
                    let options = '<option value="">{{ __('-- Select --') }}</option>';
                    if (definition && definition.values) {
                        options += definition.values.map(v => `<option value="${v.id}" data-en="${v.value_en}" data-ar="${v.value_ar}">${v.value_en} / ${v.value_ar}</option>`).join('');
                    }
                    valSelector.html(options).prop('disabled', false);

                    // Update all selectors in this variant to disable current choices
                    updateDisabledOptions(variantCard);
                });

                function updateDisabledOptions(variantCard) {
                    let selectedValues = [];
                    variantCard.find('.key-selector').each(function() {
                        if ($(this).val()) selectedValues.push($(this).val());
                    });

                    variantCard.find('.key-selector').each(function() {
                        let currentVal = $(this).val();
                        $(this).find('option').each(function() {
                            let optVal = $(this).val();
                            if (optVal && optVal !== currentVal && selectedValues.includes(optVal)) {
                                $(this).prop('disabled', true);
                            } else {
                                $(this).prop('disabled', false);
                            }
                        });
                    });
                }

                $(document).on('change', '.value-selector', function() {
                    let row = $(this).closest('.attribute-row');
                    let selected = $(this).find('option:selected');
                    row.find('.value-en').val(selected.data('en') || '');
                    row.find('.value-ar').val(selected.data('ar') || '');
                });

                $('#product_type').change(function() {
                    let isWholesale = $(this).val() === 'wholesale';
                    if (isWholesale) {
                        $('.retail-field').hide();
                        $('.wholesale-field').show();
                    } else {
                        $('.retail-field').show();
                        $('.wholesale-field').hide();
                    }
                });

                $('#createProductForm').on('submit', function (e) {
                    e.preventDefault();
                    $(".text-danger").text("");
                    let btn = $('#submitBtn');
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> {{ __('Launching...') }}');
                    let formData = new FormData(this);

                    $.ajax({
                        url: "{{ route('products.store') }}",
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            Notiflix.Notify.success(response.message || '{{ __('Created successfully!') }}');
                            setTimeout(() => { window.location.href = "{{ route('products.index') }}"; }, 1000);
                        },
                        error: function (xhr) {
                            btn.prop('disabled', false).html('<i class="material-icons me-2">rocket_launch</i> {{ __('Create Everything Now') }}');
                            if (xhr.responseJSON?.errors) {
                               $.each(xhr.responseJSON.errors, function (key, value) {
                                    let formatted = key.replace(/\./g, '-');
                                    $('.error-' + formatted).text(value[0]);
                                });
                            } else {
                                Notiflix.Notify.failure('{{ __('Something went wrong') }}');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
</x-layout>
