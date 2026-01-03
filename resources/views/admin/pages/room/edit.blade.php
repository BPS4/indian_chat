@extends('admin.layout.default')
@section('hotels', 'active menu-item-open')

@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="booking-wrapper">
                    <form method="POST" action="{{ route('hotel-room.update', [$hotelId, $roomType->id]) }}"
                        enctype="multipart/form-data" class="w-100">
                        @csrf
                        @method('PUT')

                        <div class="booking-card">
                            <h5 class="title">Edit Room</h5>
                            <p class="subtitle">Update the details of this room</p>

                            <!-- Step Progress -->
                            <div class="step-wrapper mb-3" id="stepWrapper">
                                <div class="step-circle active" data-step="1">1</div>
                                <div class="step-line"></div>
                                <div class="step-circle" data-step="2">2</div>
                                <div class="step-line"></div>
                                <div class="step-circle" data-step="3">3</div>
                                <div class="step-line"></div>
                                <div class="step-circle" data-step="4">4</div>
                            </div>

                            <!-- STEP 1: Basic Details -->
                            <div class="step-content" id="step1">
                                <p class="select">Update Basic Room Details</p>
                                <div class="row g-3">
                                    <div class="form-group col-md-6">
                                        <input type="text" name="room_type"
                                            value="{{ old('room_type', $roomType->room_name) }}" class="form-control"
                                            placeholder="Enter Room Type">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="text" name="room_size"
                                            value="{{ old('room_size', $roomType->room_size) }}" class="form-control"
                                            placeholder="Enter room size">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="text" name="max_guests"
                                            value="{{ old('max_guests', $roomType->max_guests) }}" class="form-control"
                                            placeholder="Enter max guests">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="text" name="max_child"
                                            value="{{ old('max_child', $roomType->max_child) }}" class="form-control"
                                            placeholder="Enter max child">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="text" name="bed_type"
                                            value="{{ old('bed_type', $roomType->bed_type) }}" class="form-control"
                                            placeholder="Enter bed type">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="file" name="photo_url" class="form-control">
                                        @if ($roomType->photo_url)
                                            <img src="{{ asset( $roomType->photo_url) }}" alt="Room Photo"
                                                class="mt-2" width="100">
                                        @endif
                                    </div>

                                    <div class="form-group col-md-12">
                                        <textarea name="description" rows="4" class="form-control" placeholder="Enter Full Description">{{ old('description', $roomType->description) }}</textarea>
                                    </div>
                                </div>

                                <div class="action-btn mt-4 text-end">
                                    <a href="{{ route('hotel-room.index', $hotelId) }}"
                                        class="btn btn-outline-secondary">Cancel</a>
                                    <button type="button" class="btn next-btn" data-next="2">Next</button>
                                </div>
                            </div>

                            <!-- STEP 2: Room Price -->
                            @php $price = $roomType->roomPrices->first(); @endphp
                            <div class="step-content d-none" id="step2">
                                <p class="select">Update Room Price</p>
                                <div class="row g-3">
                                    <div class="form-group col-md-6">
                                        <input type="date" name="start_date"
                                            value="{{ old('start_date', $price?->start_date) }}" class="form-control"
                                            placeholder="Start Date">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="date" name="end_date"
                                            value="{{ old('end_date', $price?->end_date) }}" class="form-control"
                                            placeholder="End Date">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="text" name="base_price"
                                            value="{{ old('base_price', $price?->base_price) }}" class="form-control"
                                            placeholder="Base Price">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="text" name="extra_person_price"
                                            value="{{ old('extra_person_price', $price?->extra_person_price) }}"
                                            class="form-control" placeholder="Extra Person Price">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <input type="text" name="currency"
                                            value="{{ old('currency', $price?->currency) }}" class="form-control"
                                            placeholder="Currency">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-btn"
                                        data-prev="1">Previous</button>
                                    <button type="button" class="btn next-btn" data-next="3">Next</button>
                                </div>
                            </div>

                            <!-- STEP 3: Facilities -->
                            @php $selectedFacilities = $roomType->facilities->pluck('id')->toArray(); @endphp
                            <div class="step-content d-none" id="step3">
                                <p class="select">Update Facility Details</p>
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

                            <!-- STEP 4: Addons -->
                            <div class="step-content d-none" id="step4">
                                <p class="select">Addons Details</p>

                                <div id="addonsContainer">
                                    @foreach ($roomType->addons as $addon)
                                        <div class="addon-row row g-2 align-items-end mb-2">
                                            <div class="form-group col-md-4">
                                                <select name="addons[]" class="form-control" required>
                                                    <option value="">Select Addon</option>
                                                    @foreach ($addons as $a)
                                                        <option value="{{ $a->id }}"
                                                            {{ $a->id == $addon->id ? 'selected' : '' }}>
                                                            {{ $a->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <input type="number" name="price[]" class="form-control"
                                                    value="{{ $addon->pivot->price }}" placeholder="Price" required>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <input type="number" name="person[]" class="form-control"
                                                    value="{{ $addon->pivot->per_person }}" placeholder="Per Person"
                                                    required>
                                            </div>

                                            <div class="form-group col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger w-100 removeAddonBtn">
                                                    <i class="bi bi-dash-lg"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach

                                    <!-- Optional: Add blank row for new Addon -->
                                    <div class="addon-row row g-2 align-items-end mb-2">
                                        <div class="form-group col-md-4">
                                            <select name="addons[]" class="form-control">
                                                <option value="">Select Addon</option>
                                                @foreach ($addons as $a)
                                                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="number" name="price[]" class="form-control"
                                                placeholder="Price">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <input type="number" name="person[]" class="form-control"
                                                placeholder="Per Person">
                                        </div>

                                    </div>
                                </div>


                                <div class="form-group mt-2">
                                    <button type="button" class="btn btn-success addAddonBtn">
                                        <i class="bi bi-plus-lg"></i> Add Addon
                                    </button>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary prev-btn"
                                        data-prev="3">Previous</button>
                                    <button type="submit" class="btn btn-success">Update</button>
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

    <script>
        document.addEventListener('click', function(e) {
            const target = e.target.closest('button');

            // Add Addon
            if (target && target.classList.contains('addAddonBtn')) {
                const container = document.getElementById('addonsContainer');
                const row = document.createElement('div');
                row.classList.add('addon-row', 'row', 'g-2', 'align-items-end', 'mb-2');

                row.innerHTML = `
            <div class="form-group col-md-4">
                <select name="addons[]" class="form-control" required>
                    <option value="">Select Addon</option>
                    @foreach ($addons as $addon)
                        <option value="{{ $addon->id }}">{{ $addon->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3">
                <input type="number" name="price[]" class="form-control" placeholder="Price" required>
            </div>
            <div class="form-group col-md-3">
                <input type="number" name="person[]" class="form-control" placeholder="Per Person" required>
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger w-100 removeAddonBtn">
                    <i class="bi bi-dash-lg"></i> Remove
                </button>
            </div>
        `;
                container.appendChild(row);
            }

            // Remove Addon
            if (target && target.classList.contains('removeAddonBtn')) {
                target.closest('.addon-row').remove();
            }

            // Step Navigation
            if (target && target.classList.contains('next-btn')) {
                const next = target.dataset.next;
                document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
                document.getElementById('step' + next).classList.remove('d-none');
                document.querySelectorAll('.step-circle').forEach(el => el.classList.remove('active'));
                document.querySelector(`.step-circle[data-step="${next}"]`).classList.add('active');
            }

            if (target && target.classList.contains('prev-btn')) {
                const prev = target.dataset.prev;
                document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
                document.getElementById('step' + prev).classList.remove('d-none');
                document.querySelectorAll('.step-circle').forEach(el => el.classList.remove('active'));
                document.querySelector(`.step-circle[data-step="${prev}"]`).classList.add('active');
            }
        });
    </script>

    <script>
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelectorAll('.addon-row').forEach(row => {
                const addon = row.querySelector('select[name="addons[]"]').value;
                const price = row.querySelector('input[name="price[]"]').value;
                const person = row.querySelector('input[name="person[]"]').value;

                if (!addon && !price && !person) {
                    row.remove(); // prevent sending empty addon rows
                }
            });
        });
    </script>

@endsection
