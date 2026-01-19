@extends('admin.layout.default')

@section('refral_commision', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="{{ route('commision.update', $commision->id) }}" class="w-100"
                        enctype="multipart/form-data">
                        @csrf
                            @method('PUT')
                            <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Update Slider</h3>
                            <p>Enter the details to update Slider</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="joining_bonus" value="{{ $commision->joining_bonus }}"
                                            required="required" class="form-control" placeholder="Enter Joining Bonus">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">

                                    <div><input type="text" name="referral_commision" value="{{ $commision->referral_commision }}"
                                            required="required" class="form-control" placeholder="Enter Referral Commission ">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                               
                                {{-- <div class="form-group col-md-6">

                                    <div><input type="text" name="link" value="{{ $slider->link }}"
                                            isrequired="required" class="form-control" placeholder="Enter url">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="button_text" value="{{ $slider->button_text }}"
                                            isrequired="required" class="form-control" placeholder="Enter Button Text">
                                    </div>
                                </div> --}}

                           
                                <div
                                    class="d-flex  gap-3 mt-4">
                                    {{-- <button class="btn bg-gray px-5">Cancel</button> --}}
                                    <button class="btn  bg-brown add" type="submit">Update</button>
                                    <a href="{{ route('commision.index') }}" class="btn bg-gray px-5">Cancel</a>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection

{{-- Scripts Section --}}
@section('scripts')
@endsection
