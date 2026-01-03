@extends('admin.layout.default')

@section('Review', 'active menu-item-open')

@section('content')
    <div class="crd cad-custom">


        <div class="card-body">
            <div class="review-details p-4 bg-white border rounded shadow-lg">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h4 class="mb-0 text-primary">
                        <i class="fas fa-comment-dots"></i> <span style="color:#C39853">Review Details</span>
                    </h4>
                    @php
                        $statusText = $review->is_approved ? 'Approved' : 'Pending';
                        $statusColor = $review->is_approved ? '#20c997' : '#E4E6EF';
                    @endphp

                    <span class="badge badge-pill px-3 py-2" style="background: {{ $statusColor }}; color: #fff;">
                        {{ $statusText }}
                    </span>

                </div>

                <!-- User & Hotel Info -->
                <div class="d-flex align-items-center mb-4">
                    <div class="mr-3">
                        <img src="{{ $review->user->profile_pic ?? asset('images/user/user.jpg') }}" alt="User Image"
                            class="rounded-circle shadow-sm" width="70" height="70">
                    </div>
                    <div>
                        <h5 class="mb-1" style="color:#C39853"><i class="fas fa-user"></i> {{ $review->user->name }}</h5>
                        <small class="text-muted d-block mb-1">
                            <i class="fas fa-hotel"></i> Hotel: {{ $review->hotel?->name ?? 'N/A' }}
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt"></i> Booking ID: {{ $review->booking_id ?? 'N/A' }}
                        </small>
                    </div>
                </div>



                <!-- Review Image -->
                @if ($review->image)
                    <div class="mb-4">
                        <h6><i class="fas fa-image"></i> Attached Image:</h6>
                        <img src="{{ asset($review->image) }}" alt="Review Image" class="img-fluid rounded shadow-sm border"
                            style="max-width: 350px;">
                    </div>
                @endif
                <!-- Review Content -->
                <div class="mb-4">
                    <h6 class="text-warning">
                        <i class="fas fa-star"></i> Rating:
                        <span class="text-dark font-weight-bold">{{ $review->rating ?? 'N/A' }}/5</span>
                    </h6>
                    <div class="p-3 bg-light border rounded">
                        <p class="mb-0">{{ $review->review }}</p>
                    </div>
                </div>
                <hr>

                <!-- Admin Reply Section -->
                <div class="admin-reply mt-4">
                    <h5 class="" style="color:#20c997"><i class="fas fa-reply"></i> Admin Reply</h5>

                    @if ($review->reply)
                        <div class=" text-white p-3 rounded mt-2 shadow-sm" style="background: rgb(221, 170, 88)">
                            <p class="mb-0">{{ $review->reply }}</p>
                            <small class="d-block mt-1 text-white-50">
                                <i class="fas fa-clock"></i> Replied on {{ $review->updated_at->format('d-m-Y, h:i A') }}
                            </small>
                        </div>
                    @else
                        <form action="{{ route('review.reply', $review->id) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="form-group">
                                <textarea name="reply" class="form-control" rows="3" placeholder="Write your reply..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">
                                <i class="fas fa-paper-plane"></i> Send Reply
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Success Message -->
                {{-- @if (session('success'))
                    <div class="alert alert-success mt-4 shadow-sm">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif --}}
            </div>
        </div>




    </div>
@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

{{-- Scripts Section --}}
@section('scripts')
@endsection
