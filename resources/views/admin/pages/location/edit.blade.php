@extends('admin.layout.default')

@section('location', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="{{ route('location.update', $location->id) }}" class="w-100"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Update Location</h3>
                            <p>Enter the details to update Location</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="city" value="{{ $location->city }}"
                                            isrequired="required" class="form-control" placeholder="Enter city">
                                    </div>
                                </div>


                                <div class="form-group col-md-6">

                                    <div><input type="text" name="state" value="{{ $location->state }}"
                                            isrequired="required" class="form-control" placeholder="state">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="country" value="{{ $location->country }}"
                                            isrequired="required" class="form-control" placeholder="country">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="zipcode" value="{{ $location->zipcode }}"
                                            isrequired="required" class="form-control" placeholder="zipcode"></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="image">Image</label>
                                    <div>
                                        <!-- If there's an image, show it. Otherwise, show a placeholder -->
                                        @if ($location->image)
                                            <img src="{{ asset($location->image) }}" alt="Location Image"
                                                class="img-fluid mb-3" style="max-height: 150px;">
                                        @else
                                            <img src="https://via.placeholder.com/150" alt="No Image" class="img-fluid mb-3"
                                                style="max-height: 150px;">
                                        @endif
                                    </div>

                                    <!-- File Input -->
                                    <input type="file" name="image" class="form-control" placeholder="Upload Image">

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
