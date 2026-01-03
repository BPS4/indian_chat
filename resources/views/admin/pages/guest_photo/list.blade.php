@extends('admin.layout.default')

@section('guestphoto', 'active menu-item-open')
@section('content')

    <div class="card card-custom">

        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Guest Photos</h3>
                <p>Manage guest photos uploaded for hotels</p>
            </div>

            <div class="card-toolbar">
                <a href="{{ route('guest-photo.create') }}" class="btn btn-primary font-weight-bolder">
                    + Add Guest Photos
                </a>
            </div>
        </div>

        <div class="card-body">

            {{-- Search Bar --}}

              <form method="GET" action=" ">
            <div class="card-search mb-5">
                <div class="w-75 position-relative">
                    <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500"></i>
                    <input class="form-control ps-25" type="search" placeholder="Search by hotel or ID"
                        aria-label="Search" name="search" value="{{ request('search') }}">
                </div>
            </div>
               </form>

            {{-- Table View --}}
            <div id="tableView" class="offer-table-container p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 offer-table" id="myTable">
                        <thead class="offer-table-header">
                            <tr>
                                <th>ID</th>
                                <th>Hotel</th>
                                <th>Photo</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($photos as $photo)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    {{-- Hotel Name --}}
                                    <td>{{ $photo->hotel->name ?? 'N/A' }}</td>

                                    {{-- Thumbnail --}}
                                    <td>
                                        <img src="{{ asset($photo->photo_url) }}" width="80" height="60"
                                            style="object-fit:cover;border-radius:4px;">
                                    </td>

                                    <td class="text-center">

                                        {{-- Edit --}}
                                        {{-- <a href="{{ route('guest-photo.edit', ['guest_photo' => $photo->id]) }}"
                                            class="btn btn-sm text-primary border-0" title="Edit">
                                            <i class="la la-edit"></i>
                                        </a> --}}

                                        {{-- Delete --}}
                                        <form action="{{ route('guest-photo.destroy', ['guest_photo' => $photo->id]) }}"
                                            method="POST" style="display:inline;"
                                            onsubmit="return confirm('Are you sure want to delete this photo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm text-danger border-0" title="Delete">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

        </div>

    </div>

@endsection


{{-- Styles --}}
@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection


{{-- Scripts --}}
@section('scripts')
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                aLengthMenu: [
                    [25, 50, 100],
                    [25, 50, 100]
                ],
                pageLength: 25,
                language: {
                    lengthMenu: 'Show _MENU_ entries'
                }
            });
        });
    </script>
@endsection
