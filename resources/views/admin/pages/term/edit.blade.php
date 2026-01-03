@extends('admin.layout.default')

@section('term', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="{{ route('term.update', $term->id) }}" class="w-100"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Update Term</h3>
                            <p>Enter the details to update Term</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="title" value="{{ $term->title }}"
                                            isrequired="required" class="form-control" placeholder="Enter Title">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <select name="is_active" required class="form-control">
                                        <option value="" disabled selected>Select Status</option>
                                        <option value=1 {{ $term->is_active == 1 ? 'selected' : '' }}>Active</option>
                                        <option value=0 {{ $term->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <div class="form-group col-md-12">
                                        <textarea name="content" id="content-editor" class="form-control">{{ old('content', $term->content) }}</textarea>
                                    </div>

                                </div>
                                <div
                                    class="d-flex  gap-3 mt-4">
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
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>

    <script>
        CKEDITOR.replace('content-editor');
    </script>
@endsection
