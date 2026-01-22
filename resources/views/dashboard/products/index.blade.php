<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="products"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Products') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">

                        {{-- Header --}}
                        <div
                            class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 flex-grow-1">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Products Table') }}</h6>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 px-3 flex-wrap gap-2">
                            {{-- Search Form --}}
                            <form method="GET" action="{{ route('products.index') }}" class="d-flex mb-0 align-items-center">
                                <div class="input-group input-group-sm input-group-outline me-2 {{ request('q') ? 'is-filled' : '' }}" style="width: 300px;">
                                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Search products, brands, or categories...') }}">
                                </div>
                                <button type="submit" class="btn btn-sm bg-gradient-primary mb-0 shadow-sm px-4">{{ __('Search') }}</button>
                                @if(request('q'))
                                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-link text-secondary mb-0 px-2" title="{{ __('Clear Search') }}">
                                        <i class="material-icons text-sm">close</i>
                                    </a>
                                @endif
                            </form>
                            <div class="d-flex gap-2 align-items-center">
                                {{-- Import Excel Button --}}
                                <button type="button" class="btn bg-gradient-info mb-0 shadow-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="material-icons text-sm me-1">upload_file</i> {{ __('Import Products') }}
                                </button>
                                
                                {{-- Export Excel --}}
                                <a href="{{ route('products.export') }}" class="btn bg-gradient-success mb-0 shadow-sm d-flex align-items-center">
                                    <i class="material-icons text-sm me-1">download</i> {{ __('Export Excel') }}
                                </a>

                                {{-- Add New Product --}}
                                <a class="btn bg-gradient-dark mb-0 shadow-sm d-flex align-items-center" href="{{ route('products.create') }}">
                                    <i class="material-icons text-sm me-1">add_circle</i> {{ __('Add New Product') }}
                                </a>
                            </div>

                            {{-- Import Modal --}}
                            <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title font-weight-bold" id="importModalLabel">{{ __('Bulk Product Import') }}</h5>
                                            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" id="bulkImportForm">
                                            @csrf
                                            <div class="modal-body pt-4">
                                                <div class="mb-4">
                                                    <label class="form-label font-weight-bold text-sm">{{ __('Excel File') }} <span class="text-danger">*</span></label>
                                                    <div class="input-group input-group-outline">
                                                        <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                                                    </div>
                                                    <small class="text-muted text-xxs mt-1 d-block">{{ __('Supported types: .xlsx, .xls, .csv') }}</small>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="form-label font-weight-bold text-sm">{{ __('Images ZIP File (Optional)') }}</label>
                                                    <div class="input-group input-group-outline">
                                                        <input type="file" name="zip_file" class="form-control" accept=".zip">
                                                    </div>
                                                    <small class="text-muted text-xxs mt-1 d-block">{{ __('Contains images matching filenames in the Excel file.') }}</small>
                                                </div>

                                                <div class="bg-gray-100 p-3 border-radius-lg">
                                                    <h6 class="text-sm font-weight-bold mb-2"><i class="material-icons text-sm me-1 text-info">info</i> {{ __('How it works:') }}</h6>
                                                    <ul class="text-xs mb-0 ps-3">
                                                        <li>{{ __('Type "retail" or "wholesale" in the "type" column.') }}</li>
                                                        <li>{{ __('Use full URLs for images, or just filenames if providing a ZIP.') }}</li>
                                                        <li>{{ __('Multiple images can be separated by commas in the images column.') }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-sm btn-outline-secondary mb-0" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                <button type="submit" class="btn btn-sm bg-gradient-info mb-0" id="submitImportBtn">
                                                    <span id="importBtnText">{{ __('Start Import') }}</span>
                                                    <div id="importSpinner" class="spinner-border spinner-border-sm text-white d-none ms-2" role="status"></div>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <style>
                            #productsTable tr:hover {
                                background-color: #f8f9fa;
                                transition: background-color 0.2s;
                            }
                            .gap-2 { gap: 0.5rem; }
                        </style>


                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Product') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ __('Brand') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Variants') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Total Stock') }}</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">{{ __('Categories') }}</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Date') }}</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>

                                    <tbody id="productsTable">
                                        @foreach ($products as $product)
                                            <tr id="product-{{ $product->id }}">
                                                <td class="px-3">
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $product->name }}</h6>
                                                            <p class="text-xxs text-secondary mb-0 text-uppercase font-weight-bold">{{ $product->type }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="text-xs font-weight-bold text-dark">{{ $product->brand->name ?? '-' }}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="badge badge-sm border border-info text-info bg-transparent">
                                                        {{ $product->variants->count() }} {{ __('Variants') }}
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @php $totalStock = $product->variants->sum('stock'); @endphp
                                                    <span class="badge badge-sm bg-{{ $totalStock > 0 ? 'success' : 'danger' }} shadow-{{ $totalStock > 0 ? 'success' : 'danger' }}">
                                                        {{ $totalStock }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="text-xs font-weight-bold text-secondary">
                                                        {{ $product->categories->pluck('name')->unique()->implode(', ') ?: '-' }}
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">{{ $product->created_at->format('M d, Y') }}</span>
                                                </td>
                                                <td class="align-middle text-end px-4">
                                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-link text-info p-0 mb-0" data-bs-toggle="tooltip" title="{{ __('View') }}">
                                                            <i class="material-icons text-lg">visibility</i>
                                                        </a>
                                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-link text-success p-0 mb-0" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                            <i class="material-icons text-lg">edit</i>
                                                        </a>
                                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline delete-form">
                                                            @csrf @method('DELETE')
                                                            <button type="button" class="btn btn-link text-danger p-0 mb-0 delete-btn" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                                <i class="material-icons text-lg">delete_outline</i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-end flex-wrap mt-2">
                                    {{ $products->links('pagination::bootstrap-5') }}
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
            // Bulk Import Loading State
            const importForm = document.getElementById('bulkImportForm');
            if (importForm) {
                importForm.addEventListener('submit', function() {
                    const btn = document.getElementById('submitImportBtn');
                    const text = document.getElementById('importBtnText');
                    const spinner = document.getElementById('importSpinner');
                    
                    btn.disabled = true;
                    text.innerText = "{{ __('Importing...') }}";
                    spinner.classList.remove('d-none');
                });
            }

            // Deletion confirm
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm("{{ __('Are you sure you want to delete this product?') }}")) {
                        this.closest('form').submit();
                    }
                });
            });

            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>

</x-layout>
@push('js')
    <script>
        $(document).ready(function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '{{ __("Success") }}',
                    text: '{{ session("success") }}',
                    confirmButtonText: '{{ __("OK") }}'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("Error") }}',
                    text: '{{ session("error") }}',
                    confirmButtonText: '{{ __("OK") }}'
                });
            @endif
        });
    </script>
@endpush
