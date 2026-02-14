@extends('admin.layout.default')

@section('refral_commision', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="{{ route('commision.update', $commision->id) }}" class="w-100"
                        enctype="multipart/form-data">
                        @csrf
                            @method('PUT')
                            <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Edit Commisions</h3>
                            <p>Update the commission details</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">

                                        <label for="joining_bonus" class="form-label">Joining Bonus</label>
                                    <div><input type="text" name="joining_bonus" value="{{ $commision->joining_bonus }}"
                                            required="required" class="form-control" placeholder="Enter Joining Bonus">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="referral_commision" class="form-label">Refral Commision</label>
                                    <div><input type="text" name="referral_commision" value="{{ $commision->referral_commision }}"
                                            required="required" class="form-control" placeholder="Enter Referral Commission ">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">

                                      <label for="roi" class="form-label">Roi (%)</label>
                                    <div><input type="number" id="roi" name="roi" value="{{ $commision->roi }}"
                                            required="required" class="form-control" placeholder="Enter Roi in percentage" min="0" max="100" step="0.01">
                                    </div>
                                </div>
                               
                                {{-- <div class="form-group col-md-6">

                                    <div><input type="text" name="link" value="{{ $slider->link }}"
                                            isrequired="required" class="form-control" placeholder="Enter url">
                                    </div>
                                </div>
                                <div class="form-group col-md-6">

                                    <div><input type="text" name="button_text" value="{{ $slider->button_text }}"
                                            isrequired="required" class="form-control" placeholder="Enter Button Text">
                                    </div>
                                </div> --}}

                           
                                <div
                                    class="d-flex  gap-3 mt-4">
                                    {{-- <button class="btn bg-gray px-5">Cancel</button> --}}
                                    <button class="btn  bg-brown add" type="submit">Update</button>
                                    <a href="{{ route('commision.index') }}" class="btn bg-gray px-5">Cancel</a>

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
