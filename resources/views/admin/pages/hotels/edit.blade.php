@extends('admin.layout.default')
@section('hotels', 'active menu-item-open')

@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="booking-wrapper">
                    <form method="POST" action="{{ route('hotels.update', $hotel->id) }}" enctype="multipart/form-data"
                        class="w-100">
                        @csrf
                        @method('PUT')

                        <div class="booking-card">
                            <h5 class="title">Edit Hotel</h5>
                            <p class="subtitle">Follow the steps to update a hotel registration</p>

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
                                        <input type="text" name="hotel_name" value="{{ $hotel->name }}"
                                            class="form-control" placeholder="Enter Hotel Name" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="hotel_address" value="{{ $hotel->address }}"
                                            class="form-control" placeholder="Enter Full Address" required>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <textarea name="description" rows="4" class="form-control" placeholder="Enter Full Description" required>{{ $hotel->description }}</textarea>
                                    </div>
                                </div>

                                <div class="action-btn mt-4 text-end">
                                    <a href="{{ url('/admin/hotels/list') }}"> <button type="button"
                                            class="btn btn-outline-secondary cancel-btn">Cancel</button></a>
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
                                                <option value="{{ $location->id }}"
                                                    {{ $location->id == $hotel->location_id ? 'selected' : '' }}>
                                                    {{ $location->city }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <select class="form-control" name="locality_id" id="locality_id">
                                            <option value="">Select Locality</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="number" name="longitude" value="{{ $hotel->longitude }}"
                                            class="form-control" placeholder="Longitude">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="number" name="latitude" value="{{ $hotel->latitude }}"
                                            class="form-control" placeholder="Latitude">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-btn"
                                        data-prev="1">Previous</button>
                                    <button type="button" class="btn next-btn" data-next="3">Next</button>
                                </div>
                            </div>

                            <!-- STEP 3: Facilities -->
                            @php $selectedFacilities = $hotel->hotelFacilities->pluck('facility_id')->toArray(); @endphp
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
                                                        id="facility_{{ $facility->id }}" value="{{ $facility->id }}"
                                                        {{ in_array($facility->id, $selectedFacilities) ? 'checked' : '' }}>
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
                                        @php
                                            $photo = $hotel->hotelPhotos[$i - 1] ?? null;
                                            $key = $photo ? $photo->id : "new_$i"; // IMPORTANT
                                        @endphp

                                        <div class="col-md-4">
                                            <div class="form-group">

                                                <label for="image_{{ $key }}">Upload Image
                                                    {{ $i }}</label>

                                                <input type="file" name="images[{{ $key }}]"
                                                    id="image_{{ $key }}" class="form-control"
                                                    accept="image/*">
                                            </div>
                                        </div>

                                        <div class="col-md-4 d-flex align-items-center">
                                            <div class="form-check mt-4">

                                                <input type="radio" name="cover_image" value="{{ $key }}"
                                                    id="cover_{{ $key }}" class="form-check-input"
                                                    @if ($photo && $photo->is_cover) checked @endif>

                                                <label class="form-check-label" for="cover_{{ $key }}">
                                                    Set as Cover Image
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-4 d-flex align-items-center">
                                            @if ($photo)
                                                <img src="{{ asset($photo->photo_url) }}" class="img-thumbnail"
                                                    style="width:120px; height:100px; object-fit:cover;">
                                            @endif
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
                                            value="{{ $hotel->hotelPolicies->checkin_time ?? null }}" required
                                            class="form-control">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="check_out_time" class="form-label">Check-out Time</label>
                                        <input type="time" name="check_out_time" id="check_out_time"
                                            value="{{ $hotel->hotelPolicies->checkout_time ?? null }}" required
                                            class="form-control">
                                    </div>

                                    <div class="form-group col-md-12">
                                        <textarea name="cancellation_policy" rows="4" class="form-control ckeditor" placeholder="Cancellation Policy"
                                            required>{{ $hotel->hotelPolicies->cancellation_policy ?? null }}</textarea>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <textarea name="extra_bed_policy" rows="4" class="form-control ckeditor" placeholder="Extra Bed Policy"
                                            required>{{ $hotel->hotelPolicies->extra_bed_policy ?? null }}</textarea>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <textarea name="child_policy" rows="4" class="form-control ckeditor" placeholder="Child Policy" required>{{ $hotel->hotelPolicies->child_policy ?? null }}</textarea>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.22.1/standard/ckeditor.js"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.ckeditor').forEach(textarea => {
                CKEDITOR.replace(textarea);
            });
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
            const selectedLocality = "{{ $hotel->locality_id }}"; // fetch selected locality

            function loadLocalities(locationId, callback = null) {
                localitySelect.innerHTML = '<option value="">Loading...</option>';

                fetch(`/locality-check/${locationId}`)
                    .then(response => response.json())
                    .then(data => {
                        localitySelect.innerHTML = '<option value="">Select Locality</option>';
                        data.forEach(locality => {
                            const option = document.createElement('option');
                            option.value = locality.id;
                            option.textContent = locality.name;

                            // pre-select locality in edit mode
                            if (selectedLocality && locality.id == selectedLocality) {
                                option.selected = true;
                            }

                            localitySelect.appendChild(option);
                        });

                        if (callback) callback();
                    })
                    .catch(error => {
                        console.error('Error fetching localities:', error);
                        localitySelect.innerHTML = '<option value="">Error loading localities</option>';
                    });
            }

            // When user changes location
            locationSelect.addEventListener('change', function() {
                const locationId = this.value;
                if (locationId) {
                    loadLocalities(locationId);
                } else {
                    localitySelect.innerHTML = '<option value="">Select Locality</option>';
                }
            });

            // 🔥 Auto-load localities in EDIT PAGE
            if (locationSelect.value) {
                loadLocalities(locationSelect.value);
            }
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
