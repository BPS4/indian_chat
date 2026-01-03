@extends('admin.layout.default')
@section('Addons', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <form method="POST" action="{{ route('addons.store') }}" class="w-100" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Add New Addons</h3>
                            <p>Enter the details to create new Addons</p>
                            <div class="row align-items-center">

                                <div class="form-group col-md-6">

                                    <div><input type="text" name="name" value="{{ old('name') }}"
                                            isrequired="required" class="form-control" placeholder="name">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="description">Description</label>
                                    <textarea  rows="4" name="description" id="description" class="form-control" placeholder="Description">{{ old('description') }}</textarea>
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
