@extends('admin.layout.default')

@section('Slider', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="{{ route('slider.update', $slider->id) }}" class="w-100"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Update Slider</h3>
                            <p>Enter the details to update Slider</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="title" value="{{ $slider->title }}"
                                            isrequired="required" class="form-control" placeholder="Enter Title">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">

                                    <div><input type="text" name="subtitle" value="{{ $slider->subtitle }}"
                                            isrequired="required" class="form-control" placeholder="Enter Subtitle">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="image">Image</label>
                                    <div>
                                        <!-- If there's an image, show it. Otherwise, show a placeholder -->
                                        @if ($slider->image_path)
                                            <img src="{{ asset($slider->image_path) }}" alt="Slider Image"
                                                class="img-fluid mb-3" style="max-height: 150px;">
                                        @else
                                            <img src="https://via.placeholder.com/150" alt="No Image" class="img-fluid mb-3"
                                                style="max-height: 150px;">
                                        @endif
                                    </div>

                                    <!-- File Input -->
                                    <input type="file" name="image" class="form-control" placeholder="Upload Image">

                                </div>
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

                                <div class="form-group col-md-6">
                                    <select name="is_active" required class="form-control">
                                        <option value="" disabled selected>Select Status</option>
                                        <option value=1 {{ $slider->is_active == 1 ? 'selected' : '' }}>Active</option>
                                        <option value=0 {{ $slider->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>




                                <div
                                    class="d-flex  gap-3 mt-4">
                                    {{-- <button class="btn bg-gray px-5">Cancel</button> --}}
                                    <button class="btn  bg-brown add" type="submit">Update</button>
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
