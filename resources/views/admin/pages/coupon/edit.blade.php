@extends('admin.layout.default')

@section('coupons', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <form method="POST" action="{{ route('coupons.update', ['coupon' => $coupon->coupon_id]) }}"
                        class="w-100" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Update Coupons</h3>
                            <p>Enter the details to update Coupons</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="code" value="{{ $coupon->code }}"
                                            isrequired="required" class="form-control" placeholder="Enter code">
                                    </div>
                                </div>


                                <div class="form-group col-md-6">
                                    <select name="discount_type" required class="form-control">
                                        <option value="" disabled selected>Select For</option>
                                        <option value="flat" {{ $coupon->discount_type == 'flat' ? 'selected' : '' }}>
                                            Flat</option>
                                        <option value="percent" {{ $coupon->discount_type == 'percent' ? 'selected' : '' }}>
                                            Percentage</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="discount_value" value="{{ $coupon->discount_value }}"
                                            isrequired="required" class="form-control" placeholder="Discount Value">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">

                                    <div><input type="text" name="min_booking_amount"
                                            value="{{ $coupon->min_booking_amount }}" isrequired="required"
                                            class="form-control" placeholder="Minimum Booking Amount"></div>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="max_discount" value="{{ $coupon->max_discount }}"
                                            isrequired="required" class="form-control" placeholder="Maximum Discount"></div>
                                </div>

                                <div class="form-group col-md-6">
                                    <select name="is_active" required class="form-control">
                                        <option value="" disabled selected>Select Status</option>
                                        <option value=1 {{ $coupon->is_active == 1 ? 'selected' : '' }}>Active</option>
                                        <option value=0 {{ $coupon->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                   <div class="form-group col-md-6">

                                    <div><input type="date" name="valid_from" value="{{ $coupon->valid_from }}"
                                            isrequired="required" class="form-control" placeholder="Valid From"></div>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="date" name="valid_to" value="{{ $coupon->valid_to }}"
                                            isrequired="required" class="form-control" placeholder="Valid To"></div>
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
