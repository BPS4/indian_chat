{{-- Topbar --}}
<div class="topbar">




    {{-- User --}}
    @if (config('layout.extras.user.display'))
        @if (config('layout.extras.user.layout') == 'offcanvas')
            <div class="dropdown">
                {{-- Toggle --}}
                <div class="topbar-item">
                       <div class="btn btn-icon bell w-auto btn-clean d-flex align-items-center btn-lg px-2">
                        <!-- <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                        <span
                            class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">{{ request()->session()->get('name') }}</span> -->
                        {{-- <span class="symbol symbol-35 symbol-light-success">
                            <a href="#">

                                <img src="{{ asset('media/icons/bell.png') }}" alt="">
                            </a>
                        </span> --}}
                    </div>

                    <div style="display:flex;align-items:center;gap:8px;">
                        <!-- Mobile aside toggle placed next to profile -->
                        <button id="kt_aside_mobile_toggle" class="btn btn-icon btn-sm d-lg-none" data-aside-toggle aria-label="Toggle sidebar">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect y="4" width="24" height="2" rx="1" fill="currentColor"/>
                                <rect y="11" width="24" height="2" rx="1" fill="currentColor"/>
                                <rect y="18" width="24" height="2" rx="1" fill="currentColor"/>
                            </svg>
                        </button>

                        {{-- profile icon --}}
                        <div class="btn btn-icon btn-clean btn-lg mr-1 profile" id="kt_quick_user_toggle">
                            <span class="svg-icon svg-icon-xl svg-icon-primary">

                                <a href="{{ route('profile') }}">
                            <img src="{{ asset('media/icons/profile.png') }}" alt="">
                                </a>
                            </span>
                        </div>
                    </div>

                      <span class="symbol symbol-35 symbol-light-success">
                            <a href="{{ url('logout') }}">
                                <span class="svg-icon svg-icon-primary svg-icon-2x">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path
                                                d="M14.0069431,7.00607258 C13.4546584,7.00607258 13.0069431,6.55855153 13.0069431,6.00650634 C13.0069431,5.45446114 13.4546584,5.00694009 14.0069431,5.00694009 L15.0069431,5.00694009 C17.2160821,5.00694009 19.0069431,6.7970243 19.0069431,9.00520507 L19.0069431,15.001735 C19.0069431,17.2099158 17.2160821,19 15.0069431,19 L3.00694311,19 C0.797804106,19 -0.993056895,17.2099158 -0.993056895,15.001735 L-0.993056895,8.99826498 C-0.993056895,6.7900842 0.797804106,5 3.00694311,5 L4.00694793,5 C4.55923268,5 5.00694793,5.44752105 5.00694793,5.99956624 C5.00694793,6.55161144 4.55923268,6.99913249 4.00694793,6.99913249 L3.00694311,6.99913249 C1.90237361,6.99913249 1.00694311,7.89417459 1.00694311,8.99826498 L1.00694311,15.001735 C1.00694311,16.1058254 1.90237361,17.0008675 3.00694311,17.0008675 L15.0069431,17.0008675 C16.1115126,17.0008675 17.0069431,16.1058254 17.0069431,15.001735 L17.0069431,9.00520507 C17.0069431,7.90111468 16.1115126,7.00607258 15.0069431,7.00607258 L14.0069431,7.00607258 Z"
                                                fill="#000000" fill-rule="nonzero" opacity="0.3"
                                                transform="translate(9.006943, 12.000000) scale(-1, 1) rotate(-90.000000) translate(-9.006943, -12.000000) " />
                                            <rect fill="#000000" opacity="0.3"
                                                transform="translate(14.000000, 12.000000) rotate(-270.000000) translate(-14.000000, -12.000000) "
                                                x="13" y="6" width="2" height="12" rx="1" />
                                            <path
                                                d="M21.7928932,9.79289322 C22.1834175,9.40236893 22.8165825,9.40236893 23.2071068,9.79289322 C23.5976311,10.1834175 23.5976311,10.8165825 23.2071068,11.2071068 L20.2071068,14.2071068 C19.8165825,14.5976311 19.1834175,14.5976311 18.7928932,14.2071068 L15.7928932,11.2071068 C15.4023689,10.8165825 15.4023689,10.1834175 15.7928932,9.79289322 C16.1834175,9.40236893 16.8165825,9.40236893 17.2071068,9.79289322 L19.5,12.0857864 L21.7928932,9.79289322 Z"
                                                fill="#000000" fill-rule="nonzero"
                                                transform="translate(19.500000, 12.000000) rotate(-90.000000) translate(-19.500000, -12.000000) " />
                                        </g>
                                    </svg>
                                </span>

                            </a>
                        </span>

                </div>

                {{-- Dropdown --}}
                <!-- <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg p-0">
            @include('admin.layout.partials.extras.dropdown._user')
        </div> -->
            </div>
        @else
            <div class="dropdown">
                {{-- Toggle --}}
                <!-- <div class="topbar-item" data-toggle="dropdown" data-offset="0px,0px">
            <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2">
                <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">Admin</span>
                <span class="symbol symbol-35 symbol-light-success">
                    <span class="symbol-label font-size-h5 font-weight-bold">A</span>
                </span>
            </div>
        </div> -->

                {{-- Dropdown --}}
                <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg p-0">
                    @include('admin.layout.partials.extras.dropdown._user')
                </div>
            </div>
        @endif
    @endif
</div>
