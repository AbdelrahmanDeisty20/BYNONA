<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <x-navbars.sidebar activePage="settings"></x-navbars.sidebar>

    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <x-navbars.navs.auth titlePage="{{ __('Create Setting') }}"></x-navbars.navs.auth>

        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4"
                 style="background-image: url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1920&q=80');">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>

            <div class="card card-body mx-3 mx-md-4 mt-n6">
                <div class="card card-plain h-100">

                    <div class="card-header pb-0 p-3">
                        <h6>{{ __('Add Setting') }}</h6>
                    </div>

                    <div class="card-body p-3">

                        <form id="createSettingForm">
                            @csrf

                            <div class="row">

                                {{-- Shipping --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Shipping (%)</label>
                                    <input type="number"
                                           name="shipping"
                                           class="form-control border border-2 p-2"
                                           step="0.01"
                                           min="0">
                                    <span class="text-danger error-shipping"></span>
                                </div>

                                {{-- Governorate --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Governorate</label>
                                    <select name="governorate_id"
                                            class="form-control border border-2 p-2">
                                        <option value="">-- Select Governorate --</option>

                                        @forelse($governorates as $governorate)
                                            <option value="{{ $governorate->id }}">
                                                {{ $governorate->name }}
                                            </option>
                                        @empty
                                            <option disabled>No available governorates</option>
                                        @endforelse
                                    </select>
                                    <span class="text-danger error-governorate_id"></span>
                                </div>

                            </div>

                            <button type="submit" class="btn bg-gradient-dark mt-3">
                                {{ __('Submit') }}
                            </button>
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

                $("#createSettingForm").validate({
                    rules: {
                        shipping: {
                            required: true,
                            number: true,
                            min: 0
                        },
                        governorate_id: {
                            required: true
                        }
                    },

                    messages: {
                        shipping: {
                            required: "{{ __('Shipping is required') }}",
                            number: "{{ __('Must be a number') }}",
                            min: "{{ __('Must be 0 or greater') }}"
                        },
                        governorate_id: {
                            required: "{{ __('Please select a governorate') }}"
                        }
                    },

                    errorElement: "span",
                    errorPlacement: function (error, element) {
                        let name = element.attr("name");
                        $(".error-" + name).html(error);
                    },

                    submitHandler: function (form) {

                        $('span[class^="error-"]').html('');

                        let btn = $(form).find('button[type="submit"]');
                        btn.prop('disabled', true).text('Submitting...');

                        $.ajax({
                            url: "{{ route('settings.store') }}",
                            method: "POST",
                            data: $(form).serialize(),

                            success: function (response) {
                                Notiflix.Notify.success(response.message || 'Setting created successfully');

                                setTimeout(() => {
                                    window.location.href = "{{ route('settings.index') }}";
                                }, 800);
                            },

                            error: function (xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    $.each(errors, function (key, value) {
                                        $('.error-' + key).text(value[0]);
                                    });
                                    Notiflix.Notify.failure('Please fix validation errors');
                                } else {
                                    Notiflix.Report.failure('Error', 'Something went wrong!', 'Close');
                                }
                            },

                            complete: function () {
                                btn.prop('disabled', false).text('Submit');
                            }
                        });
                    }
                });

            });
        </script>
    @endpush
</x-layout>
