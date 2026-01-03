@extends('admin.layout.default')

@section('locality', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="{{ route('locality.update', $localty->id) }}" class="w-100"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Update Locality</h3>
                            <p>Enter the details to update Locality</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">
                                    <div>
                                        <select name="location_id" required class="form-control">
                                            <option value="" disabled selected>Select a city</option>

                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}" {{ $location->id == $localty->location?->id ? 'selected' : '' }}>
                                                    {{ $location->city }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group col-md-6">

                                    <div><input type="text" name="name" value="{{ $localty->name }}"
                                            isrequired="required" class="form-control" placeholder="locality name">
                                    </div>
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
