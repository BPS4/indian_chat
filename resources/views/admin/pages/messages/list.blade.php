@extends('admin.layout.default')

@section('hotels', 'active menu-item-open')
@section('content')
@php
    use Illuminate\Support\Str;
@endphp
    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Message Management</h3>
                <p>Manage your broadcast messages</p>
            </div>
            <div class="card-toolbar">
                <a href="{{ url('/admin/message/add') }}" class="btn btn-primary font-weight-bolder">
                    <i class="fa fa-plus"></i> Add Message
                </a>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ url('/admin/message/list') }}">
                <div class="card-search mb-5">
                    <div class="w-75 position-relative">
                        <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500"></i>
                        <input class="form-control ps-25" type="search" placeholder="Search by message, state, or city" 
                               name="search" value="{{ request('search') }}" aria-label="Search">
                    </div>

                    {{-- <div class="d-flex justify-content-between align-items-center gap-3 drops">
                        <div class="dropdown">
                            <button class="btn btn-lg dropdown-toggle" type="button" id="stateDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ request('state') ?? 'All States' }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="stateDropdown">
                                <li><a class="dropdown-item" href="{{ url('/admin/message/list') . '?search=' . request('search') }}">
                                    All States
                                </a></li>
                                @foreach(['Andhra Pradesh', 'Karnataka', 'Kerala', 'Maharashtra', 'Tamil Nadu', 'Telangana', 'Delhi'] as $state)
                                    <li><a class="dropdown-item" href="{{ url('/admin/message/list') . '?search=' . request('search') . '&state=' . $state }}">
                                        {{ $state }}
                                    </a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div> --}}
                </div>
            </form>

            <div id="tableView">
                <table class="table table-bordered table-hover" id="myTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Message</th>
                            <th>Calling No</th>
                            <th>Website Link</th>
                            <th>State</th>
                            <th>City</th>
                            {{-- <th>Total Users</th> --}}
                            <th>Media</th>
                            <th>Created_by</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($messages as $message)
                            <tr>
                                <td>{{ $messages->firstItem() + $loop->index }}</td>


                                <td>{{ Str::limit($message->description, 50) }}</td>
                                <td>{{ $message->calling_number ?? 'N/A' }}</td>
                                <td>
                                    @if($message->website_link)
                                        <a href="{{ $message->website_link }}" target="_blank" class="btn btn-sm btn-link">
                                            <i class="fa fa-external-link"></i> View
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $message->state ?? 'N/A' }}</td>
                                <td>{{ $message->city ?? 'N/A' }}</td>
                                {{-- <td>{{ $message->total_users ?? 0 }}</td> --}}
                                <td>
                                    @if($message->media)
                                        <a href="{{ asset( $message->media) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fa fa-file"></i> View
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $message->user->name ?? 'N/A' }}</td>
                                <td>{{ $message->created_at->format('j/n/Y, H:i') }}</td>

                                <td>
                                    <div class="btn-group">
                                        {{-- <a href="{{ url('/admin/message/edit/' . $message->id) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a> --}}
                                        <button onclick="deleteMessage({{ $message->id }})" 
                                                class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No messages found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $messages->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
    
    <script>
        $(document).ready(function() {
           $('#myTable').DataTable({
    ordering: true,
    order: [], // ❌ disable default ordering
    columnDefs: [
        { orderable: false, targets: 0 } // ❌ disable sort on SR NO
    ],
    pageLength: 25,
    lengthMenu: [[25, 50, 100], [25, 50, 100]],
    language: {
        lengthMenu: 'Show _MENU_ entries'
    }
});
        });

        function deleteMessage(messageId) {
            if (!confirm('Are you sure you want to delete this message?')) {
                return;
            }

            $.ajax({
                url: '/admin/message/delete/' + messageId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Message deleted successfully!');
                        location.reload();
                    } else {
                        alert('Failed to delete message!');
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert('Something went wrong!');
                }
            });
        }
    </script>
@endsection
