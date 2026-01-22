<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />

    <x-navbars.sidebar activePage="users"></x-navbars.sidebar>

    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <x-navbars.navs.auth titlePage="{{ __('Edit User') }}"></x-navbars.navs.auth>

        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1920&q=80');">
                <span class="mask bg-gradient-primary opacity-6"></span>
            </div>

            <div class="card card-body mx-3 mx-md-4 mt-n6">
                <div class="card card-plain h-100">

                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-3">{{ __('Edit User') }}: {{ $user->first_name }} {{ $user->last_name }}</h6>
                    </div>

                    <div class="card-body p-3">

                        <form id="editUserForm">
                            @csrf
                            @method('PUT')

                            <div class="row">

                                {{-- First Name --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('First Name') }}</label>
                                    <input type="text" name="first_name" class="form-control border border-2 p-2" value="{{ $user->first_name }}">
                                </div>
                                <span class="text-danger error-first_name"></span>

                                {{-- Last Name --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">{{ __('Last Name') }}</label>
                                    <input type="text" name="last_name" class="form-control border border-2 p-2" value="{{ $user->last_name }}">
                                </div>
                                <span class="text-danger error-last_name"></span>

                                {{-- Email --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control border border-2 p-2" value="{{ $user->email }}">
                                </div>
                                <span class="text-danger error-email"></span>

                                {{-- Phone --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control border border-2 p-2" value="{{ $user->phone }}">
                                </div>
                                <span class="text-danger error-phone"></span>

                                {{-- User Type (Role) --}}
                                @if(!auth()->user()->hasRole('subAdmin'))
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">{{ __('User Type') }}</label>
                                        <select name="user_type" id="user_type" class="form-control border border-2 p-2">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ __($role->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span class="text-danger error-user_type"></span>
                                @else
                                    <input type="hidden" name="user_type" value="{{ $user->roles->first()?->name }}">
                                @endif

                                {{-- Password --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Password ({{ __('Leave blank to keep current') }})</label>
                                    <input type="password" name="password" class="form-control border border-2 p-2">
                                </div>
                                <span class="text-danger error-password"></span>

                            </div>

                            @if(!auth()->user()->hasRole('subAdmin'))
                                <div id="permissionsSection" style="{{ $user->hasRole('subAdmin') ? '' : 'display: none;' }}">
                                    <hr class="dark horizontal my-4">

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">{{ __('Assign Permissions') }}</h6>
                                                <div class="form-check p-0">
                                                    <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                                                    <label class="form-check-label font-weight-bold" for="selectAllPermissions">
                                                        {{ __('Select All') }}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @foreach($permissions as $module => $modulePermissions)
                                                    <div class="col-md-4 mb-4">
                                                        <div class="card border border-1 shadow-none">
                                                            <div class="card-header p-2 bg-light border-bottom">
                                                                <h6 class="text-xs font-weight-bold text-uppercase mb-0">{{ __($module) }}</h6>
                                                            </div>
                                                            <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                                                @foreach($modulePermissions as $permission)
                                                                    @php
                                                                        $isRestricted = in_array($permission->name, ['delete_orders', 'delete_users', 'edit_users', 'edit_contacts']);
                                                                        $isSubAdmin = $user->hasRole('subAdmin');
                                                                    @endphp
                                                                    <div class="form-check {{ $isRestricted ? 'restricted-permission' : '' }}">
                                                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm-{{ $permission->id }}" {{ $user->hasPermissionTo($permission->name) ? 'checked' : '' }} {{ ($isRestricted && $isSubAdmin) ? 'disabled' : '' }}>
                                                                        <label class="form-check-label text-xs" for="perm-{{ $permission->id }}">
                                                                            {{ __(str_replace('_', ' ', $permission->name)) }}
                                                                        @if($isRestricted)
                                                                            <br><small class="text-danger restriction-note {{ $isSubAdmin ? '' : 'd-none' }}" style="font-size: 0.7rem;">({{ __('Fixed restriction for Sub Admin') }})</small>
                                                                        @endif
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <button type="submit" class="btn bg-gradient-dark mt-3">{{ __('Update User') }}</button>

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

                // Toggle Permissions Section based on user_type
                $('#user_type').on('change', function() {
                    const type = $(this).val();
                    if (type === 'subAdmin') {
                        $('#permissionsSection').slideDown();
                        $('.restricted-permission').find('.form-check-input').prop('disabled', true).prop('checked', false);
                        $('.restriction-note').removeClass('d-none');
                    } else {
                        $('#permissionsSection').slideUp();
                        $('.restricted-permission').find('.form-check-input').prop('disabled', false);
                        $('.restriction-note').addClass('d-none');
                    }
                }).trigger('change');

                // Select All Logic
                $('#selectAllPermissions').on('change', function() {
                    const isChecked = $(this).prop('checked');
                    // Select all non-disabled checkboxes
                    $('#permissionsSection').find('.form-check-input').not('#selectAllPermissions').not(':disabled').prop('checked', isChecked);
                });

                // Update Select All on individual changes & initial load
                function updateSelectAllState() {
                    const total = $('#permissionsSection .form-check-input').not('#selectAllPermissions').not(':disabled').length;
                    const checked = $('#permissionsSection .form-check-input').not('#selectAllPermissions').not(':disabled').filter(':checked').length;
                    $('#selectAllPermissions').prop('checked', total === checked && total > 0);
                }

                $(document).on('change', '#permissionsSection .form-check-input:not(#selectAllPermissions)', updateSelectAllState);
                updateSelectAllState(); // Initial check

                $("#editUserForm").submit(function (e) {
                    e.preventDefault();
                    $('span[class^="error-"]').html('');
                    let btn = $(this).find('button[type="submit"]');
                    btn.prop('disabled', true).text('Updating ...');

                    $.ajax({
                        url: "{{ route('users.update', $user->id) }}",
                        method: "POST",
                        data: new FormData(this),
                        processData: false,
                        contentType: false,

                        success: function (response) {
                            Notiflix.Notify.success(response.message || 'User updated successfully!');
                            setTimeout(() => {
                                window.location.href = "{{ route('users.index') }}";
                            }, 700);
                        },

                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function (key, value) {
                                    $('.error-' + key).text(value[0]);
                                });
                                Notiflix.Notify.failure('Please fix the form errors.');
                            } else {
                                Notiflix.Notify.failure('Something went wrong!');
                            }
                        },

                        complete: function () {
                            btn.prop('disabled', false).text('Update User');
                        }
                    });
                });

            });
        </script>
    @endpush

</x-layout>
