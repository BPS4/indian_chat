@extends('admin.layout.default')
@section('locality', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <form method="POST" action="{{ route('locality.store') }}" class="w-100" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Add New Locality</h3>
                            <p>Enter the details to create new Locality</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">
                                    <div>
                                        <select name="location_id" required class="form-control">
                                            <option value="" disabled selected>Select a city</option>

                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->city }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>



                                <div class="form-group col-md-6">

                                    <div><input type="text" name="name" value="{{ old('name') }}"
                                            isrequired="required" class="form-control" placeholder="Locality name">
                                    </div>
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
