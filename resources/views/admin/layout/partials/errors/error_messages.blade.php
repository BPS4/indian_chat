<div class="">
    @if ($errors->any())
        {!! implode(
            '',
            $errors->all(
                '<div class="alert alert-danger"><i class="fas fa-exclamation-circle error-icons"></i> <strong>:message</strong> </div>',
            ),
        ) !!}
    @endif


    @error('error')
        <div class="alert alert-danger"><i
                class="fas fa-exclamation-circle error-icons"></i><strong>{{ $message }}</strong> </div>
    @enderror

    @error('success')
        <div class="alert alert-success"><i class="fad fa-check-double error-icons"></i><strong>{{ $message }}</strong>
        </div>
    @enderror

    {{-- @if (session()->has('success'))
        <div class="alert alert-success" style="background: #C39853;">
            <i class="fas fa-check-double error-icons"></i><strong>{{ session()->get('success') }}</strong>
        </div>
    @endif --}}

   @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #C39853; color: #fff;">
        <i class="fas fa-check-double error-icons"></i>
        <strong>{{ session('success') }}</strong>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif



    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: #C39853; color: #fff;">
        <i class="fas fa-exclamation-circle error-icons"></i>
        <strong>{{ session('error') }}</strong>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif



</div>
