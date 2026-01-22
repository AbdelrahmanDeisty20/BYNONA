<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="contacts"></x-navbars.sidebar>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="{{ __('Contact Information') }}"></x-navbars.navs.auth>

        <div class="container-fluid py-4">

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">{{ __('Contact Information') }}</h6>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                @if($contact)
                                    <table class="table align-items-center mb-0">
                                        <tbody>
                                            <tr>
                                                <th class="ps-4">{{ __('Email') }}</th>
                                                <td>{{ $contact->email }}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-4">{{ __('Address (AR)') }}</th>
                                                <td>{{ $contact->address_ar }}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-4">{{ __('Address (EN)') }}</th>
                                                <td>{{ $contact->address_en }}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-4">{{ __('Phone') }}</th>
                                                <td>{{ $contact->phone }}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-4">{{ __('WhatsApp') }}</th>
                                                <td>{{ $contact->whatsapp }}</td>
                                            </tr>
                                            <tr>
                                                <th class="ps-4">{{ __('Facebook') }}</th>
                                                <td>{{ $contact->facebook }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="px-4 py-3">
                                        <a href="{{ route('contacts.edit', $contact->id) }}" class="btn btn-primary">
                                            {{ __('Edit') }}
                                        </a>
                                    </div>
                                @else
                                    <div class="p-4 text-center">
                                        <p>{{ __('No contact information found.') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
</x-layout>
