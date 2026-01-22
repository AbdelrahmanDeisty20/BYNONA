<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="user-management"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Users') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">

                        {{-- Header --}}
                        <div
                            class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 flex-grow-1">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Users Table') }}</h6>
                            </div>
                        </div>

                        {{-- Add New (Optional to use later) --}}
                        <div class="me-3 my-3 text-end">
                            <a class="btn bg-gradient-dark mb-0" href="{{ route('users.create') }}">
                                <i class="material-icons text-sm">add</i>&nbsp;&nbsp;{{ __('Add New User') }}
                            </a>
                        </div>

                        {{-- Table --}}
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('First Name') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Last Name') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Phone') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Email') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('User Type') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Verified') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Created At') }}</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>

                                    <tbody id="usersTable">
                                        @foreach ($users as $user)
                                            <tr id="user-{{ $user->id }}">

                                                <td class="px-2 py-1">{{ $user->first_name }}</td>
                                                <td class="px-2 py-1">{{ $user->last_name }}</td>
                                                <td class="px-2 py-1">{{ $user->phone }}</td>
                                                <td class="px-2 py-1">{{ $user->email }}</td>
                                                <td class="px-2 py-1">
                                                    @if($user->user_type == 'user')
                                                        <span class="badge bg-secondary">{{ __('User') }}</span>
                                                    @else
                                                        @foreach($user->roles as $role)
                                                            <span class="badge bg-info">
                                                                {{ $role->name == 'subAdmin' ? __('Sub Admin') : __($role->name) }}
                                                            </span>
                                                        @endforeach
                                                    @endif
                                                </td>

                                                <td class="px-2 py-1">
                                                    @if($user->is_verfived)
                                                        <span class="badge bg-success">{{ __('Verified') }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ __('Not Verified') }}</span>
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    {{ $user->created_at?->format('d/m/Y') ?? '---' }}
                                                </td>

                                                <td class="align-middle">
                                                    @php
                                                        $canEdit = true;
                                                        if(auth()->user()->hasRole('subAdmin') && $user->hasRole('admin')) {
                                                            $canEdit = false;
                                                        }
                                                    @endphp

                                                    @if($canEdit)
                                                        {{-- Edit --}}
                                                        <a rel="tooltip" class="btn btn-sm btn-success"
                                                           href="{{ route('users.edit', $user->id) }}">
                                                            <i class="material-icons text-sm">edit</i> {{ __('Edit') }}
                                                        </a>

                                                        {{-- Delete --}}
                                                        <form action="{{ route('users.destroy', $user->id) }}"
                                                              method="POST"
                                                              class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                    class="btn btn-sm btn-danger delete-btn">
                                                                <i class="bi bi-trash"></i> {{ __('Delete') }}
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-xs text-secondary">{{ __('No Actions') }}</span>
                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>

                                <div class="d-flex justify-content-end flex-wrap mt-2">
                                    {{ $users->links('pagination::bootstrap-5') }}
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>

    </main>

    <x-plugins></x-plugins>

    {{-- Confirm Delete --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm("{{ __('Are you sure you want to delete this user?') }}")) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>

</x-layout>
