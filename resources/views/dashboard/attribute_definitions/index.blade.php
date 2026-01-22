<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="attributes"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Attributes') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 flex-grow-1">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Attribute Definitions') }}</h6>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end align-items-center my-3 px-3">
                            <a class="btn bg-gradient-dark mb-0" href="{{ route('attributes.create') }}">
                                <i class="material-icons text-sm">add</i>&nbsp;&nbsp;{{ __('Add New Attribute') }}
                            </a>
                        </div>

                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Name (EN)') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Name (AR)') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Values Count') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Created At') }}</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attributes as $attribute)
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-weight-bold">{{ $attribute->name_en }}</td>
                                                <td class="px-4 py-3 text-sm font-weight-bold">{{ $attribute->name_ar }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $attribute->values_count }}</td>
                                                <td class="align-middle text-center text-sm">{{ $attribute->created_at->format('Y-m-d') }}</td>
                                                <td class="align-middle text-end px-4">
                                                    <a class="btn btn-link text-dark px-3 mb-0" href="{{ route('attributes.edit', $attribute->id) }}">
                                                        <i class="material-icons text-sm me-2">edit</i>{{ __('Edit') }}
                                                    </a>
                                                    <form action="{{ route('attributes.destroy', $attribute->id) }}" method="POST" class="d-inline delete-form">
                                                        @csrf @method('DELETE')
                                                        <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0 delete-btn">
                                                            <i class="material-icons text-sm me-2">delete</i>{{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="px-3 mt-3">
                                {{ $attributes->links('pagination::bootstrap-5') }}
                            </div>
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
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm("{{ __('Are you sure you want to delete this attribute?') }}")) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>
</x-layout>
