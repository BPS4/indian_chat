{{-- Content --}}
@if (config('layout.content.extended'))
@yield('content')
@else
<div class="breadcrumbs-div ">
    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">

        <li class="breadcrumb-item text-muted">
            <a href="{{url('admin')}}" class="text-muted">Dashboard</a>
        </li>
        @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
        @foreach($breadcrumbs as $key => $list)
        <li class="breadcrumb-item text-muted">
            <a @if($list['url']) href="{{$list['url']}}" @else onclick="javascript:void(0)" @endif class="text-muted">{{$list['title']}}</a>
        </li>
        @endforeach
        @endif

    </ul>
</div>
<div class="d-flex flex-column-fluid">
    <div class="{{ Metronic::printClasses('content-container', false) }}">

        @include('admin.layout.partials.errors.error_messages')
        @yield('content')
    </div>
</div>
@endif
@stack('scripts')