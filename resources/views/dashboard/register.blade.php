<x-layout bodyClass="">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css"/>


    <div>
        <div class="container position-sticky z-index-sticky top-0">
            <div class="row">
                <div class="col-12">
                    <!-- Navbar -->
                    <x-navbars.navs.guest signin='static-sign-in' signup='static-sign-up'></x-navbars.navs.guest>
                    <!-- End Navbar -->
                </div>
            </div>
        </div>
        <main class="main-content  mt-0">
            <section>
                <div class="page-header min-vh-100">
                    <div class="container">
                        <div class="row">
                            <div
                                class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
                                <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center"
                                    style="background-image: url('{{ asset('dashboard/assets') }}/img/illustrations/illustration-signup.jpg'); background-size: cover;">
                                </div>
                            </div>
                            <div
                                class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
                                <div class="card card-plain">
                                    <div class="card-header">
                                        <h4 class="font-weight-bolder">Sign Up</h4>
                                        <p class="mb-0">Enter your email and password to register</p>
                                    </div>
                                    <div class="card-body">
                                        <form id="registerForm">
                                            @csrf
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">{{ __('First Name') }}</label>
                                                <input type="text" class="form-control" name="first_name">
                                            </div>
                                            <span class="text-danger error-first_name"></span>
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">{{ __('Last Name') }}</label>
                                                <input type="text" class="form-control" name="last_name">
                                            </div>
                                            <span class="text-danger error-last_name"></span>
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email">
                                            </div>
                                            <span class="text-danger error-email"></span>
                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">phone</label>
                                                <input type="text" class="form-control" name="phone">
                                            </div>
                                            <span class="text-danger error-phone"></span>

                                            <div class="input-group input-group-outline mb-3">
                                                <label class="form-label">Password</label>
                                                <input type="password" class="form-control" name="password">
                                            </div>
                                            <span class="text-danger error-password"></span>
                                            <div class="form-check form-check-info text-start ps-0">
                                                <input class="form-check-input" type="checkbox" name="terms"
                                                    value="" id="terms">
                                                <label class="form-check-label" for="terms">
                                                    I agree the <a href="javascript:;"
                                                        class="text-dark font-weight-bolder">Terms and Conditions</a>
                                                </label>
                                            </div>
                                            <span class="text-danger error-terms"></span>
                                            <div class="text-center">
                                                <button type="submit"
                                                    class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0">Sign
                                                    Up</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                        <p class="mb-2 text-sm mx-auto">
                                            Already have an account?
                                            <a href="{{route("dashboard.login")}}" class="text-primary text-gradient font-weight-bold">Sign
                                                in</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    @push('js')
<script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.js"></script>
<script>
$(document).ready(function() {

    // إعداد Notiflix
    Notiflix.Notify.init({
        position: 'right-top',
        distance: '10px',
        timeout: 5000,
        borderRadius: '10px',
        clickToClose: true,
    });

    $('#registerForm').validate({
        rules: {
            first_name: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            last_name: {
                required: true,
                minlength: 2,
                maxlength: 50
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
            password: {
                required: true,
                minlength: 6
            },
            terms: {
                required: true
            }
        },
        messages: {
            first_name: {
                required: "الرجاء إدخال الاسم الأول",
                minlength: "يجب أن يكون الاسم حرفين على الأقل",
                maxlength: "الاسم يجب ألا يزيد عن 50 حرف"
            },
            last_name: {
                required: "الرجاء إدخال الاسم الأخير",
                minlength: "يجب أن يكون الاسم حرفين على الأقل",
                maxlength: "الاسم يجب ألا يزيد عن 50 حرف"
            },
            email: {
                required: "الرجاء إدخال البريد الإلكتروني",
                email: "الرجاء إدخال بريد إلكتروني صالح"
            },
            phone: {
                required: "الرجاء إدخال رقم الهاتف",
                digits: "يجب إدخال أرقام فقط",
                minlength: "الرقم يجب ألا يقل عن 10 أرقام",
                maxlength: "الرقم يجب ألا يزيد عن 15 رقم"
            },
            password: {
                required: "الرجاء إدخال كلمة المرور",
                minlength: "كلمة المرور يجب أن تكون 6 أحرف على الأقل"
            },
            terms: {
                required: "يجب قبول الشروط والأحكام"
            }
        },

        errorElement: "span",
        errorPlacement: function(error, element) {
            let name = element.attr("name");
            $(".error-" + name).html(error);
        },

        submitHandler: function(form) {

            let btn = $(form).find('button[type="submit"]');
            btn.prop('disabled', true).text('جاري التسجيل...');

            $.ajax({
                url: "{{ route('dashboard.registerSend') }}",
                type: "POST",
                data: new FormData(form),
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function(response) {
                    Notiflix.Notify.success(response.message || 'تم التسجيل بنجاح!');
                    form.reset();
                    $('.text-danger').html('');

                    setTimeout(function() {
                        window.location.href = "{{ route('dashbord.dashboard') }}";
                    }, 1000);
                },

                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(key, value) {
                            $('.error-' + key).text(value[0]);
                        });

                        Notiflix.Notify.failure('يرجى تصحيح الأخطاء والمحاولة مرة أخرى.');
                    } else {
                        Notiflix.Report.failure('خطأ', 'حدث خطأ ما، يرجى المحاولة لاحقًا.', 'إغلاق');
                    }
                },

                complete: function() {
                    btn.prop('disabled', false).text('Sign Up');
                }
            });
        }
    });
});
</script>
@endpush

</x-layout>
