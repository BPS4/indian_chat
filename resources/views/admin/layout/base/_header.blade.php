{{-- Header --}}
<div id="kt_header" class="header {{ Metronic::printClasses('header', false) }}" {{ Metronic::printAttrs('header') }}>

    {{-- Container --}}
    <div class="container-fluid d-flex align-items-center justify-content-between w-100">
        @if (config('layout.header.self.display'))

            @php
                $kt_logo_image = 'logo-light.png';
            @endphp

            @if (config('layout.header.self.theme') === 'light')
                @php $kt_logo_image = 'logo-dark.png' @endphp
            @elseif (config('layout.header.self.theme') === 'dark')
                @php $kt_logo_image = 'logo-light.png' @endphp
            @endif

            {{-- Header Menu --}}
            <div class="header-mesnu-wrapper header-menu-wrapper-left w-100" id="kt_header_menu_wrapper">
                <!-- Header sidebar toggles: mobile-only and desktop-only versions -->
                <button class="btn btn-icon btn-sm me-3 align-items-center d-lg-none" data-aside-toggle aria-label="Toggle sidebar (mobile)">
                    {{-- <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect y="4" width="24" height="2" rx="1" fill="currentColor"/>
                        <rect y="11" width="24" height="2" rx="1" fill="currentColor"/>
                        <rect y="18" width="24" height="2" rx="1" fill="currentColor"/>
                    </svg> --}}
                </button>

                <button class="btn btn-icon btn-sm me-3 align-items-center d-none d-lg-inline-flex" data-aside-toggle aria-label="Toggle sidebar (desktop)">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect y="4" width="24" height="2" rx="1" fill="currentColor"/>
                        <rect y="11" width="24" height="2" rx="1" fill="currentColor"/>
                        <rect y="18" width="24" height="2" rx="1" fill="currentColor"/>
                    </svg>
                </button>
                <form action="{{ url('/admin/search/common-search') }}" method="GET">
                    <div class="w-75 position-relative">
                        <i
                            class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500"></i>

                        <input name="q" class="form-control ps-5" type="search"
                            placeholder="Search hotels, customers, bookings..." aria-label="Search">
                    </div>
                </form>

                @if (config('layout.aside.self.display') == false)
                    <div class="header-logo">
                        <a href="{{ url('/') }}">
                            <img alt="Logo" src="{{ asset('media/logos/' . $kt_logo_image) }}" />
                            <!-- <img alt="Logo" src="{{ asset('media/logos/hotel-logo.png') }}" /> -->
                        </a>
                    </div>

                    <!-- <div>
                     <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                </div>   -->
                @endif

              


            </div>
        @else
            <div></div>
        @endif

        @include('admin.layout.partials.extras._topbar')
    </div>
</div>
