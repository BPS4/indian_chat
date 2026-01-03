@extends('admin.layout.default')
@section('Term', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <form method="POST" action="{{ route('term.store') }}" class="w-100" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Add New Term</h3>
                            <p>Enter the details to create new Term</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="title" value="{{ old('title') }}"
                                            isrequired="required" class="form-control" placeholder="Enter Title">
                                    </div>
                                </div>
                                <div class="form-group  col-md-6">
                                    <select name="is_active" required class="form-control">
                                        <option value="" disabled selected>Select Status</option>
                                        <option value=1>Active</option>
                                        <option value=0>Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <textarea name="content" id="content-editor" class="form-control" placeholder="Enter Content">{{ old('content') }}</textarea>
                                </div>



                                <div
                                    class="d-flex  gap-3 mt-4">
                                    <button type="submit" class="btn  bg-brown add">Save</button>
                                    <button type= 'reset'class="btn bg-gray px-5">Cancel</button>
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
