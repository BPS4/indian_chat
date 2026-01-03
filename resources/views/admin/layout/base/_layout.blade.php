@if(config('layout.self.layout') == 'blank')
<div class="d-flex flex-column flex-root">
    @yield('content')
</div>
@else

@include('admin.layout.base._header-mobile')

<div class="d-flex flex-column flex-root">
    <div class="d-flex flex-row flex-column-fluid page">

        @if(config('layout.aside.self.display'))
        @include('admin.layout.base._aside')
        @endif

        <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">

            @include('admin.layout.base._header')

            <div class="content {{ Metronic::printClasses('content', false) }} d-flex flex-column flex-column-fluid" id="kt_content">
 

                @include('admin.layout.base._content')
            </div>

            @include('admin.layout.base._footer')
        </div>
    </div>
</div>

@endif
 