@extends('admin.layout.default')
@section('gift-card', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <form method="POST" action="{{ route('gift-card.store') }}" class="w-100">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Add New Gift Card</h3>
                            <p>Enter the details to create new Gift Card</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="code" value="{{ old('code') }}"
                                            isrequired="required" class="form-control" placeholder="Enter code">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">

                                    <div><input type="text" name="balance_amount" value="{{ old('balance_amount') }}"
                                            isrequired="required" class="form-control" placeholder="Balance Amount">
                                    </div>
                                </div>

                               <div class="form-group col-md-6">
    <label for="is_active">Status</label>
    <select name="is_active" id="is_active" required class="form-control">
        <option value="" disabled selected>Select Status</option>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
    </select>
</div>

<div class="form-group col-md-6">
    <label for="expiry_date">Expiry Date</label>
    <input type="date" id="expiry_date" name="expiry_date"
           value="{{ old('expiry_date') }}" required class="form-control">
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
