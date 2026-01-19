@extends('admin.layout.default')

@section('Slider', 'active menu-item-open')

@section('content')
    @php
        $contact = $contacts->first();
    @endphp

    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    {{-- SHOW EXISTING CONTACT NUMBERS --}}
                    {{-- @if ($contact)
                    <div class="col-12 mb-4">
                        <div class="alert alert-info">
                            <strong>Saved Contact Numbers</strong><br>
                            Primary: {{ $contact->primary }}<br>
                            Secondary: {{ $contact->secondary ?? 'N/A' }}
                        </div>
                    </div>
                @endif --}}

                    {{-- ADD / UPDATE FORM --}}
                    <form method="POST" action="{{ route('contacts.create') }}" class="w-100">
                        @csrf

                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>{{ $contact ? 'Update Contact Numbers' : 'Add Contact Numbers' }}</h3>
                            <p>Enter the details to {{ $contact ? 'update' : 'create' }} contact numbers</p>

                            <div class="row align-items-center">

                                <div class="form-group col-md-6">
                                    <label>Primary Contact Number</label>
                                    <input type="tel" name="Primary" class="form-control" required
                                        placeholder="Primary Contact Number"
                                        value="{{ old('Primary', $contact->primary ?? '') }}" pattern="[0-9]{10}"
                                        maxlength="10"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                                    <small class="form-text text-muted">Enter exactly 10 digits</small>
                                </div>


                                <div class="form-group col-md-6">
                                    <label>Secondary Contact Number</label>
                                    <input type="tel" name="Secondary" class="form-control"
                                        placeholder="Secondary Contact Number"
                                        value="{{ old('Secondary', $contact->secondary ?? '') }}" pattern="[0-9]{10}"
                                        maxlength="10"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                                    <small class="form-text text-muted">Enter exactly 10 digits</small>
                                </div>


                                <div class="d-flex gap-3 mt-4">
                                    <button type="submit" class="btn bg-brown add">
                                        {{ $contact ? 'Update' : 'Save' }}
                                    </button>

                                    <a href="{{ route('contacts.index') }}" class="btn bg-gray px-5">
                                        Cancel
                                    </a>
                                </div>

                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
@endsection

@section('scripts')
@endsection
