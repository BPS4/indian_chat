@extends('admin.layout.default')
@section('Offers', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <form method="POST" action="" class="w-100">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Add New Offer</h3>
                            <p>Enter the details to create new offer</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">
                                    <!-- <label>Enter Hotel Name</label> -->
                                    <div><input type="text" name="article_no" value="{{ old('article_no') }}"
                                            isrequired="required" class="form-control" placeholder="Enter Offer Title">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <select class="form-control" name="item_category_id" isrequired="required">
                                        <option value="">Discount type</option>
                                        {{-- @php
                                        $divisions = ItemCategoryList();
                                        @endphp
                                        @foreach ($divisions as $division)
                                        <option value="{{$division->id}}" @if (old('item_category_id') == $division->id) selected @endif
                                    >{{$division->name}}</option>
                                    @endforeach --}}
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <!-- <label>price</label> -->
                                    <div><input type="text" name="price" value="{{ old('price') }}"
                                            pattern="^\d+(\.\d{1,2})?$" title="Enter a valid number (e.g., 10 or 10.50)"
                                            isrequired="required" class="form-control" placeholder="Discount Value">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <!-- <label>price</label> -->
                                    <div><input type="text" name="price" value="{{ old('price') }}"
                                            pattern="^\d+(\.\d{1,2})?$" title="Enter a valid number (e.g., 10 or 10.50)"
                                            isrequired="required" class="form-control" placeholder="Status">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <!-- <label>sku</label> -->
                                    <div><input type="text" name="sku" value="{{ old('sku') }}"
                                            isrequired="required" class="form-control" placeholder="Start Date"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <!-- <label>sku</label> -->
                                    <div><input type="text" name="sku" value="{{ old('sku') }}"
                                            isrequired="required" class="form-control" placeholder="End Date"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <!-- <label>sku</label> -->
                                    <div><input type="file" name="sku" value="{{ old('sku') }}"
                                            isrequired="required" class="form-control" placeholder="Upload Image"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <!-- <label>sku</label> -->
                                    <div><input type="text" name="sku" value="{{ old('sku') }}"
                                            isrequired="required" class="form-control" placeholder="Redirection Link"></div>
                                </div>


                                <div class="form-group col-md-12">
                                    <!-- <label>Description</label> -->
                                    <div>
                                        <textarea name="description" rows="4" required class="form-control" placeholder="Enter Offer Description">{{ old('description') }}</textarea>
                                    </div>
                                </div>


                                <div
                                    class="form-group col-md-12 submit-btn d-flex mt-3  justify-content-center align-items-center gap-4">
                                    <button class="btn bg-gray px-5">Cancel</button>
                                    <button class="btn  bg-brown add">Save</button>
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
