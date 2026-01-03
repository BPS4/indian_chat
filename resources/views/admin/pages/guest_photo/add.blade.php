@extends('admin.layout.default')
@section('GuestPhoto', 'active menu-item-open')
@section('content')

    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="{{ route('guest-photo.store') }}" class="w-100" enctype="multipart/form-data">
                        @csrf

                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Add Guest Photos</h3>
                            <p>Upload photos for selected hotel</p>

                            <div class="row align-items-center">

                                {{-- Select Hotel --}}
                                <div class="form-group col-md-6">
                                    <label for="hotel_id" class="form-label">Select Hotel</label>
                                    <select name="hotel_id" id="hotel_id" required class="form-control">
                                        <option value="" disabled selected>Select Hotel</option>
                                        @foreach ($hotels as $hotel)
                                            <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Upload Photos --}}
                                <div class="form-group col-md-6">
                                    <label for="photo_url" class="form-label">Upload Photos(Multiple) </label>
                                    <input type="file" id="photo_url" name="photo_url[]" class="form-control" multiple
                                        required>
                                </div>


                                {{-- Submit --}}
                                <div class="d-flex  gap-3 mt-4">
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
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

    <script>
        CKEDITOR.replace('content-editor');
    </script>
@endsection
