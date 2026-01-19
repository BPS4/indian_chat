{{-- Aside --}}

@php
    $kt_logo_image = 'logo-light.png';
@endphp

@if (config('layout.brand.self.theme') === 'light')
    @php $kt_logo_image = 'logo-dark.png' @endphp
@elseif (config('layout.brand.self.theme') === 'dark')
    @php $kt_logo_image = 'hotel-logo.png' @endphp
@endif

<div class="aside aside-left {{ Metronic::printClasses('aside', false) }} d-flex flex-column flex-row-auto"
    id="kt_aside">

    {{-- Brand --}}
    <div class="brand flex-column-auto {{ Metronic::printClasses('brand', false) }}" id="kt_brand">
        <div class="brand-logo flex gap-3 align-items-center">
            <a href="{{ url('/admin/dashboard') }}">

                <img class="pt-10  w-70" alt="{{ config('app.name') }}"
                    src="{{ asset('media/logos/' . $kt_logo_image) }}" />
            </a>
            <p class="mb-0 text-bold">My Indian Bussiness</p>
        </div>

        @if (config('layout.aside.self.minimize.toggle'))
            <button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
                {{ Metronic::getSVG('media/svg/icons/Navigation/Angle-double-left.svg', 'svg-icon-xl') }}
            </button>
        @endif

    </div>

    {{-- Aside menu --}}
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">

        @if (config('layout.aside.self.display') === false)
            <div class="header-logo">
                <a href="{{ url('/') }}">


                    <img alt="{{ config('app.name') }}" src="{{ asset('media/logos/' . $kt_logo_image) }}" />
                </a>
            </div>
        @endif

        @php
            if (auth()->user()->role_id) {
                $role = getRoleById(auth()->user()->role_id);
                $existingPermissions = $role->permission ? json_decode($role->permission, true) : [];
            }
        @endphp

        <div id="kt_aside_menu" class="aside-menu {{ Metronic::printClasses('aside_menu', false) }}"
            data-menu-vertical="1" {{ Metronic::printAttrs('aside_menu') }}>
            <ul class="menu-nav {{ Metronic::printClasses('aside_menu_nav', false) }}">

                <li class="menu-item menu-item-submenu @yield('dashboard')" aria-haspopup="true" data-menu-toggle="hover">
                    <a href="{{ url('/admin/dashboard') }}" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">

                            <span><img src="{{ asset('media/icons/dashboard-ico.png') }}" class="w-75"
                                    alt=""> </span>
                        </span>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>

                {{-- <li class="menu-item menu-item-submenu @yield('master') @yield('master')" aria-haspopup="true"
                    data-menu-toggle="hover">
                    <a href="#" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" height="16" width="20" viewBox="0 0 640 512">
                                <path
                                    d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z" />
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-text">Masters</span><i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu " kt-hidden-height="320" style=""><span class="menu-arrow"></span>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true"><span class="menu-link"><span
                                        class="menu-text">Admin</span></span></li>
                            @if (isset($existingPermissions['addons']) && $existingPermissions['addons'] != 0)
                                <li class="menu-item  @yield('addons')" aria-haspopup="true" data-menu-toggle="hover">
                                    <a href="{{ route('addons.index') }}" class="menu-link menu-toggle">
                                        <i class="menu-bullet menu-bullet-line"><span></span></i>
                                        <span class="menu-text">Add Ons</span>
                                    </a>
                                </li>
                            @endif
                         
                            <li class="menu-item  @yield('facility')" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="{{ route('facility.index') }}" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Facility </span>
                                </a>
                            </li>
                            <li class="menu-item  @yield('location')" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="{{ route('location.list') }}" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Location </span>
                                </a>
                            </li>
                            <li class="menu-item  @yield('locality')" aria-haspopup="true" data-menu-toggle="hover">
                                <a href="{{ route('locality.list') }}" class="menu-link menu-toggle">
                                    <i class="menu-bullet menu-bullet-line"><span></span></i>
                                    <span class="menu-text">Locality </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>



                <li class="menu-item menu-item-submenu @yield('Offers') @yield('Offers')" aria-haspopup="true"
                    data-menu-toggle="hover">
                    <a href="#" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" height="16" width="20"
                                viewBox="0 0 640 512">
                                <path
                                    d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z" />
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="menu-text">Offers</span><i class="menu-arrow"></i>
                    </a>
                    <div class="menu-submenu " kt-hidden-height="320" style=""><span
                            class="menu-arrow"></span>
                        <ul class="menu-subnav">
                            <li class="menu-item  menu-item-parent" aria-haspopup="true"><span
                                    class="menu-link"><span class="menu-text">Admin</span></span></li>
                            @if (isset($existingPermissions['coupons']) && $existingPermissions['coupons'] != 0)
                                <li class="menu-item  @yield('coupons')" aria-haspopup="true"
                                    data-menu-toggle="hover">
                                    <a href="{{ route('coupons.index') }}" class="menu-link menu-toggle">
                                        <i class="menu-bullet menu-bullet-line"><span></span></i>
                                        <span class="menu-text">Coupons</span>
                                    </a>
                                </li>
                            @endif
                            @if (isset($existingPermissions['gift-card']) && $existingPermissions['gift-card'] != 0)
                                <li class="menu-item  @yield('gift-card')" aria-haspopup="true"
                                    data-menu-toggle="hover">
                                    <a href="{{ route('gift-card.index') }}" class="menu-link menu-toggle">
                                        <i class="menu-bullet menu-bullet-line"><span></span></i>
                                        <span class="menu-text">Gift Card</span>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li> --}}


                  @if (isset($existingPermissions['message']) && $existingPermissions['message'] != 0)
                    <li class="menu-item menu-item-submenu @yield('message')" aria-haspopup="true"
                        data-menu-toggle="hover">
                        <a href="{{ url('/admin/message/list') }}" class="menu-link menu-toggle">
                            <span class="svg-icon menu-icon">
                                <!-- <span class="flaticon2-analytics-1"></span> -->
                                <span><img src="{{ asset('media/icons/hotel-ico.png') }}" class="w-75"
                                        alt=""> </span>
                            </span>
                            <span class="menu-text">Message</span>
                        </a>
                    </li>
                @endif


                @if (isset($existingPermissions['customers']) && $existingPermissions['customers'] != 0)
                    <li class="menu-item menu-item-submenu @yield('customers')" aria-haspopup="true"
                        data-menu-toggle="hover">
                        <a href="{{ url('/admin/customers/list') }}" class="menu-link menu-toggle">
                            <span class="svg-icon menu-icon">
                                <!-- <span class="flaticon2-percentage"></span> -->
                                <span><img src="{{ asset('media/icons/customer-ico.png') }}" class="w-75"
                                        alt=""> </span>
                            </span>
                            <span class="menu-text">Users</span>
                        </a>
                    </li>
                @endif

                @if (isset($existingPermissions['withdrawal']) && $existingPermissions['withdrawal'] != 0)
                    <li class="menu-item menu-item-submenu @yield('withdrawal') " aria-haspopup="true"
                        data-menu-toggle="hover">
                        <a href="{{ url('/admin/withdrawal/list') }}" class="menu-link menu-toggle">
                            <span class="svg-icon menu-icon">
                                <!-- <span class="flaticon2-delivery-package"></span> -->
                                <span><img src="{{ asset('media/icons/booking-ico.png') }}" class="w-75"
                                        alt=""> </span>
                            </span>
                            <span class="menu-text">Withdrawal</span>
                        </a>
                    </li>
                @endif



                @if (isset($existingPermissions['Offers']) && $existingPermissions['Offers'] != 0)
                    <li class="menu-item menu-item-submenu @yield('Offers')" aria-haspopup="true"
                        data-menu-toggle="hover">
                        <a href="{{ route('review.list') }}" class="menu-link menu-toggle">
                            <span class="svg-icon menu-icon">
                                <!-- <span class="flaticon2-delivery-package"></span> -->
                                <span><img src="{{ asset('media/icons/offers-ico.png') }}" class="w-75"
                                        alt=""> </span>
                            </span>
                            <span class="menu-text">Refral</span>
                        </a>
                    </li>
                @endif
                    
                {{-- @if (isset($existingPermissions['Payments']) && $existingPermissions['Payments'] != 0)
                    <li class="menu-item menu-item-submenu @yield('Payments')" aria-haspopup="true"
                        data-menu-toggle="hover">
                        <a href="{{ url('/admin/Payments/list') }}" class="menu-link menu-toggle">
                            <span class="svg-icon menu-icon">
                                <!-- <span class="flaticon2-open-box"></span> -->
                                <span><img src="{{ asset('media/icons/payment-ico.png') }}" class="w-75"
                                        alt=""> </span>
                            </span>
                            <span class="menu-text">Payment</span>
                        </a>
                    </li>
                @endif --}}


                {{-- <li class="menu-item menu-item-submenu  @yield('settings')" aria-haspopup="true"
                    data-menu-toggle="hover">
                    <a href="{{ url('/admin/settings') }}" class="menu-link menu-toggle">
                        <span class="svg-icon menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path
                                        d="M5,8.6862915 L5,5 L8.6862915,5 L11.5857864,2.10050506 L14.4852814,5 L19,5 L19,9.51471863 L21.4852814,12 L19,14.4852814 L19,19 L14.4852814,19 L11.5857864,21.8994949 L8.6862915,19 L5,19 L5,15.3137085 L1.6862915,12 L5,8.6862915 Z M12,15 C13.6568542,15 15,13.6568542 15,12 C15,10.3431458 13.6568542,9 12,9 C10.3431458,9 9,10.3431458 9,12 C9,13.6568542 10.3431458,15 12,15 Z"
                                        fill="#000000" />
                                </g>
                            </svg>
                        </span>
                        <span class="menu-text">Settings</span>
                    </a>
                </li> --}}
                @if (isset($existingPermissions['settings']) && $existingPermissions['settings'] != 0)
                    <li class="menu-item menu-item-submenu @yield('settings') @yield('settings')" aria-haspopup="true"
                        data-menu-toggle="hover">
                        <a href="#" class="menu-link menu-toggle">
                            <span class="svg-icon menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M5,8.6862915 L5,5 L8.6862915,5 L11.5857864,2.10050506 L14.4852814,5 L19,5 L19,9.51471863 L21.4852814,12 L19,14.4852814 L19,19 L14.4852814,19 L11.5857864,21.8994949 L8.6862915,19 L5,19 L5,15.3137085 L1.6862915,12 L5,8.6862915 Z M12,15 C13.6568542,15 15,13.6568542 15,12 C15,10.3431458 13.6568542,9 12,9 C10.3431458,9 9,10.3431458 9,12 C9,13.6568542 10.3431458,15 12,15 Z"
                                            fill="#000000" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            <span class="menu-text">Settings</span><i class="menu-arrow"></i>
                        </a>
                        <div class="menu-submenu " kt-hidden-height="320" style=""><span
                                class="menu-arrow"></span>
                            <ul class="menu-subnav">
                                <li class="menu-item  menu-item-parent" aria-haspopup="true"><span
                                        class="menu-link"><span class="menu-text">Admin</span></span></li>
                                @if (isset($existingPermissions['refral_commision']) && $existingPermissions['refral_commision'] != 0)
                                    <li class="menu-item  @yield('refral_commision')" aria-haspopup="true"
                                        data-menu-toggle="hover">
                                        <a href="{{ url('/admin/refral_commision/list') }}" class="menu-link menu-toggle">
                                            <i class="menu-bullet menu-bullet-line"><span></span></i>
                                            <span class="menu-text">Refral Commision</span>
                                        </a>
                                    </li>
                                @endif

                                   @if (isset($existingPermissions['contact_details']) && $existingPermissions['contact_details'] != 0)
                                    <li class="menu-item  @yield('contact_details')" aria-haspopup="true"
                                        data-menu-toggle="hover">
                                        <a href="{{ url('/admin/contact_details/list') }}" class="menu-link menu-toggle">
                                            <i class="menu-bullet menu-bullet-line"><span></span></i>
                                            <span class="menu-text">Contact Details</span>
                                        </a>
                                    </li>
                                @endif

                                {{-- @if (isset($existingPermissions['app_logo']) && $existingPermissions['app_logo'] != 0)
                                    <li class="menu-item  @yield('app_logo')" aria-haspopup="true"
                                        data-menu-toggle="hover">
                                        <a href="{{ route('app_logo.list') }}" class="menu-link menu-toggle">
                                            <i class="menu-bullet menu-bullet-line"><span></span></i>
                                            <span class="menu-text">App Logo</span>
                                        </a>
                                    </li>
                                @endif --}}
                                {{-- @if (isset($existingPermissions['user']) && $existingPermissions['user'] != 0)
                                    <li class="menu-item  @yield('user')" aria-haspopup="true"
                                        data-menu-toggle="hover">
                                        <a href="{{ route('guest-photo.index') }}" class="menu-link menu-toggle">
                                            <i class="menu-bullet menu-bullet-line"><span></span></i>
                                            <span class="menu-text">Guest Photo</span>
                                        </a>
                                    </li>
                                @endif --}}
                            </ul>
                        </div>
                    </li>
                @endif

                @if (isset($existingPermissions['admin-users']) && $existingPermissions['admin-users'] != 0)
                    <li class="menu-item menu-item-submenu @yield('user') @yield('role')" aria-haspopup="true"
                        data-menu-toggle="hover">
                        <a href="#" class="menu-link menu-toggle">
                            <span class="svg-icon menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" height="16" width="20"
                                    viewBox="0 0 640 512">
                                    <path
                                        d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z" />
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                            <span class="menu-text">Admin Users</span><i class="menu-arrow"></i>
                        </a>
                        <div class="menu-submenu " kt-hidden-height="320" style=""><span
                                class="menu-arrow"></span>
                            <ul class="menu-subnav">
                                <li class="menu-item  menu-item-parent" aria-haspopup="true"><span
                                        class="menu-link"><span class="menu-text">Admin</span></span></li>
                                @if (isset($existingPermissions['role']) && $existingPermissions['role'] != 0)
                                    <li class="menu-item  @yield('role')" aria-haspopup="true"
                                        data-menu-toggle="hover">
                                        <a href="{{ url('/admin/role/list') }}" class="menu-link menu-toggle">
                                            <i class="menu-bullet menu-bullet-line"><span></span></i>
                                            <span class="menu-text">Roles</span>
                                        </a>
                                    </li>
                                @endif
                                @if (isset($existingPermissions['user']) && $existingPermissions['user'] != 0)
                                    <li class="menu-item  @yield('user')" aria-haspopup="true"
                                        data-menu-toggle="hover">
                                        <a href="{{ url('/admin/user/list') }}" class="menu-link menu-toggle">
                                            <i class="menu-bullet menu-bullet-line"><span></span></i>
                                            <span class="menu-text">Users</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

            </ul>
        </div>
    </div>

</div>
