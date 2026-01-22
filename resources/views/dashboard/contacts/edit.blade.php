<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />
    <x-navbars.sidebar activePage="contacts"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Edit Contact Information') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Edit Contact Information') }}</h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="updateContactForm">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('Email') }}</label>
                                        <input type="email" name="email" class="form-control border border-2 p-2"
                                            value="{{ old('email', $contact->email) }}">
                                        <span class="text-danger error-email"></span>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('Phone') }}</label>
                                        <input type="text" name="phone" class="form-control border border-2 p-2"
                                            value="{{ old('phone', $contact->phone) }}">
                                        <span class="text-danger error-phone"></span>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('WhatsApp') }}</label>
                                        <input type="text" name="whatsapp" class="form-control border border-2 p-2"
                                            value="{{ old('whatsapp', $contact->whatsapp) }}">
                                        <span class="text-danger error-whatsapp"></span>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('Facebook') }}</label>
                                        <input type="text" name="facebook" class="form-control border border-2 p-2"
                                            value="{{ old('facebook', $contact->facebook) }}">
                                        <span class="text-danger error-facebook"></span>
                                    </div>

                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">{{ __('Address (AR)') }}</label>
                                        <textarea name="address_ar" class="form-control border border-2 p-2"
                                            rows="3">{{ old('address_ar', $contact->address_ar) }}</textarea>
                                        <span class="text-danger error-address_ar"></span>
                                    </div>

                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">{{ __('Address (EN)') }}</label>
                                        <textarea name="address_en" class="form-control border border-2 p-2"
                                            rows="3">{{ old('address_en', $contact->address_en) }}</textarea>
                                        <span class="text-danger error-address_en"></span>
                                    </div>
                                </div>

                                <button type="submit" class="btn bg-gradient-dark">{{ __('Save') }}</button>
                                <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.js"></script>
        <script>
            $(document).ready(function() {
                $("#updateContactForm").validate({
                    rules: {
                        email: { required: true, email: true },
                        phone: { required: true },
                        address_ar: { required: true },
                        address_en: { required: true },
                    },
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        $(".error-" + element.attr("name")).html(error);
                    },
                    submitHandler: function(form) {
                        let btn = $(form).find('button[type="submit"]');
                        btn.prop('disabled', true).text("{{ __('Saving...') }}");

                        $.ajax({
                            url: "{{ route('contacts.update', $contact->id) }}",
                            type: "POST",
                            data: $(form).serialize(),
                            success: function(response) {
                                Notiflix.Notify.success(response.message);
                                setTimeout(() => {
                                    window.location.href = "{{ route('contacts.index') }}";
                                }, 1000);
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    $.each(errors, function(key, value) {
                                        $(".error-" + key).text(value[0]);
                                    });
                                } else {
                                    Notiflix.Notify.failure("{{ __('Something went wrong!') }}");
                                }
                            },
                            complete: function() {
                                btn.prop('disabled', false).text("{{ __('Save') }}");
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
</x-layout>
