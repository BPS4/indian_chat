<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{ Metronic::printAttrs('html') }}
    {{ Metronic::printClasses('html') }}>

<head>
    <meta charset="utf-8" />

    {{-- Title Section --}}
    <title>{{ config('app.name') }} | @yield('title', $page_title ?? '')</title>

    {{-- Meta Data --}}
    <meta name="description" content="@yield('page_description', $page_description ?? '')" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">


    {{-- Fonts --}}
    {{ Metronic::getGoogleFontsInclude() }}

    {{-- Global Theme Styles (used by all pages) --}}
    @foreach (config('layout.resources.css') as $style)
        <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($style)) : asset($style) }}"
            rel="stylesheet" type="text/css" />
    @endforeach

    {{-- Layout Themes (used by all pages) --}}
    @foreach (Metronic::initThemes() as $theme)
        <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($theme)) : asset($theme) }}"
            rel="stylesheet" type="text/css" />
    @endforeach

    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
    {{-- Includable CSS --}}
    @yield('styles')
</head>

<body id="kt_body"
    class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
    @yield('content')
    <script>
        var HOST_URL = "";
    </script>
    {{-- Global Config (global config for global JS scripts) --}}
    <script>
        var KTAppSettings = `{!! json_encode(config('layout.js'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}`;
    </script>

    {{-- Global Theme JS Bundle (used by all pages)  --}}
    @foreach (config('layout.resources.js') as $script)
        <script src="{{ asset($script) }}" type="text/javascript"></script>
    @endforeach


    @if (session('success'))
        <script>
            toastr.success("{{ session('success') }}");

            // Inline style override for success
            setTimeout(() => {
                $('.toast-success').css({
                    'background-color': '#CC0001', // custom green
                    'color': '#fff', // white text
                    'border': '1px solid #1e7e34' // optional
                });
            }, 100);
        </script>
    @endif

    @if (session('error'))
        <script>
            toastr.error("{{ session('error') }}");

            setTimeout(() => {
                $('.toast-error').css({
                    'background-color': '#CC0001', // custom red
                    'color': '#fff', // white text
                    'border': '1px solid #b21f2d' // optional
                });
            }, 100);
        </script>
    @endif

    {{-- Includable JS --}}
    @yield('scripts')

</body>

</html>
