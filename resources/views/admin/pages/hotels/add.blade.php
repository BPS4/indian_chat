@extends('admin.layout.default')
@section('hotels', 'active menu-item-open')

@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="booking-wrapper">
                    <form method="POST" action="{{ url('/admin/hotels/add') }}" enctype="multipart/form-data" class="w-100">
                        @csrf

                        <div class="booking-card">
                            <h5 class="title">Create New Hotel</h5>
                            <p class="subtitle">Follow the steps to create a new hotel registration</p>

                            <!-- Step Progress -->
                            <div class="step-wrapper mb-3" id="stepWrapper">
                                <div class="step-circle active" data-step="1">1</div>
                                <div class="step-line"></div>
                                <div class="step-circle" data-step="2">2</div>
                                <div class="step-line"></div>
                                <div class="step-circle" data-step="3">3</div>
                                <div class="step-line"></div>
                                <div class="step-circle" data-step="4">4</div>
                                <div class="step-line"></div>
                                <div class="step-circle" data-step="5">5</div>
                            </div>

                            <!-- STEP 1: Basic Details -->
                            <div class="step-content" id="step1">
                                <p class="select">Enter Basic Hotel Details</p>
                                <div class="row g-3">
                                    <div class="form-group col-md-6">
                                        <input type="text" name="hotel_name" value="{{ old('hotel_name') }}"
                                            class="form-control" placeholder="Enter Hotel Name" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="hotel_address" value="{{ old('hotel_address') }}"
                                            class="form-control" placeholder="Enter Full Address" required>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <textarea name="description" rows="4" class="form-control" placeholder="Enter Full Description" required>{{ old('description') }}</textarea>
                                    </div>
                                </div>

                                <div class="action-btn mt-4 text-end">
                                    <button type="button" class="btn btn-outline-secondary cancel-btn">Cancel</button>
                                    <button type="button" class="btn next-btn" data-next="2">Next</button>
                                </div>
                            </div>

                            <!-- STEP 2: Location Details -->
                            <div class="step-content d-none" id="step2">
                                <p class="select">Add Location Details</p>
                                <div class="row g-3">
                                    <div class="form-group col-md-6">
                                        <select class="form-control" name="location_id" id="location_id" required>
                                            <option value="">Select Location</option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->city }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <select class="form-control" name="locality_id" id="locality_id" required>
                                            <option value="">Select Locality</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="number" name="longitude" value="{{ old('longitude') }}"
                                            class="form-control" placeholder="Longitude" step="any">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="number" name="latitude" value="{{ old('latitude') }}"
                                            class="form-control" placeholder="Latitude" step="any">
                                    </div>

                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-btn"
                                        data-prev="1">Previous</button>
                                    <button type="button" class="btn next-btn" data-next="3">Next</button>
                                </div>
                            </div>

                            <!-- STEP 3: Facilities -->
                            <div class="step-content d-none" id="step3">
                                <p class="select">Add Facility Details</p>
                                <div class="row mt-3">
                                    <div class="form-group col-md-7">
                                        <label for="facility_id">Facility Name</label>
                                        <div id="facilityCheckboxes" class="border rounded p-2"
                                            style="max-height: 200px; overflow-y: auto;">
                                            @foreach ($FacilityMaster as $facility)
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" type="checkbox" name="facilities[]"
                                                        id="facility_{{ $facility->id }}" value="{{ $facility->id }}">
                                                    <label class="form-check-label" for="facility_{{ $facility->id }}">
                                                        {{ $facility->facility_name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-btn"
                                        data-prev="2">Previous</button>
                                    <button type="button" class="btn next-btn" data-next="4">Next</button>
                                </div>
                            </div>

                            <!-- STEP 4: Hotel Images -->
                            <div class="step-content d-none" id="step4">
                                <p class="select">Add Hotel Images</p>
                                <div class="row g-3 align-items-end">
                                    @for ($i = 1; $i <= 4; $i++)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="image_{{ $i }}">Upload Image
                                                    {{ $i }}</label>
                                                <input type="file" name="images[]" id="image_{{ $i }}"
                                                    class="form-control" accept="image/*" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6 d-flex align-items-center">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="radio" name="cover_image"
                                                    id="cover_{{ $i }}" value="{{ $i }}">
                                                <label class="form-check-label" for="cover_{{ $i }}">Set as
                                                    Cover Image</label>
                                            </div>
                                        </div>
                                    @endfor
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-btn"
                                        data-prev="3">Previous</button>
                                    <button type="button" class="btn next-btn" data-next="5">Next</button>
                                </div>
                            </div>

                            <!-- STEP 5: Hotel Policies -->
                            <div class="step-content d-none" id="step5">
                                <p class="select">Add Hotel Policies</p>
                                <div class="row g-3">
                                    <div class="form-group col-md-6">
                                        <label for="check_in_time" class="form-label">Check-in Time</label>
                                        <input type="time" name="check_in_time" id="check_in_time"
                                            value="{{ old('check_in_time') }}" required class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="check_out_time" class="form-label">Check-out Time</label>
                                        <input type="time" name="check_out_time" id="check_out_time"
                                            value="{{ old('check_out_time') }}" required class="form-control">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <textarea name="cancellation_policy" rows="4" class="form-control ckeditor" placeholder="Cancellation Policy"
                                            required>{{ old('cancellation_policy') }}</textarea>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <textarea name="extra_bed_policy" rows="4" class="form-control ckeditor" placeholder="Extra Bed Policy"
                                            required>{{ old('extra_bed_policy') }}</textarea>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <textarea name="child_policy" rows="4" class="form-control ckeditor" placeholder="Child Policy" required>{{ old('child_policy') }}</textarea>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-btn"
                                        data-prev="4">Previous</button>
                                    <button type="submit" class="btn btn-success">Finish</button>
                                </div>
                            </div>




                        </div>
                    </form>

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


@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.25.1/standard/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/4.25.1/standard/ckeditor.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.ckeditor').forEach(textarea => {
                CKEDITOR.replace(textarea);
            });
        });
    </script>

     <script>
                                document.querySelector("form").addEventListener("submit", function(e) {
                                    let cancel = CKEDITOR.instances['cancellation_policy'].getData().trim();
                                    let bed = CKEDITOR.instances['extra_bed_policy'].getData().trim();
                                    let child = CKEDITOR.instances['child_policy'].getData().trim();

                                    if (!cancel) {
                                        alert("Cancellation Policy is required");
                                        e.preventDefault();
                                        return false;
                                    }

                                    if (!bed) {
                                        alert("Extra Bed Policy is required");
                                        e.preventDefault();
                                        return false;
                                    }

                                    if (!child) {
                                        alert("Child Policy is required");
                                        e.preventDefault();
                                        return false;
                                    }
                                });
                            </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const steps = document.querySelectorAll(".step-content");
            const circles = document.querySelectorAll(".step-circle");

            // Show the correct step
            function showStep(step) {
                steps.forEach((s, i) => s.classList.toggle("d-none", i + 1 !== step));
                circles.forEach((c, i) => c.classList.toggle("active", i + 1 <= step));
            }

            // Validate required inputs in the current step
            function validateStep(step) {
                const currentStep = document.querySelector(`#step${step}`);
                const requiredFields = currentStep.querySelectorAll("[required]");
                let allValid = true;

                requiredFields.forEach(field => {
                    if (!field.value || (field.type === "checkbox" && !field.checked)) {
                        field.classList.add("is-invalid");
                        allValid = false;
                    } else {
                        field.classList.remove("is-invalid");
                    }
                });

                if (!allValid) {
                    // Scroll to first invalid field for better UX
                    const firstInvalid = currentStep.querySelector(".is-invalid");
                    if (firstInvalid) firstInvalid.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });
                }

                return allValid;
            }

            // Handle "Next" button
            document.querySelectorAll(".next-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    const currentStep = parseInt(btn.closest(".step-content").id.replace("step",
                        ""));
                    if (validateStep(currentStep)) {
                        const nextStep = parseInt(btn.dataset.next);
                        showStep(nextStep);
                    } else {
                        alert("Please fill all required fields before proceeding.");
                    }
                });
            });

            // Handle "Previous" button
            document.querySelectorAll(".prev-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    const prevStep = parseInt(btn.dataset.prev);
                    showStep(prevStep);
                });
            });

            // Start at Step 1
            showStep(1);
        });
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const locationSelect = document.getElementById('location_id');
            const localitySelect = document.getElementById('locality_id');

            locationSelect.addEventListener('change', function() {
                const locationId = this.value;
                localitySelect.innerHTML = '<option value="">Loading...</option>';

                // alert(locationId);
                if (locationId) {
                    fetch(`/locality-check/${locationId}`)
                        .then(response => response.json())
                        .then(data => {
                            localitySelect.innerHTML = '<option value="">Select Locality</option>';
                            data.forEach(locality => {
                                const option = document.createElement('option');
                                option.value = locality.id;
                                option.textContent = locality.name;
                                localitySelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching localities:', error);
                            localitySelect.innerHTML =
                                '<option value="">Error loading localities</option>';
                        });
                } else {
                    localitySelect.innerHTML = '<option value="">Select Locality</option>';
                }
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $('#facility_group_id').on('change', function() {
                var groupId = $(this).val();
                var $facilityContainer = $('#facilityCheckboxes');

                // Show loading message
                $facilityContainer.html('<p class="text-info m-0">Loading facilities...</p>');

                if (groupId) {
                    $.ajax({
                        url: '/facilities-check/' + groupId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data.length > 0) {
                                $facilityContainer.empty(); // Clear old checkboxes

                                $.each(data, function(index, facility) {
                                    var checkboxHtml = `
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="facilities[]"
                                           id="facility_${facility.id}"
                                           value="${facility.id}">
                                    <label class="form-check-label" for="facility_${facility.id}">
                                        ${facility.facility_name}
                                    </label>
                                </div>
                            `;
                                    $facilityContainer.append(checkboxHtml);
                                });
                            } else {
                                $facilityContainer.html(
                                    '<p class="text-muted m-0">No facilities found for this group.</p>'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching facilities:', error);
                            $facilityContainer.html(
                                '<p class="text-danger m-0">Error loading facilities.</p>');
                        }
                    });
                } else {
                    $facilityContainer.html(
                        '<p class="text-muted m-0">Select a facility group first...</p>');
                }
            });
        });
    </script>





@endsection
