@extends('admin.layout.default')

@section('dashboard', 'active menu-item-open')
@section('content')

    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Dashboard Overview
                </h3>
                <p>Welcome back! Here's what's happening In your Bussiness.</p>
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
                                <span>Total Users</span>
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
                                <span>Total Admin</span>
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
                                <span>Total Message</span>
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
                                <span>Total States</span>
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
