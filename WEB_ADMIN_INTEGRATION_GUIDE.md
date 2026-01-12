# Web Admin Panel Integration Guide - Broadcast Messaging

## Overview
This guide shows how to integrate the admin broadcast messaging system into your web admin panel. Admin can send messages to all mobile app users in real-time.

## Prerequisites
- Laravel backend with Reverb configured
- Admin authentication (session-based)
- jQuery or modern JavaScript framework

## Quick Start

### 1. Access the Broadcast Page
Navigate to: `http://localhost/admin/broadcast`

The page is already created and ready to use with full functionality!

## Integration Options

You can integrate the broadcast messaging in three ways:

### Option 1: Use the Existing Broadcast Page (Recommended)
The system includes a pre-built UI at `/admin/broadcast` with all features.

### Option 2: Add to Dashboard
Embed the broadcast form into your existing admin dashboard.

### Option 3: Custom Integration
Build your own UI using the API endpoints.

---

## Option 1: Using the Existing Broadcast Page

### Add to Admin Navigation

Update your admin sidebar/navigation to include a link:

#### For Blade Template (e.g., `resources/views/admin/layout/base/_aside.blade.php`):

```blade
<!-- Admin Messages Section -->
<li class="nav-item">
    <a href="{{ route('admin.broadcast.view') }}" class="nav-link">
        <i class="nav-icon fas fa-bullhorn"></i>
        <p>
            Broadcast Messages
            <span class="badge badge-info right">New</span>
        </p>
    </a>
</li>
```

### Features Available:
✅ Send broadcast messages
✅ View message history
✅ User statistics (total users, active users)
✅ Auto-refresh message list
✅ Beautiful responsive UI

---

## Option 2: Dashboard Integration

### Add Broadcast Widget to Dashboard

#### Create Partial View: `resources/views/admin/partials/broadcast_widget.blade.php`

```blade
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-bullhorn"></i> Quick Broadcast
        </h5>
    </div>
    <div class="card-body">
        <form id="quickBroadcastForm">
            @csrf
            <div class="form-group">
                <textarea 
                    id="quickMessageInput" 
                    class="form-control" 
                    rows="3" 
                    placeholder="Type your message to all users..."
                    required
                ></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-paper-plane"></i> Send to All Users
            </button>
        </form>
        
        <div id="quickBroadcastStatus" class="mt-3" style="display: none;"></div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#quickBroadcastForm').on('submit', function(e) {
        e.preventDefault();
        
        const message = $('#quickMessageInput').val().trim();
        if (!message) return;
        
        $.ajax({
            url: '/admin/broadcast-message',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { message: message },
            success: function(response) {
                $('#quickMessageInput').val('');
                $('#quickBroadcastStatus')
                    .removeClass('alert-danger')
                    .addClass('alert alert-success')
                    .text('✓ Message sent to all users successfully!')
                    .fadeIn()
                    .delay(3000)
                    .fadeOut();
            },
            error: function(xhr) {
                $('#quickBroadcastStatus')
                    .removeClass('alert-success')
                    .addClass('alert alert-danger')
                    .text('✗ Error: ' + (xhr.responseJSON?.message || 'Failed to send'))
                    .fadeIn();
            }
        });
    });
});
</script>
```

#### Include in Dashboard: `resources/views/admin/dashboard.blade.php`

```blade
@extends('admin.layout.default')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Existing Dashboard Cards -->
        <div class="col-lg-8">
            <!-- Your existing dashboard content -->
        </div>
        
        <!-- Broadcast Widget -->
        <div class="col-lg-4">
            @include('admin.partials.broadcast_widget')
        </div>
    </div>
</div>
@endsection
```

---

## Option 3: Custom Integration

### Using Vanilla JavaScript

```html
<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Broadcast</title>
</head>
<body>
    <div class="broadcast-container">
        <h2>Send Broadcast Message</h2>
        
        <form id="broadcastForm">
            <textarea id="messageInput" placeholder="Your message..." required></textarea>
            <button type="submit">Send to All Users</button>
        </form>
        
        <div id="status"></div>
        
        <h3>Recent Messages</h3>
        <div id="messagesList"></div>
    </div>

    <script>
        // Send Message
        document.getElementById('broadcastForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const message = document.getElementById('messageInput').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            try {
                const response = await fetch('/admin/broadcast-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ message })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('messageInput').value = '';
                    document.getElementById('status').innerHTML = 
                        '<div class="success">Message sent successfully!</div>';
                    loadMessages();
                }
            } catch (error) {
                document.getElementById('status').innerHTML = 
                    '<div class="error">Error sending message</div>';
            }
        });
        
        // Load Messages
        async function loadMessages() {
            try {
                const response = await fetch('/admin/admin-messages');
                const data = await response.json();
                
                if (data.success) {
                    displayMessages(data.messages.data);
                }
            } catch (error) {
                console.error('Error loading messages:', error);
            }
        }
        
        // Display Messages
        function displayMessages(messages) {
            const container = document.getElementById('messagesList');
            container.innerHTML = '';
            
            messages.reverse().forEach(msg => {
                const div = document.createElement('div');
                div.className = 'message-item';
                div.innerHTML = `
                    <strong>${msg.sender?.name || 'Admin'}</strong>
                    <span>${new Date(msg.created_at).toLocaleString()}</span>
                    <p>${msg.message}</p>
                `;
                container.appendChild(div);
            });
        }
        
        // Load on page load
        loadMessages();
        
        // Auto-refresh every 30 seconds
        setInterval(loadMessages, 30000);
    </script>
</body>
</html>
```

### Using jQuery/AJAX (AdminLTE Compatible)

```javascript
// Send Broadcast Message
function sendBroadcastMessage(message) {
    $.ajax({
        url: '/admin/broadcast-message',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: { message: message },
        beforeSend: function() {
            // Show loading spinner
            $('.btn-send').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        },
        success: function(response) {
            if (response.success) {
                // Show success notification
                toastr.success('Message broadcasted to all users!');
                
                // Clear input
                $('#messageInput').val('');
                
                // Reload messages
                loadBroadcastMessages();
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Failed to send message';
            toastr.error(errorMsg);
        },
        complete: function() {
            $('.btn-send').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send');
        }
    });
}

// Load Messages
function loadBroadcastMessages() {
    $.ajax({
        url: '/admin/admin-messages',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                displayMessages(response.messages.data);
                updateStats(response.messages.total);
            }
        },
        error: function() {
            console.error('Failed to load messages');
        }
    });
}

// Display Messages
function displayMessages(messages) {
    const container = $('#messagesList');
    container.empty();
    
    if (messages.length === 0) {
        container.html('<div class="empty-state">No messages yet</div>');
        return;
    }
    
    messages.reverse().forEach(function(msg) {
        const date = new Date(msg.created_at);
        const html = `
            <div class="message-item">
                <div class="d-flex justify-content-between">
                    <strong>${msg.sender?.name || 'Admin'}</strong>
                    <small class="text-muted">${date.toLocaleString()}</small>
                </div>
                <p class="mt-2">${msg.message}</p>
            </div>
        `;
        container.append(html);
    });
}

// Form Submit Handler
$(document).on('submit', '#broadcastForm', function(e) {
    e.preventDefault();
    const message = $('#messageInput').val().trim();
    
    if (message) {
        sendBroadcastMessage(message);
    }
});

// Initialize
$(document).ready(function() {
    loadBroadcastMessages();
    
    // Auto-refresh every 30 seconds
    setInterval(loadBroadcastMessages, 30000);
});
```

### Using Vue.js

```vue
<template>
  <div class="broadcast-panel">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h4>Broadcast Messages</h4>
      </div>
      
      <div class="card-body">
        <!-- Send Message Form -->
        <form @submit.prevent="sendMessage">
          <div class="form-group">
            <textarea
              v-model="newMessage"
              class="form-control"
              rows="3"
              placeholder="Type your message..."
              :disabled="sending"
              required
            ></textarea>
          </div>
          
          <button 
            type="submit" 
            class="btn btn-primary"
            :disabled="sending || !newMessage.trim()"
          >
            <span v-if="sending">
              <i class="fas fa-spinner fa-spin"></i> Sending...
            </span>
            <span v-else>
              <i class="fas fa-paper-plane"></i> Send to All Users
            </span>
          </button>
        </form>
        
        <!-- Status Message -->
        <div v-if="statusMessage" :class="statusClass" class="mt-3">
          {{ statusMessage }}
        </div>
        
        <!-- Messages List -->
        <div class="messages-list mt-4">
          <h5>Recent Messages</h5>
          
          <div v-if="loading" class="text-center py-4">
            <i class="fas fa-spinner fa-spin"></i> Loading...
          </div>
          
          <div v-else-if="messages.length === 0" class="text-center py-4 text-muted">
            No messages yet
          </div>
          
          <div v-else>
            <div 
              v-for="message in messages" 
              :key="message.id"
              class="message-item"
            >
              <div class="d-flex justify-content-between">
                <strong>{{ message.sender?.name || 'Admin' }}</strong>
                <small class="text-muted">{{ formatDate(message.created_at) }}</small>
              </div>
              <p class="mt-2">{{ message.message }}</p>
            </div>
          </div>
        </div>
        
        <!-- User Stats -->
        <div class="stats mt-4">
          <div class="row">
            <div class="col-md-4 text-center">
              <h3>{{ stats.totalUsers }}</h3>
              <small>Total Users</small>
            </div>
            <div class="col-md-4 text-center">
              <h3>{{ stats.activeUsers }}</h3>
              <small>Active Users</small>
            </div>
            <div class="col-md-4 text-center">
              <h3>{{ messages.length }}</h3>
              <small>Messages Sent</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BroadcastPanel',
  data() {
    return {
      newMessage: '',
      messages: [],
      stats: {
        totalUsers: 0,
        activeUsers: 0
      },
      sending: false,
      loading: false,
      statusMessage: '',
      statusClass: ''
    };
  },
  mounted() {
    this.loadMessages();
    this.loadUserStats();
    
    // Auto-refresh every 30 seconds
    this.refreshInterval = setInterval(() => {
      this.loadMessages();
    }, 30000);
  },
  beforeUnmount() {
    clearInterval(this.refreshInterval);
  },
  methods: {
    async sendMessage() {
      if (!this.newMessage.trim()) return;
      
      this.sending = true;
      this.statusMessage = '';
      
      try {
        const response = await fetch('/admin/broadcast-message', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            message: this.newMessage
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          this.statusMessage = '✓ Message sent to all users successfully!';
          this.statusClass = 'alert alert-success';
          this.newMessage = '';
          this.loadMessages();
          
          // Clear status after 3 seconds
          setTimeout(() => {
            this.statusMessage = '';
          }, 3000);
        }
      } catch (error) {
        this.statusMessage = '✗ Error sending message';
        this.statusClass = 'alert alert-danger';
      } finally {
        this.sending = false;
      }
    },
    
    async loadMessages() {
      this.loading = true;
      
      try {
        const response = await fetch('/admin/admin-messages');
        const data = await response.json();
        
        if (data.success) {
          this.messages = data.messages.data.reverse();
        }
      } catch (error) {
        console.error('Error loading messages:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async loadUserStats() {
      try {
        const response = await fetch('/admin/users-list');
        const data = await response.json();
        
        if (data.success) {
          this.stats.totalUsers = data.users.total || 0;
          this.stats.activeUsers = data.users.data?.filter(u => u.status == 1).length || 0;
        }
      } catch (error) {
        console.error('Error loading user stats:', error);
      }
    },
    
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString();
    }
  }
};
</script>

<style scoped>
.message-item {
  padding: 15px;
  border-bottom: 1px solid #eee;
  transition: background-color 0.2s;
}

.message-item:hover {
  background-color: #f8f9fa;
}

.messages-list {
  max-height: 500px;
  overflow-y: auto;
}

.stats h3 {
  color: #007bff;
  margin-bottom: 5px;
}
</style>
```

### Using React

```jsx
import React, { useState, useEffect } from 'react';
import axios from 'axios';

function BroadcastPanel() {
  const [newMessage, setNewMessage] = useState('');
  const [messages, setMessages] = useState([]);
  const [stats, setStats] = useState({ totalUsers: 0, activeUsers: 0 });
  const [sending, setSending] = useState(false);
  const [loading, setLoading] = useState(false);
  const [statusMessage, setStatusMessage] = useState('');
  const [statusType, setStatusType] = useState('');

  useEffect(() => {
    loadMessages();
    loadUserStats();
    
    // Auto-refresh every 30 seconds
    const interval = setInterval(loadMessages, 30000);
    return () => clearInterval(interval);
  }, []);

  const sendMessage = async (e) => {
    e.preventDefault();
    if (!newMessage.trim()) return;

    setSending(true);
    setStatusMessage('');

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      
      const response = await axios.post('/admin/broadcast-message', {
        message: newMessage
      }, {
        headers: {
          'X-CSRF-TOKEN': csrfToken
        }
      });

      if (response.data.success) {
        setStatusMessage('✓ Message sent to all users successfully!');
        setStatusType('success');
        setNewMessage('');
        loadMessages();
        
        setTimeout(() => setStatusMessage(''), 3000);
      }
    } catch (error) {
      setStatusMessage('✗ Error sending message');
      setStatusType('error');
    } finally {
      setSending(false);
    }
  };

  const loadMessages = async () => {
    setLoading(true);
    try {
      const response = await axios.get('/admin/admin-messages');
      if (response.data.success) {
        setMessages(response.data.messages.data.reverse());
      }
    } catch (error) {
      console.error('Error loading messages:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadUserStats = async () => {
    try {
      const response = await axios.get('/admin/users-list');
      if (response.data.success) {
        setStats({
          totalUsers: response.data.users.total || 0,
          activeUsers: response.data.users.data?.filter(u => u.status === 1).length || 0
        });
      }
    } catch (error) {
      console.error('Error loading user stats:', error);
    }
  };

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
  };

  return (
    <div className="broadcast-panel">
      <div className="card">
        <div className="card-header bg-primary text-white">
          <h4>Broadcast Messages</h4>
        </div>
        
        <div className="card-body">
          {/* Send Message Form */}
          <form onSubmit={sendMessage}>
            <div className="form-group">
              <textarea
                value={newMessage}
                onChange={(e) => setNewMessage(e.target.value)}
                className="form-control"
                rows="3"
                placeholder="Type your message..."
                disabled={sending}
                required
              />
            </div>
            
            <button 
              type="submit" 
              className="btn btn-primary"
              disabled={sending || !newMessage.trim()}
            >
              {sending ? (
                <>
                  <i className="fas fa-spinner fa-spin"></i> Sending...
                </>
              ) : (
                <>
                  <i className="fas fa-paper-plane"></i> Send to All Users
                </>
              )}
            </button>
          </form>
          
          {/* Status Message */}
          {statusMessage && (
            <div className={`alert alert-${statusType === 'success' ? 'success' : 'danger'} mt-3`}>
              {statusMessage}
            </div>
          )}
          
          {/* Messages List */}
          <div className="messages-list mt-4">
            <h5>Recent Messages</h5>
            
            {loading ? (
              <div className="text-center py-4">
                <i className="fas fa-spinner fa-spin"></i> Loading...
              </div>
            ) : messages.length === 0 ? (
              <div className="text-center py-4 text-muted">
                No messages yet
              </div>
            ) : (
              messages.map(message => (
                <div key={message.id} className="message-item">
                  <div className="d-flex justify-content-between">
                    <strong>{message.sender?.name || 'Admin'}</strong>
                    <small className="text-muted">{formatDate(message.created_at)}</small>
                  </div>
                  <p className="mt-2">{message.message}</p>
                </div>
              ))
            )}
          </div>
          
          {/* User Stats */}
          <div className="stats mt-4">
            <div className="row">
              <div className="col-md-4 text-center">
                <h3>{stats.totalUsers}</h3>
                <small>Total Users</small>
              </div>
              <div className="col-md-4 text-center">
                <h3>{stats.activeUsers}</h3>
                <small>Active Users</small>
              </div>
              <div className="col-md-4 text-center">
                <h3>{messages.length}</h3>
                <small>Messages Sent</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default BroadcastPanel;
```

---

## API Reference

### Send Broadcast Message
```
POST /admin/broadcast-message
```

**Headers:**
```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
```

**Body:**
```json
{
  "message": "Your announcement message here"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Message broadcasted successfully",
  "data": {
    "id": 123,
    "conversation_id": 1,
    "sender_id": 4,
    "message": "Your announcement message here",
    "created_at": "2026-01-11T10:30:00Z"
  }
}
```

### Get Admin Messages
```
GET /admin/admin-messages
```

**Response:**
```json
{
  "success": true,
  "conversation": {
    "id": 1,
    "type": "admin_broadcast",
    "name": "Admin Announcements"
  },
  "messages": {
    "data": [
      {
        "id": 123,
        "message": "Message content",
        "sender": {
          "id": 4,
          "name": "Admin",
          "email": "admin@example.com"
        },
        "created_at": "2026-01-11T10:30:00Z"
      }
    ],
    "total": 10
  }
}
```

### Get Users List
```
GET /admin/users-list
```

**Response:**
```json
{
  "success": true,
  "users": {
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "mobile": "9876543210",
        "status": 1,
        "created_at": "2026-01-01T00:00:00Z"
      }
    ],
    "total": 150
  }
}
```

---

## Styling

### Bootstrap CSS (Recommended)

```css
/* Broadcast Panel Styles */
.broadcast-panel .card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border: none;
}

.broadcast-panel .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.message-item {
    padding: 15px;
    border-bottom: 1px solid #eee;
    transition: all 0.3s ease;
}

.message-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.message-item:last-child {
    border-bottom: none;
}

.messages-list {
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 10px;
}

.stats h3 {
    color: #667eea;
    font-size: 2rem;
    margin: 0;
}

.stats small {
    color: #6c757d;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 1px;
}

/* Custom Scrollbar */
.messages-list::-webkit-scrollbar {
    width: 8px;
}

.messages-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.messages-list::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 10px;
}

.messages-list::-webkit-scrollbar-thumb:hover {
    background: #764ba2;
}
```

---

## Testing

### 1. Test from Browser Console

```javascript
// Send a test message
fetch('/admin/broadcast-message', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        message: 'Test broadcast message'
    })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
```

### 2. Test with Postman

**Request:**
```
POST http://localhost/admin/broadcast-message
Headers:
  Content-Type: application/json
  Cookie: laravel_session=YOUR_SESSION_COOKIE
Body:
{
  "message": "Test message from Postman"
}
```

### 3. Verify Real-time Broadcast

1. Open Flutter app on mobile/emulator
2. Send message from admin panel
3. Message should appear in Flutter app instantly!

---

## Advanced Features

### Add Rich Text Editor (TinyMCE)

```html
<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/tinymce.min.js"></script>

<script>
tinymce.init({
    selector: '#messageInput',
    height: 300,
    plugins: 'link lists',
    toolbar: 'bold italic | bullist numlist | link',
    menubar: false
});

// Modified send function
function sendMessage() {
    const message = tinymce.get('messageInput').getContent();
    // Send via AJAX...
}
</script>
```

### Add Message Templates

```javascript
const templates = [
    { title: 'Welcome', message: 'Welcome to our hotel booking app!' },
    { title: 'Offer Alert', message: 'New offers available! Check our latest deals.' },
    { title: 'Update', message: 'We have updated our app. Please update to latest version.' }
];

function loadTemplate(template) {
    $('#messageInput').val(template.message);
}
```

### Add Scheduled Messages

```javascript
function scheduleMessage(message, scheduledTime) {
    $.ajax({
        url: '/admin/schedule-message',
        method: 'POST',
        data: {
            message: message,
            scheduled_at: scheduledTime
        },
        success: function(response) {
            alert('Message scheduled successfully!');
        }
    });
}
```

### Add Message Preview

```javascript
function previewMessage() {
    const message = $('#messageInput').val();
    $('#previewModal .message-preview').html(message);
    $('#previewModal').modal('show');
}
```

---

## Troubleshooting

### CSRF Token Mismatch
**Solution:** Ensure CSRF token is included in meta tag:
```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Session Not Found (401 Error)
**Solution:** Check admin authentication middleware is working:
```php
Route::middleware(['CheckSession'])->group(function () {
    // routes...
});
```

### Messages Not Sending
**Solution:** Check Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

### Reverb Not Broadcasting
**Solution:** Verify Reverb server is running:
```bash
php artisan reverb:start
```

---

## Production Checklist

- [ ] Test message sending from admin panel
- [ ] Verify CSRF protection
- [ ] Test with multiple users
- [ ] Check message history loading
- [ ] Verify user statistics
- [ ] Test on different browsers
- [ ] Add rate limiting for message sending
- [ ] Implement message approval workflow (optional)
- [ ] Add admin audit logs
- [ ] Setup error monitoring (Sentry, etc.)

---

## Summary

Your admin panel now has:
✅ Complete broadcast messaging UI
✅ Send messages to all mobile users
✅ View message history
✅ User statistics dashboard
✅ Real-time message delivery
✅ Multiple integration options (Vanilla JS, jQuery, Vue, React)
✅ Beautiful responsive design
✅ Auto-refresh functionality

Admin can send announcements that instantly appear on all mobile app users' devices! 🚀
