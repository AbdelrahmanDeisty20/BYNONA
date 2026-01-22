<x-layout bodyClass="g-sidenav-show bg-gray-200">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css" />
    <x-navbars.sidebar activePage="categories"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

        <x-navbars.navs.auth titlePage="Sort Categories"></x-navbars.navs.auth>

        <div class="container-fluid py-4">

            <div class="card">
                <div class="card-header pb-0">
                    <h6>Sort Categories</h6>
                    <small class="text-muted">Drag & Drop to reorder</small>
                </div>

                <div class="card-body pt-3">

                    <ul id="sortList" class="list-group">
                        @foreach ($categories as $category)
                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                data-id="{{ $category->id }}">
                                <span>{{ $category->name_en }} / {{ $category->name_ar }}</span>
                                <i class="fas fa-arrows-alt"></i>
                            </li>
                        @endforeach
                    </ul>

                    <button id="saveOrder" class="btn btn-primary mt-3">
                        Save Order
                    </button>

                </div>
            </div>

        </div>

    </main>
</x-layout>

{{-- SortableJS --}}
        <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    let el = document.getElementById('sortList');

    let sortable = Sortable.create(el, {
        animation: 150,
        ghostClass: 'bg-light'
    });

    document.getElementById('saveOrder').addEventListener('click', function() {

        let order = [];
        document.querySelectorAll('#sortList li').forEach(item => {
            order.push(item.getAttribute('data-id'));
        });

        fetch("{{ route('dashboard.categories.sort.update') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
            },
            body: JSON.stringify({ order: order })
        }).then(res => res.json()).then(data => {
            Notiflix.Notify.success("Order updated successfully!");
        });
    });
</script>
