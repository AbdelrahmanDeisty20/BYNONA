<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="attributes"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Edit Attribute') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Edit Attribute Definition') }}</h6>
                            </div>
                        </div>
                        <div class="card-body px-4 pb-2">
                            <form action="{{ route('attributes.update', $attribute->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('Name (English)') }}</label>
                                        <input type="text" name="name_en" class="form-control border border-2 p-2" value="{{ old('name_en', $attribute->name_en) }}" required>
                                        @error('name_en') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">{{ __('Name (Arabic)') }}</label>
                                        <input type="text" name="name_ar" class="form-control border border-2 p-2" value="{{ old('name_ar', $attribute->name_ar) }}" required>
                                        @error('name_ar') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <hr class="my-3">
                                <h6 class="mb-3">{{ __('Attribute Values (Options)') }}</h6>
                                
                                <div id="values-wrapper">
                                    @foreach($attribute->values as $key => $val)
                                    <div class="row value-row mb-2">
                                        <div class="col-md-5">
                                            <label class="form-label text-xs d-md-none">{{ __('Value (English)') }}</label>
                                            <input type="text" name="values[{{$key}}][value_en]" class="form-control border border-2 p-2" value="{{ $val->value_en }}" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label text-xs d-md-none">{{ __('Value (Arabic)') }}</label>
                                            <input type="text" name="values[{{$key}}][value_ar]" class="form-control border border-2 p-2" value="{{ $val->value_ar }}" required>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-row mb-0">
                                                <i class="material-icons text-sm">close</i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <button type="button" class="btn btn-sm btn-info mt-2" id="add-value">
                                    <i class="material-icons text-sm">add</i> {{ __('Add Value') }}
                                </button>

                                <div class="text-end mt-4">
                                    <a href="{{ route('attributes.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                                    <button type="submit" class="btn bg-gradient-dark ms-2">{{ __('Update Attribute') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let wrapper = document.getElementById('values-wrapper');
            let addBtn = document.getElementById('add-value');
            let index = {{ $attribute->values->count() + 1 }};

            addBtn.addEventListener('click', function() {
                let tpl = `
                <div class="row value-row mb-2">
                    <div class="col-md-5">
                        <input type="text" name="values[${index}][value_en]" class="form-control border border-2 p-2" placeholder="Value (EN)" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="values[${index}][value_ar]" class="form-control border border-2 p-2" placeholder="Value (AR)" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-row mb-0">
                            <i class="material-icons text-sm">close</i>
                        </button>
                    </div>
                </div>`;
                wrapper.insertAdjacentHTML('beforeend', tpl);
                index++;
            });

            wrapper.addEventListener('click', function(e) {
                if (e.target.closest('.remove-row')) {
                    e.target.closest('.value-row').remove();
                }
            });
        });
    </script>
</x-layout>
