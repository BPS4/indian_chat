@extends('admin.layout.default')

@section('gift-card', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">
                    <form method="POST" action="{{ route('gift-card.update', ['gift_card' => $giftCard->giftcard_id]) }}"
                        class="w-100" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Update Gift Card</h3>
                            <p>Enter the details to update Gift Card</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="code" value="{{ $giftCard->code }}"
                                            isrequired="required" class="form-control" placeholder="Enter code">
                                    </div>
                                </div>


                                <div class="form-group col-md-6">

                                    <div><input type="text" name="balance_amount" value="{{ $giftCard->balance_amount }}"
                                            isrequired="required" class="form-control" placeholder="Balance Amount">
                                    </div>
                                </div>



                               <div class="form-group col-md-6">
    <label for="is_active">Status</label>
    <select name="is_active" id="is_active" required class="form-control">
        <option value="" disabled {{ is_null($giftCard->is_active) ? 'selected' : '' }}>Select Status</option>
        <option value="1" {{ $giftCard->is_active == 1 ? 'selected' : '' }}>Active</option>
        <option value="0" {{ $giftCard->is_active == 0 ? 'selected' : '' }}>Inactive</option>
    </select>
</div>

<div class="form-group col-md-6">
    <label for="expiry_date">Expiry Date</label>
    <input type="date" id="expiry_date" name="expiry_date"
           value="{{ $giftCard->expiry_date }}" required class="form-control">
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
