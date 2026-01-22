<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="banners"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Banners') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">

                        {{-- Header + Add Button --}}
                        <div
                            class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 flex-grow-1">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Banners Table') }}</h6>
                            </div>
                        </div>
                        <!--<div class=" me-3 my-3 text-end">-->
                        <!--    <a class="btn bg-gradient-dark mb-0" href="{{ route('banners.create') }}"><i-->
                        <!--            class="material-icons text-sm">add</i>&nbsp;&nbsp;Add New-->
                        <!--        Banner</a>-->
                        <!--</div>-->
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Title') }}</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Image') }}</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                {{ __('Description') }}</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Created At') }}</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="bannersTable">
                                        @foreach ($banners as $banner)
                                            <tr id="banner-{{ $banner->id }}">
                                                <td class="px-2 py-1">{{ $banner->title }}</td>
                                                <td class="px-2 py-1"><img
                                                        src="{{ $banner->image_path }}"
                                                        alt="Banner"
                                                        style="width:50px;height:50px;border-radius:5px;"></td>
                                                <td>{{ $banner->desc }}</td>
                                                <td class="text-center">{{ $banner->created_at->format('d/m/Y') }}</td>
                                                <td class="align-middle">
                                                    <a rel="tooltip" class="btn btn-success btn-link"
                                                        href="{{ route('banners.edit', $banner->id) }}"
                                                        data-original-title="" title="">
                                                        <i class="material-icons">edit</i>
                                                        <div class="ripple-container"></div>
                                                    </a>

                                                    <!--<form action="{{ route('banners.destroy', $banner->id) }}" method="POST"-->
                                                    <!--    class="d-inline delete-form">-->
                                                    <!--    @csrf-->
                                                    <!--    @method('DELETE')-->
                                                    <!--    <button type="button" class="btn btn-sm btn-danger delete-btn">-->
                                                    <!--        <i class="bi bi-trash"></i> Delete-->
                                                    <!--    </button>-->
                                                    <!--</form>-->

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end flex-wrap mt-2">
                                    {{ $banners->links('pagination::bootstrap-5') }}
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm("{{ __('Are you sure you want to delete this banner?') }}")) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>

</x-layout>
