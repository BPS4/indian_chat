@extends('admin.layout.default')
@section('hotels', 'active menu-item-open')

@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="booking-wrapper">
                    <form method="POST" action="{{ route('hotel-room.store', $hotelId) }}" enctype="multipart/form-data"
                        class="w-100">
                        @csrf

                        <div class="booking-card">
                            <h5 class="title">Create New Room</h5>
                            <p class="subtitle">Follow the steps to create a new hotel room</p>

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
                                {{-- <div class="step-circle" data-step="5">5</div> --}}
                            </div>

                            <!-- STEP 1: Basic Details -->
                            <div class="step-content" id="step1">
                                <p class="select">Enter Basic Room Details</p>
                                <div class="row g-3">
                                    <div class="form-group col-md-6">
                                        <input type="text" name="room_type" value="{{ old('room_type') }}"
                                            class="form-control" placeholder="Enter Room Type">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="room_size" value="{{ old('room_size') }}"
                                            class="form-control" placeholder="Enter room size">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="max_guests" value="{{ old('max_guests') }}"
                                            class="form-control" placeholder="Enter max guests">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="max_child" value="{{ old('max_child') }}"
                                            class="form-control" placeholder="Enter max child">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="bed_type" value="{{ old('bed_type') }}"
                                            class="form-control" placeholder="Enter bed type">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="file" name="photo_url" value="{{ old('photo_url') }}"
                                            class="form-control" placeholder="Enter photo url">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <textarea name="description" rows="4" class="form-control" placeholder="Enter Full Description">{{ old('description') }}</textarea>
                                    </div>
                                </div>

                                <div class="action-btn mt-4 text-end">
                                    <button type="button" class="btn btn-outline-secondary cancel-btn">Cancel</button>
                                    <button type="button" class="btn next-btn" data-next="2">Next</button>
                                </div>
                            </div>

                            <!-- STEP 2: Location Details -->
                            <div class="step-content d-none" id="step2">
                                <p class="select">Add Room Price</p>
                                <div class="row g-3">


                                    <div class="form-group col-md-6">
                                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                                            class="form-control" placeholder="start date">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                                            class="form-control" placeholder="end date">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="base_price" value="{{ old('base_price') }}"
                                            class="form-control" placeholder="Base price" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="extra_person_price"
                                            value="{{ old('extra_person_price') }}" class="form-control"
                                            placeholder="Extra person price" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="currency" value="{{ old('currency') }}"
                                            class="form-control" placeholder="Currency" required>
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

                           
                            <div class="step-content d-none" id="step4">
                                <p class="select">Addons Details</p>

                                <div id="addonsContainer">
                                    <!-- Addon Row Template -->
                                    <div class="addon-row row g-2 align-items-end mb-2">
                                        <!-- Facility Selection -->
                                        <div class="form-group col-md-4">
                                            {{-- <label>Addons</label> --}}
                                            <select name="addons[]" class="form-control" >
                                                <option value="">Select Addon</option>
                                                @foreach ($addons as $addon)
                                                    <option value="{{ $addon->id }}">{{ $addon->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Price -->
                                        <div class="form-group col-md-3">
                                            {{-- <label>Price</label> --}}
                                            <input type="number" name="price[]" class="form-control"
                                                placeholder="Price" >
                                        </div>

                                        <!-- Per Person -->
                                        <div class="form-group col-md-3">
                                            {{-- <label>Per Person</label> --}}
                                            <input type="number" name="person[]" class="form-control"
                                                placeholder="Per Person" >
                                        </div>

                                        <!-- Add Button -->
                                        <div class="form-group col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn btn-success w-100 addAddonBtn">
                                                <i class="bi bi-plus-lg"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-btn"
                                        data-prev="3">Previous</button>
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
        document.addEventListener('click', function(e) {
            const target = e.target.closest('button');

            // If Add button clicked
            if (target && target.classList.contains('addAddonBtn')) {
                const container = document.getElementById('addonsContainer');
                const currentRow = target.closest('.addon-row');
                const newRow = currentRow.cloneNode(true);

                // Clear all inputs
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                newRow.querySelector('select').selectedIndex = 0;

                // Convert Add button → Remove button
                const newBtn = newRow.querySelector('.addAddonBtn');
                newBtn.classList.remove('btn-success', 'addAddonBtn');
                newBtn.classList.add('btn-danger', 'removeAddonBtn');
                newBtn.innerHTML = '<i class="bi bi-dash-lg"></i> Remove';

                container.appendChild(newRow);
            }

            // If Remove button clicked
            if (target && target.classList.contains('removeAddonBtn')) {
                const row = target.closest('.addon-row');
                row.remove();
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
