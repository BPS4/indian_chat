@extends('admin.layout.default')

@section('facility', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="{{ route('facility.update', $facility->id) }}" class="w-100"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-lg-9 col-xl-12 mt-3">
                            <h3>Update Facility</h3>
                            <p>Enter the details to update Facility</p>
                            <div class="row align-items-center">
                                <div class="form-group col-md-6">
                                    <div>
                                        <select name="group_id" required class="form-control">
                                            <option value="" disabled selected>Select Group</option>

                                            @foreach ($facilityGroups as $group)
                                                <option value="{{ $group->id }}"
                                                    {{ $group->id == $facility->group_id ? 'selected' : '' }}>
                                                    {{ $group->group_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div>
                                        <select name="facility_for" required class="form-control">
                                            <option value="" disabled selected>Select For</option>

                                            <option value="Hotel"
                                                {{ $facility->facility_for == 'Hotel' ? 'selected' : '' }}>
                                                Hotel</option>
                                            <option value="Room"
                                                {{ $facility->facility_for == 'Room' ? 'selected' : '' }}>
                                                Room</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group col-md-6">

                                    <div><input type="text" name="facility_name" value="{{ $facility->facility_name }}"
                                            isrequired="required" class="form-control" placeholder="facility_name">
                                    </div>
                                </div>


                                <div class="form-group col-md-6">
                                    <label for="icon">icon</label>
                                    <div>
                                        <!-- If there's an icon, show it. Otherwise, show a placeholder -->
                                        @if ($facility->icon)
                                            <img src="{{ asset( $facility->icon) }}" alt="facility icon"
                                                class="img-fluid mb-3" style="max-height: 150px;">
                                        @else
                                            <img src="https://via.placeholder.com/150" alt="No icon" class="img-fluid mb-3"
                                                style="max-height: 150px;">
                                        @endif
                                    </div>

                                    <!-- File Input -->
                                    <input type="file" name="icon" class="form-control" placeholder="Upload icon">

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
