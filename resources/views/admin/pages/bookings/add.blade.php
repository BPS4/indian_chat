@extends('admin.layout.default')
@section('bookings', 'active menu-item-open')

@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <div class="mb-7">
                <div class="booking-wrapper">
                    <div class="booking-card">
                        <h5 class="title">Create New Booking</h5>
                        <p class="subtitle">
                            Follow the steps to create a new hotel reservation
                        </p>

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

                        <!-- STEP CONTENT -->
                        <div class="step-content" id="step1">
                            <div>
                                <p class="select">Select Customer</p>
                                <p class="select-p">
                                    Choose an existing customer or create a new one
                                </p>
                            </div>
                            <div class="tab-btns mb-3">
                                <button id="existingBtn" class="tab-btn active">
                                    Existing Customer
                                </button>
                                <button id="newBtn" class="tab-btn">New Customer</button>
                            </div>

                            <!-- Existing Customer -->
                            <div id="existingCustomer">

                                <div class="w-100 position-relative mb-5 existing-customer">
                                    <i
                                        class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500"></i>
                                    <input class="form-control ps-25" type="search" id="searchInput"
                                        placeholder="Search by customers, Name or Email ID" aria-label="Search">
                                </div>

                                <div id="customerList">
                                    @forelse ($users as $user)
                                        <div class="customer-card" data-user-id="{{ $user->id }}">
                                            <div class="customer-details customer-name d-flex align-items-center">
                                                <div class="d-flex flex-column">
                                                    <span><img src="{{ asset('media/icons/profile-ico.png') }}"
                                                            alt="Profile"></span>
                                                    @if ($user->is_vip ?? false)
                                                        <span class="vip">VIP</span>
                                                    @endif
                                                </div>
                                                <p class="name">{{ $user->name ?? 'N/A' }}</p>
                                            </div>
                                            <p class="email">{{ $user->email ?? 'N/A' }}</p>
                                            <p class="phone">{{ $user->mobile ?? 'N/A' }}</p>
                                            <p class="city d-none">{{ $user->city ?? 'N/A' }}</p>
                                            <button class="btn btn-primary addCustomerBtn" data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                                data-mobile="{{ $user->mobile }}" data-city="{{ $user->city }}">
                                                Add More Guests
                                            </button>

                                        </div>
                                    @empty
                                        <p class="text-center">No customers found.</p>
                                    @endforelse
                                </div>

                                {{ $users->links('pagination::bootstrap-5') }}

                            </div>


                            <div class="modal fade" id="customerModal" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Customer Details & Guests</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <!-- CUSTOMER DETAILS -->


                                            <!-- GUEST TABLE -->
                                            <h5>Additional Guests</h5>
                                            <table class="table table-bordered" id="guestTable">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Mobile</th>
                                                        <th>Email</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>


                                            <button class="btn btn-success" id="addGuestRow">+ Add Guest</button>

                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary" id="saveGuestsBtn">Save Guests</button>
                                        </div>

                                    </div>
                                </div>
                            </div>








                            <!-- New Customer -->
                            <div id="newCustomer" class="d-none ">
                                <input type="text" Name="name" class="form-control " placeholder="Full Name" />
                                <input type="email" Name="email" class="form-control" placeholder="Email ID" />
                                <input type="text" Name="phone" class="form-control" placeholder="Phone Number" />
                                <input type="text" Name="city" class="form-control" placeholder="City" />

                                <button class="btn next-btn">Create Customer</button>
                            </div>
                            <!-- Buttons -->
                            <div class="action-btn ">
                                <a href="{{ url('/admin/bookings/list') }}"> <button type="button"
                                        class="btn btn-outline-secondary cancel-btn">Cancel</button></a>
                                <button class="btn next-btn" id="nextBtn">Next</button>
                            </div>

                        </div>



                        <!-- Step 2–4 placeholder -->
                        <!-- Step 2 -->
                        <div class="step-content d-none" id="step2">
                            <h6 class="fw-semibold mb-1">Select Hotel & Dates</h6>
                            <p class="text-muted small mb-3">
                                Choose your hotel and check-in/check-out dates
                            </p>



                            <div id="hotelList">
                                @forelse ($hotels as $hotel)
                                    <div class="booking-content hotel-item" data-hotel-id="{{ $hotel->id }}">
                                        <p class="hotel-name">{{ $hotel->name ?? 'N/A' }}</p>

                                        <div class="hotel-location pb-0">
                                            <span><img src="{{ asset('media/icons/location1.png') }}"
                                                    alt=""></span>
                                            {{ $hotel->address ?? 'N/A' }}
                                        </div>

                                        <div class="hotel-badges">
                                            @if (!empty($hotel->facilities))
                                                @foreach ($hotel->facilities as $facility)
                                                    <span>{{ $facility->facility_name }}</span>
                                                @endforeach
                                            @endif
                                        </div>

                                        <div class="hotel-rating">
                                            <div class="d-flex justify-content-between align-items-center gap-2 star">
                                                <span><img src="{{ asset('media/icons/emoji_star.png') }}"
                                                        alt=""></span>
                                                {{ number_format($hotel->rating_avg ?? 0, 1) }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center">No hotels found.</p>
                                @endforelse
                            </div>






                            <!-- Form Fields -->
                            <div class="row mt-3 g-2 checking">
                                <div class="col-md-4">
                                    <label for="check_in">Check-In Date</label>
                                    <input type="date" class="form-control" name="check_in"
                                        placeholder="Check-In Date" min="{{ date('Y-m-d') }}" />
                                </div>

                                <div class="col-md-4">
                                    <label for="Check-In ">Check-Out Date </label>
                                    <input type="date" class="form-control" name="check_out"
                                        placeholder="Check-Out Date" min="{{ date('Y-m-d') }}" />
                                </div>
                                {{-- <div class="col-md-2">
                                    <label for="Number-guests ">Number of Guests </label>
                                    <input type="number" class="form-control" name="adults"
                                        placeholder="Number of Guests" />
                                </div> --}}
                                <div class="col-md-4">
                                    <label for="Number-guests ">Number of Rooms </label>
                                    <input type="number" class="form-control" name="rooms"
                                        placeholder="No Of Rooms" />
                                </div>
                            </div>

                            {{-- <div class="mt-3 checking">
                                <input type="text" class="form-control" placeholder="Stay Duration :" readonly />
                            </div> --}}

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <button class="btn prev-btn" id="prevBtn">
                                    Previous
                                </button>
                                <div>

                                    <a href="{{ url('/admin/bookings/list') }}"> <button type="button"
                                            class="btn btn-outline-secondary cancel-btn">Cancel</button></a>
                                    <button class="btn next-btn" id="nextBtn2">Next</button>
                                </div>
                            </div>
                        </div>


                        <script>
                            $(document).ready(function() {

                                // alert('hi');
                                let guests = []; // all guests including main customer
                                let mainCustomer = {}; // stores main customer details

                                // -------------------------------------------------
                                // OPEN MODAL → LOAD SAVED GUESTS (if any)
                                // -------------------------------------------------
                                $(document).on("click", ".addCustomerBtn", function() {

                                    let id = $(this).data("id");
                                    let name = $(this).data("name");
                                    let email = $(this).data("email");
                                    let mobile = $(this).data("mobile");



                                    // Set main customer
                                    mainCustomer = {
                                        id: id,
                                        name: name || 'xyz',
                                        mobile: mobile,
                                        email: email,
                                        is_primary: true
                                    };

                                    // Reset table
                                    $("#guestTable tbody").empty();

                                    // ----------------------------------------------
                                    // Add MAIN CUSTOMER as first fixed row
                                    // ----------------------------------------------
                                    let mainRow = `
                                        <tr class="table-primary">
                                            <td style="display: none">${mainCustomer.id}</td>
                                            <td>${mainCustomer.name ? mainCustomer.name : "xyz"}</td>
                                            <td>${mainCustomer.mobile}</td>
                                            <td>${mainCustomer.email}</td>
                                            <td><span class="badge bg-secondary">Main</span></td>
                                        </tr>
                                    `;
                                    $("#guestTable tbody").append(mainRow);

                                    // ----------------------------------------------
                                    // Load previously added guests (if exists)
                                    // guests[0] = main user → skip
                                    // ----------------------------------------------
                                    if (guests.length > 1) {
                                        guests.forEach(function(g, index) {
                                            if (index === 0) return; // skip main customer

                                            let row = `
                                        <tr>
                                            <td class="d-none">${g.id}</td>
                                            <td><input type="text" class="form-control guest-name" value="${g.name }"></td>
                                            <td><input type="text" class="form-control guest-mobile" value="${g.phone}"  "
                                                            ></td>
                                            <td><input type="email" class="form-control guest-email" value="${g.email}"></td>
                                            <td><button class="btn btn-danger btn-sm removeRow">X</button></td>
                                        </tr>
                                    `;
                                            $("#guestTable tbody").append(row);
                                        });
                                    }

                                    $("#customerModal").modal("show");
                                });





                                // -------------------------------------------------
                                // ADD NEW GUEST ROW
                                // -------------------------------------------------
                                $("#addGuestRow").on("click", function() {

                                    let row = `
                                        <tr>
                                            <td><input type="text" class="form-control guest-name"></td>
                                            <td><input type="text" class="form-control guest-mobile"></td>
                                            <td><input type="email" class="form-control guest-email"></td>
                                            <td><button class="btn btn-danger btn-sm removeRow">X</button></td>
                                        </tr>
                                    `;
                                    $("#guestTable tbody").append(row);
                                });

                                // -------------------------------------------------
                                // DELETE A GUEST ROW
                                // -------------------------------------------------
                                $(document).on("click", ".removeRow", function() {
                                    $(this).closest("tr").remove();
                                });

                                // -------------------------------------------------
                                // SAVE BUTTON → STORE GUESTS IN 'guests' ARRAY
                                // -------------------------------------------------
                                $("#saveGuestsBtn").on("click", function() {

                                    guests = []; // empty array
                                    guests.push(mainCustomer); // Add main customer first

                                    let isValid = true;
                                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                    const phonePattern = /^[0-9]+$/; // only digits

                                    $("#guestTable tbody tr").each(function(index) {

                                        if (index === 0) return; // skip main user

                                        let name = $(this).find(".guest-name").val().trim();
                                        let phone = $(this).find(".guest-mobile").val().trim();
                                        let email = $(this).find(".guest-email").val().trim();

                                        // Reset previous invalid states
                                        $(this).find("input").removeClass("is-invalid");

                                        if (name === "") {
                                            $(this).find(".guest-name").addClass("is-invalid");
                                            isValid = false;
                                        }

                                        if (phone === "" || !phonePattern.test(phone)) {
                                            $(this).find(".guest-mobile").addClass("is-invalid");
                                            isValid = false;
                                            alert("Phone must contain only numeric values.");
                                            return false; // stop further execution
                                        }

                                        if (email === "" || !emailPattern.test(email)) {
                                            $(this).find(".guest-email").addClass("is-invalid");
                                            isValid = false;
                                            alert("Enter a valid email address.");
                                            return false;
                                        }

                                        guests.push({
                                            name: name,
                                            phone: phone,
                                            email: email,
                                            is_primary: false
                                        });
                                    });

                                    if (!isValid) return;

                                    console.log("Saved Guests:", guests);
                                    alert("Guests saved successfully!");
                                    $("#customerModal").modal("hide");
                                });





                                /* -----------------------------------------
                                   GLOBAL VARIABLES
                                ----------------------------------------- */
                                let selectedUserId = null;
                                let selectedHotel = null;
                                let selectedRoomType = null;
                                let sessionToken = null;
                                let jwtToken = null;
                                let checkIn = null;
                                let checkOut = null;
                                let selectedCombo = null;

                                let rooms = null;
                                let bookingPayload = {};

                                /* -----------------------------------------
                                   STEP NAVIGATION (Merged From Second Script)
                                ----------------------------------------- */
                                let currentStep = 1;

                                function showStep(step) {
                                    $(".step-content").addClass("d-none");
                                    $("#step" + step).removeClass("d-none");

                                    $(".step-circle").removeClass("active");
                                    $(`.step-circle[data-step="${step}"]`).addClass("active");
                                }

                                // Step circle click
                                $(".step-circle").on("click", function() {
                                    currentStep = parseInt($(this).data("step"));
                                    showStep(currentStep);
                                });

                                // Previous buttons
                                $("#prevBtn, #prevBtn2, #prevBtn3").on("click", function() {
                                    currentStep--;
                                    showStep(currentStep);
                                });

                                /* -----------------------------------------
                                   TAB SWITCH (Existing / New Customer)
                                ----------------------------------------- */
                                $("#existingBtn").on("click", function() {


                                    $(this).addClass("active");
                                    $("#newBtn").removeClass("active");

                                    $("#existingCustomer").removeClass("d-none");
                                    $("#newCustomer").addClass("d-none");
                                    selectedUserId = null;
                                });

                                $("#newBtn").on("click", function() {
                                    $(this).addClass("active");
                                    $("#existingBtn").removeClass("active");

                                    $("#existingCustomer").addClass("d-none");
                                    $("#newCustomer").removeClass("d-none");
                                    selectedUserId = null;
                                });

                                /* -----------------------------------------
                                   SELECT EXISTING CUSTOMER
                                ----------------------------------------- */
                                $("#customerList").on("click", ".customer-card", function() {
                                    //    alert('hi');
                                    $(".customer-card").removeClass("selected");
                                    $(this).addClass("selected");
                                    selectedUserId = $(this).data("user-id");
                                });

                                /* -----------------------------------------
                                   CREATE NEW CUSTOMER
                                ----------------------------------------- */
                                $("#newCustomer .next-btn").on("click", function() {

                                    let name = $('#newCustomer input[name="name"]').val();
                                    let email = $('#newCustomer input[name="email"]').val();
                                    let phone = $('#newCustomer input[name="phone"]').val();
                                    let city = $('#newCustomer input[name="city"]').val();

                                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                    if (!emailPattern.test(email)) {
                                        alert("Please enter a valid email address.");
                                        return;
                                    }

                                    if (!phone) {
                                        alert("Phone is required.");
                                        return;
                                    }

                                    // City validation (only alphabets)
                                    if (!city) {
                                        alert("City is required.");
                                        return;
                                    }

                                    if (!/^[a-zA-Z\s]+$/.test(city)) {
                                        alert("City must contain only alphabets.");
                                        return;
                                    }




                                    if (!name || !email) return alert("Name and Email are required.");

                                    $.ajax({
                                        url: "/admin/customers/add",
                                        method: "POST",
                                        data: {
                                            name,
                                            email,
                                            mobile: phone,
                                            city,
                                            _token: "{{ csrf_token() }}"
                                        },

                                        success: function(res) {
                                            if (res.status === false) {
                                                let msg = "";
                                                if (res.errors.email) msg += res.errors.email[0] + "\n";
                                                if (res.errors.mobile) msg += res.errors.mobile[0] + "\n";
                                                return alert(msg);
                                            }

                                            let user = res.user;

                                            let html = `
        <div class="customer-card" data-user-id="${user.id}">
            <div class="customer-details customer-name d-flex align-items-center">
                <div class="d-flex flex-column">
                    <span><img src="/media/icons/profile-ico.png" alt="Profile"></span>
                    ${user.is_vip ? '<span class="vip">VIP</span>' : ''}
                </div>
                <p class="name">${user.name || 'N/A'}</p>
            </div>
            <p class="email">${user.email || 'N/A'}</p>
            <p class="phone">${user.mobile || 'N/A'}</p>
            <p class="city d-none">${user.city || 'N/A'}</p>
            <button class="btn btn-primary addCustomerBtn"
                    data-id="${user.id}"
                    data-name="${user.name || ''}"
                    data-email="${user.email || ''}"
                    data-mobile="${user.mobile || ''}"
                    data-city="${user.city || ''}">
                Add More Guests
            </button>
        </div>
    `;

                                            $("#customerList").prepend(html);

                                            selectedUserId = user.id;

                                            window.selectedCustomerData = {
                                                id: user.id,
                                                name: user.name,
                                                email: user.email,
                                                phone: user.mobile,
                                                city: user.city || ""
                                            };

                                            $("#existingBtn").click();
                                            alert("Customer created successfully!");
                                        },

                                        error: function(xhr) {
                                            let response = xhr.responseJSON;
                                            let msg = "Something went wrong.";

                                            if (response && response.errors) {
                                                msg = "";
                                                if (response.errors.email) msg += response.errors.email[0] + "\n";
                                                if (response.errors.mobile) msg += response.errors.mobile[0] + "\n";
                                            }

                                            alert(msg);
                                        }
                                    });
                                });

                                /* -----------------------------------------
                                   STEP 1 → NEXT (Customer Selected)
                                ----------------------------------------- */
                                $("#nextBtn").on("click", function(e) {
                                    e.preventDefault();

                                    if (!selectedUserId) return alert("Please select a customer.");

                                    const card = $(`#customerList .customer-card[data-user-id="${selectedUserId}"]`);

                                    // alert(selectedUserId);

                                    window.selectedCustomerData = {
                                        id: selectedUserId,
                                        name: card.find(".name").text(),
                                        email: card.find(".email").text(),
                                        phone: card.find(".phone").text(),
                                        city: card.find(".city").text() || "",
                                        is_primary: true
                                    };

                                    // console.log(window.selectedCustomerData);

                                    currentStep = 2;
                                    showStep(2);
                                });

                                /* -----------------------------------------
                                   HOTEL SELECTION
                                ----------------------------------------- */
                                $("#hotelList").on("click", ".hotel-item", function() {
                                    $(".hotel-item").removeClass("selected");
                                    $(this).addClass("selected");
                                });

                                /* -----------------------------------------
                                   STEP 2 → NEXT (Hotel + Dates)
                                ----------------------------------------- */
                                $("#nextBtn2").on("click", function(e) {
                                    e.preventDefault();

                                    selectedHotel = $(".hotel-item.selected").data("hotel-id");
                                    if (!selectedHotel) return alert("Please select a hotel.");

                                    checkIn = $('input[name="check_in"]').val();
                                    checkOut = $('input[name="check_out"]').val();
                                    // adults = parseInt($('input[name="adults"]').val());
                                    rooms = parseInt($('input[name="rooms"]').val());


                                    // Convert to Date objects
                                    let today = new Date();
                                    today.setHours(0, 0, 0, 0); // set time to midnight for comparison

                                    let checkInDate = new Date(checkIn);
                                    let checkOutDate = new Date(checkOut);

                                    // Rule 1: check-in cannot be earlier than today
                                    if (checkInDate < today) {
                                        alert("Check-in date cannot be before today.");
                                        return false;
                                    }

                                    // Rule 2: check-in and check-out cannot be the same
                                    if (checkInDate.getTime() === checkOutDate.getTime()) {
                                        alert("Check-out date cannot be the same as check-in date.");
                                        return false;
                                    }

                                    // Optional: check-out cannot be before check-in
                                    if (checkOutDate < checkInDate) {
                                        alert("Check-out date cannot be before check-in date.");
                                        return false;
                                    }



                                    if (!checkIn || !checkOut || !guests || !rooms)
                                        return alert("Please fill all fields.");

                                    $("#nextBtn2").prop("disabled", true);

                                    /* STEP 2 API CHAIN */
                                    $.ajax({
                                        url: "{{ url('/api/session-token') }}",
                                        method: "GET",

                                        success: function(tokenResponse) {
                                            // alert(guests.length);
                                            sessionToken = tokenResponse.session_token;
                                            if (!sessionToken) return alert("Session token missing!");

                                            $.ajax({
                                                url: "{{ url('/api/hotel-rooms') }}",
                                                method: "POST",
                                                headers: {
                                                    "session-token": sessionToken
                                                },
                                                data: {
                                                    hotel_id: selectedHotel,
                                                    checkIn,
                                                    checkOut,
                                                    adults: guests.length === 0 ? 1 : guests.length,
                                                    rooms,
                                                    _token: "{{ csrf_token() }}"
                                                },

                                                success: function(response) {
                                                    $("#hotelNameText").text(response.hotel_name);
                                                    $("#roomList").empty();

                                                    response.combos.forEach(combo => {

                                                        $("#roomList").append(`
        <div class="room-card select-combo mb-2 d-flex justify-content-between align-items-start p-3 rounded-3 shadow-sm"
            data-room-type="${combo.rooms[0].room_type_id}"
            data-rooms-booked="${combo.rooms[0].rooms_booked}"
            data-addons='${JSON.stringify(combo.rooms[0].selected_addonsId || [])}'>

            <div>


                <h6>${combo.combo_title}</h6>
                <p>${combo.combo_summary ?? ""}</p>
                <p class="mb-1">Comfortable room with city view</p>

                <div class="d-flex flex-wrap gap-2 mb-2 standard-rooms">
                    <span class="badge">Wifi</span>
                    <span class="badge">Restaurant</span>
                    <span class="badge">Parking</span>
                </div>

                <!-- Hidden Addons -->
                <div class="d-none selected-addon-list">
                    ${(combo.selected_addonsId || []).join(",")}
                </div>
            </div>

            <div class="text-end">
                <h6 class="fw-semibold mb-0">${combo.new_price} INR
                    <span class="text-muted small">Per Night</span>
                </h6>
                <p class="mb-0">${combo.total_capacity} Max Capacity</p>
                <p class="mb-0">Available : ${combo.rooms.length} Rooms</p>
            </div>
        </div>
    `);

                                                    });


                                                    currentStep = 3;
                                                    showStep(3);
                                                },

                                                error: function() {
                                                    alert("Failed to fetch hotel rooms");
                                                },

                                                complete: function() {
                                                    $("#nextBtn2").prop("disabled", false);
                                                }
                                            });
                                        }
                                    });
                                });

                                /* -----------------------------------------
                                   SELECT ROOM COMBO
                                ----------------------------------------- */
                                $(document).on("click", ".select-combo", function() {
                                    $(".select-combo").removeClass("selected-combo");
                                    $(this).addClass("selected-combo");

                                    // Read the raw addons data safely
                                    let rawAddons = $(this).attr("data-addons");
                                    // console.log(rawAddons);

                                    // If attribute is missing or empty, use empty array
                                    if (!rawAddons) {
                                        rawAddons = "[]";
                                    }

                                    let addons = [];
                                    try {
                                        // Parse the JSON string
                                        addons = JSON.parse(rawAddons);

                                        // If parsed value is not an array, force it to be an array
                                        if (!Array.isArray(addons)) {
                                            addons = [];
                                        }
                                    } catch (e) {
                                        // In case of parsing error, fallback to empty array
                                        addons = [];
                                    }

                                    // console.log("Addons:", addons);

                                    // Build the selected combo object
                                    selectedCombo = {
                                        room_type_id: $(this).data("room-type"),
                                        rooms_booked: $(this).data("rooms-booked"),
                                        addons_ids: addons // guaranteed array
                                    };

                                    // console.log("Selected Combo:", selectedCombo);
                                });


                                /* -----------------------------------------
                                   STEP 3 → NEXT (Review Booking)
                                ----------------------------------------- */
                                $("#nextBtn3").on("click", function() {
                                    // Robust check for selected combo
                                    if (!selectedCombo || !selectedCombo.room_type_id) {
                                        alert("Select a room / plan first.");
                                        return;
                                    }

                                    $("#nextBtn3").prop("disabled", true);

                                    $.ajax({
                                        url: "{{ url('/api/review-bookings') }}",
                                        method: "POST",
                                        headers: {
                                            "session-token": sessionToken
                                        },
                                        data: {
                                            hotel_id: selectedHotel,
                                            check_in: checkIn,
                                            check_out: checkOut,
                                            adults: guests.length === 0 ? 1 : guests.length,
                                            child: 0,
                                            combo: [{
                                                room_type_id: selectedCombo.room_type_id,
                                                rooms_booked: selectedCombo.rooms_booked,
                                                addons_ids: selectedCombo.addons_ids // MUST be array
                                            }],
                                            coupon_code: "",
                                            _token: "{{ csrf_token() }}"
                                        },

                                        success: function(reviewResponse) {
                                            let hotel = reviewResponse.hotel;
                                            let itinerary = reviewResponse.itinerary;
                                            let price = reviewResponse.price_summary;

                                            // Fill Step 4 Review
                                            $("#reviewHotelName").text(hotel.name);
                                            $("#reviewHotelLocation").text(hotel.address ?? "N/A");
                                            $("#reviewRoomType").text(reviewResponse.selected_room_type ?? "Room");
                                            $("#reviewGuests").text(itinerary.adults + " Guest(s)");
                                            $("#reviewCheckIn").text(itinerary.check_in);
                                            $("#reviewCheckOut").text(itinerary.check_out);
                                            $("#reviewNights").text(itinerary.nights + " Nights");
                                            $("#reviewBasePrice").text(price.base_room_charges + " INR");
                                            $("#reviewHotelDiscount").text("-" + price.hotel_discount + " INR");
                                            $("#reviewTaxes").text(price.taxes + " INR");
                                            $("#reviewTotalPayable").text(price.total_payable + " INR");

                                            // STEP 3 → Populate bookingPayload.guest from selected customer
                                            let mainguests = [];
                                            if (window.selectedCustomerData) {
                                                // alert('hi');

                                                mainguests = [{
                                                    id: window.selectedCustomerData.id,
                                                    name: window.selectedCustomerData.name || 'xyz',
                                                    email: window.selectedCustomerData.email || '',
                                                    phone: window.selectedCustomerData.phone || '',
                                                    is_primary: true
                                                }];
                                            }

                                            $("#guestName").text(window.selectedCustomerData.name);


                                            // console.log("Booking Payload:", bookingPayload);

                                            console.log("guests:", guests);
                                            let finalGuests = guests.length > 0 ? guests : mainguests;


                                            bookingPayload = {
                                                hotel_id: selectedHotel,
                                                check_in: checkIn,
                                                check_out: checkOut,
                                                adults: guests.length === 0 ? 1 : guests.length,

                                                child: 0,
                                                combo: [{
                                                    room_type_id: selectedCombo.room_type_id,
                                                    rooms_booked: selectedCombo.rooms_booked,
                                                    addons_ids: selectedCombo
                                                        .addons_ids // MUST be array
                                                }],

                                                gst_no: "",
                                                company_name: "",
                                                address: "",
                                                payable_amount: parseFloat(price
                                                    .total_payable), // make sure it's a float
                                                created_by: 'Admin',
                                                guest: finalGuests,

                                            };

                                            console.log("Booking Payload:", bookingPayload);
                                            // return false;

                                            currentStep = 4;
                                            showStep(4);
                                        },

                                        complete: function() {
                                            $("#nextBtn3").prop("disabled", false);
                                        }
                                    });
                                });

                                /* -----------------------------------------
                                   FINISH → CREATE ORDER
                                ----------------------------------------- */
                                $("#finishBtn").on("click", function() {

                                    $("#finishBtn").prop("disabled", true);

                                    const adminEmail = "{{ session('email') }}";

                                    $.ajax({
                                        url: "{{ url('/api/admin-login') }}",
                                        method: "POST",
                                        headers: {
                                            "session-token": sessionToken
                                        },
                                        data: {
                                            email: adminEmail
                                        },

                                        success: function(login) {
                                            jwtToken = login.token;

                                            $.ajax({
                                                url: "{{ url('/api/create-order') }}",
                                                method: "POST",
                                                headers: {
                                                    "Authorization": "Bearer " + jwtToken
                                                },
                                                data: bookingPayload,

                                                success: function() {
                                                    alert("Booking Created Successfully!");
                                                    window.location.href = "/admin/bookings/list";
                                                },

                                                error: function() {
                                                    alert("Order creation failed!");
                                                },

                                                complete: function() {
                                                    $("#finishBtn").prop("disabled", false);
                                                }
                                            });
                                        }
                                    });
                                });

                            });
                        </script>


                        <!-- STEP 3 -->
                        <div class="step-content d-none" id="step3">
                            <h6 class="fw-semibold mb-1">Select Room Type</h6>
                            <p class="text-muted small mb-3" id="hotelNameText"></p>

                            <div id="roomList"></div> <!-- API rooms will be inserted here -->

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <button class="btn prev-btn" id="prevBtn2">Previous</button>
                                <div>
                                    <a href="{{ url('/admin/bookings/list') }}"> <button type="button"
                                            class="btn btn-outline-secondary cancel-btn">Cancel</button></a>
                                    <button class="btn next-btn" id="nextBtn3">Next</button>
                                </div>
                            </div>
                        </div>


                        <!-- STEP 4 -->
                        <div class="step-content d-none" id="step4">
                            <h6 class="fw-semibold mb-1">Review Booking Details</h6>
                            <p class="text-gray medium mb-3">
                                Please review all details before confirming the booking
                            </p>

                            <!-- Guest Info -->
                            <div class="bg-gradient-gold rounded-3 p-3 mb-3">
                                <h6 class="mb-0">Guest Information</h6>
                                <p class="mb-0 text-gray" id="guestName">Tanishq Marwah</p>
                            </div>

                            <!-- Hotel & Room Details -->
                            <div class="border rounded-3 p-3 mb-3">
                                <h6 class="fw-semibold mb-2">Hotel & Room Details</h6>

                                <div class="row small mb-1">
                                    <div class="col-6 text-muted">Hotel :</div>
                                    <div class="col-6 fw-semibold text-end" id="reviewHotelName"></div>
                                </div>

                                <div class="row small mb-1">
                                    <div class="col-6 text-muted">Location :</div>
                                    <div class="col-6 fw-semibold text-end" id="reviewHotelLocation"></div>
                                </div>

                                <div class="row small mb-1">
                                    <div class="col-6 text-muted">Room Type :</div>
                                    <div class="col-6 fw-semibold text-end" id="reviewRoomType"></div>
                                </div>

                                <div class="row small">
                                    <div class="col-6 text-muted">Guest :</div>
                                    <div class="col-6 fw-semibold text-end" id="reviewGuests"></div>
                                </div>
                            </div>

                            <!-- Booking Details -->
                            <div class="border rounded-3 p-3 mb-3">
                                <h6 class="fw-semibold mb-2">Booking Details</h6>

                                <div class="row small mb-1">
                                    <div class="col-6 text-muted">Check-In :</div>
                                    <div class="col-6 fw-semibold text-end" id="reviewCheckIn"></div>
                                </div>

                                <div class="row small mb-1">
                                    <div class="col-6 text-muted">Check-Out :</div>
                                    <div class="col-6 fw-semibold text-end" id="reviewCheckOut"></div>
                                </div>

                                <div class="row small">
                                    <div class="col-6 text-muted">Duration :</div>
                                    <div class="col-6 fw-semibold text-end" id="reviewNights"></div>
                                </div>
                            </div>

                            <!-- Pricing Breakdown -->
                            <div class="border rounded-3 p-3 mb-3">
                                <h6 class="fw-semibold mb-2">Pricing Breakdown</h6>

                                <div class="row small mb-1">
                                    <div class="col-6 text-muted">Base Room Charges :</div>
                                    <div class="col-6 fw-semibold text-end" id="reviewBasePrice"></div>
                                </div>

                                <div class="row small mb-1">
                                    <div class="col-6 text-muted">Hotel Discount :</div>
                                    <div class="col-6 fw-semibold text-end" id="reviewHotelDiscount"></div>
                                </div>

                                <div class="row small mb-1">
                                    <div class="col-6 text-muted">Taxes :</div>
                                    <div class="col-6 fw-semibold text-end" id="reviewTaxes"></div>
                                </div>

                                <div class="row small">
                                    <div class="col-6 text-muted">Total Payable :</div>
                                    <div class="col-6 fw-semibold text-end text-dark" id="reviewTotalPayable"></div>
                                </div>
                            </div>

                            <!-- Special Request -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Special Request ( Optional )</label>
                                <textarea class="form-control" rows="2" placeholder="Any special request or notes for this booking ..."></textarea>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <button class="btn prev-btn" id="prevBtn3">Previous</button>
                                <div>
                                    <a href="{{ url('/admin/bookings/list') }}"> <button type="button"
                                            class="btn btn-outline-secondary cancel-btn">Cancel</button></a>
                                    <button class="btn next-btn" id="finishBtn">Finish</button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
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
    <script>
        $(document).on("click", ".room-card", function() {
            $(".room-card").removeClass("selected"); // remove highlight from all
            $(this).addClass("selected"); // highlight clicked one
        });
    </script>





@endsection
