@extends('admin.layout.default')

@section('dashboard', 'active menu-item-open')
@section('content')

    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Dashboard Overview
                </h3>
                <p>Welcome back! Here's what's happening at your hotels today.</p>
            </div>
            <div class="card-toolbar">
                <!--begin::Button-->
                <a href="{{ url('/admin/bookings/add') }}" class="btn btn-primary font-weight-bolder">
                    + Quick Booking </a>
                <!-- <div>
                                                                                                                                    <img src="{{ asset('media/icons/card-icon.png') }}" alt="">
                                                                                                                                </div> -->


            </div>
        </div>
        <div class="card-body">

            <div class=" py-4 mb-5">
                <div class="row g-3">
                    <!-- Total Revenue -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="payment-card shadow-sm">
                            <div class="payment-card-title">
                                <span>Total Bookings</span>
                                <!-- <i class="bi bi-house"></i> -->
                                <img src="{{ asset('media/icons/total-bookings.png') }}" alt="">

                            </div>
                            <h4 class="payment-card-amount py-4">{{ $bookings['total_booking'] }}</h4>
                            <p class="payment-card-subtext ">{{ $bookings['change'] }}% from last month</p>
                        </div>
                    </div>

                    <!-- Successful Payments -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="payment-card shadow-sm">
                            <div class="payment-card-title rupees">
                                <span>Monthly Revenue</span>
                                <img src="{{ asset('media/icons/rupees.png') }}" alt="">
                            </div>
                            <h4 class="payment-card-amount py-4">₹{{ $monthlyRevenue['amount'] }}
                            </h4>
                            <p class="payment-card-subtext">{{ $monthlyRevenue['change'] }}% from last month</p>
                        </div>
                    </div>

                    <!-- Pending Payments -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="payment-card shadow-sm">
                            <div class="payment-card-title">
                                <span>Active Customers</span>
                                <img src="{{ asset('media/icons/active-customer.png') }}" alt="">
                            </div>
                            <h4 class="payment-card-amount py-4">{{ $customers['percentage'] }}%</h4>
                            <p class="payment-card-subtext">{{ $customers['change'] }}% from last month</p>
                        </div>
                    </div>

                    <!-- Refund Issued -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="payment-card shadow-sm">
                            <div class="payment-card-title">
                                <span>Occupancy Rate</span>
                                <img src="{{ asset('media/icons/pending-payment.png') }}" alt="">
                            </div>
                            @php $occupacy = occupacyRate(); @endphp
                            <h4 class="payment-card-amount py-4">{{ $occupacy['current'] }}</h4>
                            <p class="payment-card-subtext">{{ $occupacy['growth'] }}% from last month</p>
                            <div class="progress occupancy-progress mt-2">
                                {{-- <div class="progress-bar" role="progressbar" style="width: 80%;" aria-valuenow="80"
                                    aria-valuemin="0" aria-valuemax="100"></div> --}}
                            </div>
                        </div>
                    </div>

                </div>
            </div>



            <div class="hotel-dashboard container-fluid">

                <div class="row g-4 mb-4">
                    <!-- Revenue and Booking Trend -->
                    <div class="col-lg-7">
                        <div class="card p-3 h-100">
                            <h5>Revenue and Booking Trend</h5>
                            <p>Monthly revenue and booking trends over the last 6 months</p>
                            <div class="chart-container">
                                <canvas id="revenueChart"></canvas>
                            </div>

                        </div>
                    </div>

                    <!-- Room Type Occupancy -->
                    <div class="col-lg-5">
                        <div class="card p-3 h-100">
                            <h5>Room Type Occupancy</h5>
                            <p>Current occupancy rates by room type</p>
                            <div class="chart-container">
                                <canvas id="occupancyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-5">
                    <!-- Upcoming Check-In -->
                    <div class="col-lg-6">
                        <div class="card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-5">
                                <div>
                                    <h5>Today Upcoming Check-In</h5>
                                    <p>Today expected arrivals</p>
                                </div>
                                <select class="form-select w-auto" id="hotelFilter">
                                    <option value=" ">All Hotels</option>
                                    @foreach ($hotels as $hotel)
                                        <option value="{{ $hotel->id }}"
                                            {{ $hotel->id == $hotelId ? 'selected' : '' }}>{{ $hotel->name }}</option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="d-flex flex-column gap-3 mt-5">
                                @foreach ($upcomingCheckIns as $upcomingCheckIn)
                                    <div class="check-card">
                                        <div class="guest-info">
                                            <h6>{{ $upcomingCheckIn->guests?->first()->guest_name ?? null }}</h6>
                                            <small>{{ $upcomingCheckIn->rooms->first()->roomType->room_name }}</small>
                                        </div>
                                        <div class="guest-time">
                                            {{ date('h:i A', strtotime($upcomingCheckIn->checkin_time)) }}

                                        </div>
                                        <small>{{ $upcomingCheckIn->hotel->name }}</small>

                                        <div class="guest-count">{{ $upcomingCheckIn->guests_count }} Guest</div>
                                    </div>
                                @endforeach



                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Check-Out -->
                    <div class="col-lg-6">
                        <div class="card p-3">
                            <div class="d-flex justify-content-between align-items-center mb-5">
                                <div>
                                    <h5>Today Upcoming Check-Out</h5>
                                    <p>Today expected Departure</p>
                                </div>
                                <select class="form-select w-auto" id="hotelFilterUpcoming">
                                    <option value="">All Hotels</option>
                                    @foreach ($hotels as $hotel)
                                        <option value="{{ $hotel->id }}"
                                            {{ $hotel->id == $upcominghotelId ? 'selected' : '' }}>
                                            {{ $hotel->name }}</option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="d-flex flex-column gap-3 mt-5">
                                @foreach ($upcomingCheckOuts as $upcomingCheckOut)
                                    <div class="check-card">
                                        <div class="guest-info">
                                            <h6>{{ $upcomingCheckOut->guests?->first()->guest_name ?? null }}</h6>
                                            <small>{{ $upcomingCheckOut->rooms->first()->roomType->room_name }}</small>
                                        </div>
                                        <div class="guest-time">
                                            {{ date('h:i A', strtotime($upcomingCheckOut->checkin_time)) }}

                                        </div>
                                        <small>{{ $upcomingCheckOut->hotel->name }}</small>

                                        <div class="guest-count">{{ $upcomingCheckOut->guests_count }} Guest</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @php $chartData = roomAnalyticsChart();    @endphp
            {{-- {{ $details->links('pagination::bootstrap-5') }} --}}
            <!--end: Datatable-->
        </div>
    </div>

@endsection

{{-- Styles Section --}}
@section('styles')



@endsection


{{-- @section('script') --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("✅ Dashboard charts initialized from list");
            const labels = @json($monthlyRevenueCharts['labels']);
            const revenueData = @json($monthlyRevenueCharts['data']);
            const bookingData = @json($bookingChart['data']);
            const ctx1 = document.getElementById('revenueChart');
            const ctx2 = document.getElementById('occupancyChart');
            const chartData = @json($chartData);
            console.log(chartData.map(i => i.room_type));
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Revenue',
                            data: revenueData,
                            backgroundColor: '#4285F4'
                        },
                        {
                            label: 'Bookings',
                            data: bookingData,
                            backgroundColor: '#C49B66'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: chartData.map(i => i.room_type),
                    datasets: [{
                        data: chartData.map(i => i.total),
                        backgroundColor: ['#795548', '#03A9F4', '#8BC34A', '#FF9800', '#E57373', '#20c997','#28a745']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hotelSelect = document.getElementById('hotelFilter');
            const hotelSelectUpcoming = document.getElementById('hotelFilterUpcoming');

            hotelSelect.addEventListener('change', function() {
                let hotelId = this.value;

                // Build URL
                let baseUrl = "{{ route('admin.dashboard') }}";

                if (hotelId) {
                    window.location.href = baseUrl + "?hotel_id=" + hotelId;
                } else {
                    window.location.href = baseUrl; // reset to all hotels
                }
            });
            hotelSelectUpcoming.addEventListener('change', function() {
                let hotelId = this.value;

                // Build URL
                let baseUrl = "{{ route('admin.dashboard') }}";

                if (hotelId) {
                    window.location.href = baseUrl + "?upcoming_hotel_id=" + hotelId;
                } else {
                    window.location.href = baseUrl; // reset to all hotels
                }
            });
        });
    </script>
@endpush
