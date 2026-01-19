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
                {{-- <a href="{{ url('/admin/bookings/add') }}" class="btn btn-primary font-weight-bolder">
                    + Quick Booking </a> --}}
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
                            <h4 class="payment-card-amount py-4">{{ $customers }}</h4>
                            <p class="payment-card-subtext ">0% from last month</p>
                        </div>
                    </div>

                    <!-- Successful Payments -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="payment-card shadow-sm">
                            <div class="payment-card-title rupees">
                                <span>Total Admin</span>
                                <img src="{{ asset('media/icons/rupees.png') }}" alt="">
                            </div>
                            <h4 class="payment-card-amount py-4">₹{{ $admin }}
                            </h4>
                            <p class="payment-card-subtext"> 0% from last month</p>
                        </div>
                    </div>

                    <!-- Pending Payments -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="payment-card shadow-sm">
                            <div class="payment-card-title">
                                <span>Total Message</span>
                                <img src="{{ asset('media/icons/active-customer.png') }}" alt="">
                            </div>
                            <h4 class="payment-card-amount py-4">{{ $message }}</h4>
                            <p class="payment-card-subtext">0% from last month</p>
                        </div>
                    </div>

                    <!-- Refund Issued -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="payment-card shadow-sm">
                            <div class="payment-card-title">
                                <span>Total City</span>
                                <img src="{{ asset('media/icons/pending-payment.png') }}" alt="">
                            </div>
                            @php $occupacy = occupacyRate(); @endphp
                            <h4 class="payment-card-amount py-4">{{ $city }}</h4>
                            <p class="payment-card-subtext">0% from last month</p>
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
