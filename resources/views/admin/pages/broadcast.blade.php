<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Broadcast Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .message-item {
            border-bottom: 1px solid #eee;
            padding: 15px;
            transition: background-color 0.2s;
        }
        .message-item:hover {
            background-color: #f8f9fa;
        }
        .messages-container {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">📢 Admin Broadcast Messages</h4>
                    </div>
                    
                    <div class="card-body">
                        <!-- Send Message Form -->
                        <form id="broadcastForm">
                            <div class="mb-3">
                                <label class="form-label">Broadcast Message to All Users</label>
                                <textarea 
                                    id="messageInput" 
                                    class="form-control" 
                                    rows="3" 
                                    placeholder="Type your message here..."
                                    required
                                ></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                Send to All Users
                            </button>
                        </form>

                        <hr class="my-4">

                        <!-- Messages List -->
                        <h5>Recent Messages</h5>
                        <div class="messages-container" id="messagesList">
                            <div class="text-center text-muted py-4">
                                Loading messages...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card shadow mt-4">
                    <div class="card-body">
                        <h5>User Statistics</h5>
                        <div id="userStats" class="row">
                            <div class="col-md-4 text-center">
                                <h3 id="totalUsers">-</h3>
                                <small class="text-muted">Total Users</small>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 id="activeUsers">-</h3>
                                <small class="text-muted">Active Users</small>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 id="messagesSent">-</h3>
                                <small class="text-muted">Messages Sent</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Load messages on page load
            loadMessages();
            loadUserStats();

            // Send message
            $('#broadcastForm').on('submit', function(e) {
                e.preventDefault();
                
                const message = $('#messageInput').val().trim();
                if (!message) return;

                $.ajax({
                    url: '/admin/broadcast-message',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { message: message },
                    success: function(response) {
                        if (response.success) {
                            $('#messageInput').val('');
                            loadMessages();
                            
                            // Show success message
                            alert('Message sent to all users successfully!');
                        }
                    },
                    error: function(xhr) {
                        alert('Error sending message: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    }
                });
            });

            // Load messages
            function loadMessages() {
                $.ajax({
                    url: '/admin/admin-messages',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            displayMessages(response.messages.data);
                            $('#messagesSent').text(response.messages.total || 0);
                        }
                    },
                    error: function() {
                        $('#messagesList').html('<div class="text-danger">Error loading messages</div>');
                    }
                });
            }

            // Display messages
            function displayMessages(messages) {
                if (!messages || messages.length === 0) {
                    $('#messagesList').html('<div class="text-muted text-center py-4">No messages yet</div>');
                    return;
                }

                let html = '';
                messages.reverse().forEach(function(msg) {
                    const date = new Date(msg.created_at);
                    html += `
                        <div class="message-item">
                            <div class="d-flex justify-content-between">
                                <strong>${msg.sender?.name || 'Admin'}</strong>
                                <small class="text-muted">${date.toLocaleString()}</small>
                            </div>
                            <div class="mt-2">${msg.message}</div>
                        </div>
                    `;
                });
                
                $('#messagesList').html(html);
            }

            // Load user stats
            function loadUserStats() {
                $.ajax({
                    url: '/admin/users-list',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#totalUsers').text(response.users.total || 0);
                            $('#activeUsers').text(response.users.data?.filter(u => u.status == 1).length || 0);
                        }
                    }
                });
            }

            // Auto-refresh messages every 30 seconds
            setInterval(loadMessages, 30000);
        });
    </script>
</body>
</html>
