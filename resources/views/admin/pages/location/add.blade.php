@extends('admin.layout.default')
@section('Location', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <form method="POST" action="{{ route('location.store') }}" class="w-100" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Add New Location</h3>
                            <p>Enter the details to create new Location</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="city" value="{{ old('city') }}"
                                            isrequired="required" class="form-control" placeholder="Enter city">
                                    </div>
                                </div>


                                <div class="form-group col-md-6">

                                    <div><input type="text" name="state" value="{{ old('state') }}"
                                            isrequired="required" class="form-control" placeholder="state">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="country" value="{{ old('country') }}"
                                            isrequired="required" class="form-control" placeholder="country">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="zipcode" value="{{ old('zipcode') }}"
                                            isrequired="required" class="form-control" placeholder="zipcode"></div>
                                </div>

                                <div class="form-group col-md-6">

                                    <div><input type="file" name="image" value="{{ old('image') }}"
                                            isrequired="required" class="form-control" placeholder="Upload Image"></div>
                                </div>

                                <div class="d-flex  gap-3 mt-4">
                                    <button type = "submit" class="btn  bg-brown add">Save</button>
                                    <button type="reset" class="btn bg-gray px-5">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <script>
                        // Allow only alphabets (City, State, Country)
                        document.querySelectorAll("input[name='city'], input[name='state'], input[name='country']")
                            .forEach(input => {
                                input.addEventListener("input", function() {
                                    this.value = this.value.replace(/[^A-Za-z\s]/g, ''); // remove non alphabets
                                });
                            });

                        // Allow only 6-digit numeric zipcode
                        document.querySelector("input[name='zipcode']").addEventListener("input", function() {
                            this.value = this.value.replace(/[^0-9]/g, ""); // only numbers
                            if (this.value.length > 6) {
                                this.value = this.value.slice(0, 6); // max 6 digits
                            }
                        });
                    </script>



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
