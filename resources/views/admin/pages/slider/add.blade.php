@extends('admin.layout.default')
@section('Slider', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <form method="POST" action="{{ route('slider.store') }}" class="w-100" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Add New Slider</h3>
                            <p>Enter the details to create new Slider</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="title" value="{{ old('title') }}"
                                            isrequired="required" class="form-control" placeholder="Enter Title">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">

                                    <div><input type="text" name="subtitle" value="{{ old('subtitle') }}"
                                            isrequired="required" class="form-control" placeholder="Enter Subtitle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="file" name="image" value="{{ old('image') }}"
                                            isrequired="required" class="form-control" placeholder="Upload Image"></div>
                                </div>
                                {{-- <div class="form-group col-md-6">

                                    <div><input type="text" name="link" value="{{ old('link') }}"
                                            isrequired="required" class="form-control" placeholder="Enter url">
                                    </div>
                                </div> --}}
                                {{-- <div class="form-group col-md-6">

                                    <div><input type="text" name="button_text" value="{{ old('button_text') }}"
                                            isrequired="required" class="form-control" placeholder="Enter Button Text">
                                    </div>
                                </div> --}}

                                <div class="form-group  col-md-6">
                                    <select name="is_active" required class="form-control">
                                        <option value="" disabled selected>Select Status</option>
                                        <option value=1>Active</option>
                                        <option value=0>Inactive</option>
                                    </select>
                                </div>

                                <div
                                    class="d-flex  gap-3 mt-4">
                                    <button type="submit" class="btn  bg-brown add">Save</button>
                                    <button type="reset" class="btn bg-gray px-5">Cancel</button>
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
