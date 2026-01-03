@extends('admin.layout.default')

@section('addons', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">AddOns </h3>
                <p>Manage AddOns</p>
            </div>
            <div class="card-toolbar">

                <a href="{{ route('addons.create') }}" class="btn btn-primary font-weight-bolder">
                    + Add AddOns</a>



            </div>
        </div>


        <div class="card-body">

            <form method="GET" action="">
                <div class="card-search mb-5">
                    <div class="w-75 position-relative">

                        <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-gray-500"></i>

                        <input id="searchInput" class="form-control ps-25" type="search" name="search"
                            value="{{ request('search') }}" placeholder="Search by title">

                        <!-- ❌ Cross icon -->
                        <i id="clearSearch"
                            class="fa fa-times position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                            style="cursor:pointer"></i>

                    </div>
                </div>
            </form>

            <script>
                document.getElementById("clearSearch").addEventListener("click", function() {
                    window.location.href = window.location.pathname; // refresh page without query string
                });
            </script>


            <div id="tableView" class="offer-table-container p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 offer-table" id="myTable">
                        <thead class="offer-table-header">
                            <tr>
                                <th class="">Id</th>
                                <th>Name</th>
                                <th class="w-50">Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($addOns as $addon)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $addon->name }}</td>
                                    <td>{{ $addon->description }}</td>
                                    <td>
                                        {{-- <button class="btn btn-sm text-primary border-0" data-bs-toggle="tooltip"
                                            title="view">
                                            <i class="la la-eye"></i>
                                        </button> --}}
                                        <a href="{{ route('addons.edit', $addon->id) }}"
                                            class="btn btn-sm text-primary border-0" data-bs-toggle="tooltip"
                                            title="Edit">
                                            <i class="la la-edit"></i>
                                        </a>
                                        <form action="{{ route('addons.destroy', $addon->id) }}" method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this addon?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm text-danger border-0"
                                                data-bs-toggle="tooltip" title="Delete">
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

{{-- Styles Section --}}
@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection


{{-- Scripts Section --}}
@section('scripts')
    <script>
        $(document).ready(function() {
            // $('#myTable').DataTable();
            // $('.dataTables_filter label input[type=search]').addClass('form-control form-control-sm');
            // $('.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
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

        $('#toggleViewIcon').on('click', function() {
            const tableView = $('#tableView');
            const cardView = $('#cardView');
            const icon = $(this);

            if (tableView.is(':visible')) {
                // Switch to card view
                tableView.hide();
                cardView.show();
                icon.attr('src', '{{ asset('media/icons/table-icon.png') }}');
            } else {
                // Switch to table view
                cardView.hide();
                tableView.show();
                icon.attr('src', '{{ asset('media/icons/card-icon.png') }}');
            }
        });
    </script>
    {{-- vendors --}}
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <!-- <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script> -->

    {{-- page scripts --}}
    <!-- <script src="{{ asset('js/pages/crud/datatables/basic/basic.js') }}" type="text/javascript"></script>
                                                        <script src="{{ asset('js/app.js') }}" type="text/javascript"></script> -->
@endsection
