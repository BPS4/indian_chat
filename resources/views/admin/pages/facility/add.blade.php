@extends('admin.layout.default')
@section('Facility', 'active menu-item-open')
@section('content')
    <div class="card card-custom">
        <div class="card-body">
            <h3>Add New Facility</h3>
            <p>Enter the details to create new Facility</p>

            <form method="POST" action="{{ route('facility.store') }}" class="w-100" enctype="multipart/form-data">
                {{ csrf_field() }}

                <!-- Add New Group -->
                <div class="mb-4 border-bottom pb-3">
                    <h5>Add New Group</h5>
                    <div class="input-group mb-2">
                        <input type="text" id="newGroupName" class="form-control" placeholder="Enter Group Name">
                        <button type="button" id="addGroupBtn" class="btn btn-primary">Add</button>
                    </div>
                </div>

                <!-- All Groups List -->
                <div class="mb-4 border-bottom pb-3">
                    <h5>All Groups</h5>
                    <table class="table table-striped" id="groupTable">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Group Name</th>
                                <th>Number of Facilities</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Groups will be dynamically populated here -->
                        </tbody>
                    </table>
                </div>

                <!-- Facility Fields -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label>Select Group</label>
                        <select name="group_id" id="groupSelect" class="form-control" required>
                            <option value="" disabled selected>Select Group</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Upload Icon</label>
                        <input type="file" name="icon" value="{{ old('icon') }}" required class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Facility Name</label>
                        <input type="text" name="facility_name" value="{{ old('facility_name') }}" required
                            class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Facility For</label>
                        <select name="facility_for" required class="form-control">
                            <option value="" disabled selected>Select For</option>
                            <option value="Hotel">Hotel</option>
                            <option value="Room">Room</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex  gap-3 mt-4">
                    <button type="submit" class="btn bg-brown add">Save</button>
                    <button type="reset" class="btn bg-gray px-5">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection
{{-- Styles Section --}}
@section('styles')
@endsection
{{-- Scripts Section --}}
@section('scripts')
    <script>
        $(document).ready(function() {

            fetchGroups();

            // Fetch all groups and show facility count
            function fetchGroups() {
                $.get("{{ route('facility-group.list') }}", function(data) {
                    // Clear select dropdown
                    $('#groupSelect').empty().append(
                        '<option value="" disabled selected>Select Group</option>');

                    // Clear table body
                    $('#groupTable tbody').empty();

                    data.forEach(function(group, index) {
                        // Update select dropdown
                        $('#groupSelect').append('<option value="' + group.id + '">' + group
                            .group_name + '</option>');

                        // Add row to table
                        $('#groupTable tbody').append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${group.group_name}</td>
                    <td>${group.facilities_count || 0}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm deleteGroup" data-id="${group.id}">Delete</button>
                    </td>
                </tr>
            `);
                    });
                });
            }


            // Add new group
            $('#addGroupBtn').click(function() {
                var groupName = $('#newGroupName').val();
                if (groupName.trim() === '') return alert('Enter group name');

                $.post("{{ route('facility-group.store') }}", {
                    _token: '{{ csrf_token() }}',
                    group_name: groupName
                }, function(data) {
                    $('#newGroupName').val('');
                    fetchGroups();
                    // Auto-select newly added group
                    $('#groupSelect').val(data.id);
                }).fail(function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error adding group');
                });
            });

            // Delete group

            $(document).on('click', '.deleteGroup', function(e) {
                e.preventDefault();
                if (!confirm('Are you sure to delete this group?')) return;

                var id = $(this).data('id');

                $.ajax({
                    url: '{{ route('facility-group.destroy', ':id') }}'.replace(':id', id),

                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        fetchGroups();
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.message || 'Error deleting group');
                    }
                });
            });

        });
    </script>
@endsection
