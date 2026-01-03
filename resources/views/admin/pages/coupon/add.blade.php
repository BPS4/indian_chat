@extends('admin.layout.default')
@section('coupons', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <form method="POST" action="{{ route('coupons.store') }}" class="w-100">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Add New Coupon</h3>
                            <p>Enter the details to create new Coupon</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="code" value="{{ old('code') }}"
                                            isrequired="required" class="form-control" placeholder="Enter code">
                                    </div>
                                </div>


                                <div class="form-group col-md-6">
                                    <select name="discount_type" required class="form-control">
                                        <option value="" disabled selected>Discound Type</option>
                                        <option value="flat">Flat</option>
                                        <option value="percent">Percentage</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="discount_value" value="{{ old('discount_value') }}"
                                            isrequired="required" class="form-control" placeholder="Discount Value">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="min_booking_amount"
                                            value="{{ old('min_booking_amount') }}" isrequired="required"
                                            class="form-control" placeholder="Minimum Booking Amount"></div>
                                </div>

                                <div class="form-group col-md-6">

                                    <div><input type="text" name="max_discount" value="{{ old('max_discount') }}"
                                            isrequired="required" class="form-control" placeholder="Maximum Discount"></div>
                                </div>

                                <div class="form-group  col-md-6">
                                    <select name="is_active" required class="form-control">
                                        <option value="" disabled selected> Status</option>
                                        <option value=1>Active</option>
                                        <option value=0>Inactive</option>
                                    </select>
                                </div>


                                <div class="form-group col-md-6">
                                    <label for="valid_from">Valid From</label>
                                    <input type="date" id="valid_from" name="valid_from" value="{{ old('valid_from') }}"
                                        required class="form-control" min="{{ date('Y-m-d') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="valid_to">Valid To</label>
                                    <input type="date" id="valid_to" name="valid_to" value="{{ old('valid_to') }}"
                                        required class="form-control" min="{{ date('Y-m-d') }}">
                                </div>





                                <div class="d-flex  gap-3 mt-4">
                                    <button type="submit" class="btn  bg-brown add">Save</button>
                                    <button type="reset" class="btn bg-gray px-5">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <script>
                        const discountType = document.querySelector("[name='discount_type']");
                        const discountValue = document.querySelector("[name='discount_value']");

                        discountValue.addEventListener("input", function() {
                            const type = discountType.value;

                            if (type === "percent") {
                                if (this.value > 100) {
                                    this.value = this.value.slice(0, -1); // remove last digit typed
                                }
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
