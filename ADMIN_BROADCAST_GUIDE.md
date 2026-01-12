# Admin Broadcast Chat Setup Guide

## Overview
This system allows admin to send broadcast messages to all users. When users sign up and go to their dashboard, they automatically see the admin conversation where they can receive messages from admin.

## Architecture

### Database Structure
- **conversations** table: Stores conversation with type `admin_broadcast`
- **messages** table: Stores all admin broadcast messages
- **conversation_id = 1**: Reserved for admin broadcast conversation

### Broadcasting
- **Channel**: `admin-broadcast` (public channel, no auth required)
- **Event**: `AdminBroadcastMessage`
- **Event Name**: `.admin-message`

## Implementation

### 1. Backend Files Created/Modified

#### New Files:
- `app/Events/AdminBroadcastMessage.php` - Event for broadcasting admin messages
- `app/Http/Controllers/Admin/AdminChatController.php` - Admin controller for sending broadcasts
- `resources/views/admin/pages/broadcast.blade.php` - Admin UI for sending messages

#### Modified Files:
- `routes/api.php` - Added admin messages endpoint for mobile app
- `routes/web.php` - Added admin broadcast routes
- `app/Http/Controllers/Api/ChatController.php` - Added getAdminConversation method
- `resources/js/echo.js` - Added global listener for admin broadcasts

### 2. API Endpoints

#### For Mobile App (API Routes - JWT Auth Required):
```
GET /api/admin-messages
```
Returns admin conversation and all messages.

Response:
```json
{
  "status": true,
  "conversation": {
    "id": 1,
    "type": "admin_broadcast",
    "name": "Admin Announcements",
    "is_admin_conversation": true
  },
  "messages": {
    "data": [
      {
        "id": 1,
        "message": "Welcome to our app!",
        "sender": {
          "id": 1,
          "name": "Admin",
          "profile_pic": null
        },
        "created_at": "2024-01-11T10:00:00Z"
      }
    ]
  }
}
```

#### For Admin Panel (Web Routes - Session Auth Required):
```
POST /admin/broadcast-message
Body: { "message": "Your message here" }
```
Sends broadcast message to all users.

```
GET /admin/admin-messages
```
Gets all admin broadcast messages.

```
GET /admin/users-list
```
Gets list of all users.

```
GET /admin/broadcast
```
View admin broadcast page.

## Usage

### For Admin Panel

1. **Access Broadcast Page:**
   Navigate to: `http://localhost/admin/broadcast`

2. **Send Message:**
   ```javascript
   // Via form on the page, or via API:
   fetch('/admin/broadcast-message', {
       method: 'POST',
       headers: {
           'Content-Type': 'application/json',
           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
       },
       body: JSON.stringify({
           message: 'Hello all users!'
       })
   });
   ```

3. **View All Messages:**
   The broadcast page automatically loads and displays all sent messages.

### For Mobile App

#### 1. Load Admin Conversation on Dashboard

```javascript
// When user logs in and goes to dashboard
async function loadAdminConversation() {
    try {
        const response = await fetch('/api/admin-messages', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
            }
        });
        
        const data = await response.json();
        
        if (data.status) {
            // Display conversation
            displayConversation(data.conversation);
            displayMessages(data.messages.data);
        }
    } catch (error) {
        console.error('Error loading admin conversation:', error);
    }
}

// Call on dashboard mount
loadAdminConversation();
```

#### 2. Listen to Real-time Admin Messages

The Echo.js file automatically listens to admin broadcasts. In your mobile app component:

```javascript
// Listen for admin messages
window.addEventListener('admin-message', (event) => {
    const data = event.detail;
    console.log('New admin message:', data);
    
    // Add message to UI
    addMessageToUI({
        id: data.message_id,
        message: data.message,
        sender: {
            id: data.admin_id,
            name: data.admin_name
        },
        timestamp: data.timestamp
    });
    
    // Show notification
    showNotification('Admin Message', data.message);
});
```

#### 3. Complete React Component Example

```jsx
import { useState, useEffect } from 'react';
import axios from 'axios';

function AdminConversation() {
    const [messages, setMessages] = useState([]);
    const [conversation, setConversation] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadAdminConversation();
        
        // Listen for real-time messages
        const handleAdminMessage = (event) => {
            const data = event.detail;
            setMessages(prev => [...prev, {
                id: data.message_id,
                message: data.message,
                sender: {
                    id: data.admin_id,
                    name: data.admin_name
                },
                created_at: data.timestamp
            }]);
        };
        
        window.addEventListener('admin-message', handleAdminMessage);
        
        return () => {
            window.removeEventListener('admin-message', handleAdminMessage);
        };
    }, []);

    const loadAdminConversation = async () => {
        try {
            const response = await axios.get('/api/admin-messages');
            if (response.data.status) {
                setConversation(response.data.conversation);
                setMessages(response.data.messages.data);
            }
        } catch (error) {
            console.error('Error loading admin conversation:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return <div>Loading admin messages...</div>;
    }

    return (
        <div className="admin-conversation">
            <div className="conversation-header">
                <h3>📢 {conversation?.name || 'Admin Announcements'}</h3>
                <span className="badge">Official</span>
            </div>
            
            <div className="messages-list">
                {messages.map((msg) => (
                    <div key={msg.id} className="message-item admin-message">
                        <div className="message-header">
                            <strong>{msg.sender?.name || 'Admin'}</strong>
                            <small>{new Date(msg.created_at).toLocaleString()}</small>
                        </div>
                        <div className="message-body">
                            {msg.message}
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}

export default AdminConversation;
```

#### 4. Complete Vue 3 Component Example

```vue
<template>
  <div class="admin-conversation">
    <div class="conversation-header">
      <h3>📢 {{ conversation?.name || 'Admin Announcements' }}</h3>
      <span class="badge">Official</span>
    </div>
    
    <div v-if="loading" class="loading">Loading admin messages...</div>
    
    <div v-else class="messages-list">
      <div 
        v-for="msg in messages" 
        :key="msg.id" 
        class="message-item admin-message"
      >
        <div class="message-header">
          <strong>{{ msg.sender?.name || 'Admin' }}</strong>
          <small>{{ formatDate(msg.created_at) }}</small>
        </div>
        <div class="message-body">
          {{ msg.message }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const messages = ref([]);
const conversation = ref(null);
const loading = ref(true);

onMounted(() => {
  loadAdminConversation();
  
  // Listen for real-time messages
  window.addEventListener('admin-message', handleAdminMessage);
});

onUnmounted(() => {
  window.removeEventListener('admin-message', handleAdminMessage);
});

const loadAdminConversation = async () => {
  try {
    const response = await axios.get('/api/admin-messages');
    if (response.data.status) {
      conversation.value = response.data.conversation;
      messages.value = response.data.messages.data;
    }
  } catch (error) {
    console.error('Error loading admin conversation:', error);
  } finally {
    loading.value = false;
  }
};

const handleAdminMessage = (event) => {
  const data = event.detail;
  messages.value.push({
    id: data.message_id,
    message: data.message,
    sender: {
      id: data.admin_id,
      name: data.admin_name
    },
    created_at: data.timestamp
  });
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString();
};
</script>

<style scoped>
.admin-conversation {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 10px 10px 0 0;
}

.badge {
  background: rgba(255,255,255,0.3);
  padding: 5px 10px;
  border-radius: 15px;
  font-size: 12px;
}

.messages-list {
  background: white;
  padding: 20px;
  border-radius: 0 0 10px 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  max-height: 600px;
  overflow-y: auto;
}

.message-item {
  padding: 15px;
  margin-bottom: 10px;
  background: #f8f9fa;
  border-radius: 8px;
  border-left: 4px solid #667eea;
}

.message-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  font-size: 14px;
}

.message-body {
  color: #333;
  line-height: 1.5;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #666;
}
</style>
```

## Database Migration

Run this to create the admin conversation:

```php
// In tinker or a seeder
use App\Models\Conversation;

Conversation::firstOrCreate(
    ['type' => 'admin_broadcast'],
    [
        'name' => 'Admin Announcements',
        'created_by' => 1 // Your admin user ID
    ]
);
```

Or via SQL:
```sql
INSERT INTO conversations (id, type, name, created_by, created_at, updated_at)
VALUES (1, 'admin_broadcast', 'Admin Announcements', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE type = 'admin_broadcast';
```

## Testing

### 1. Start Reverb Server
```bash
php artisan reverb:start
```

### 2. Test Admin Broadcast

**Via Admin Panel:**
1. Go to `http://localhost/admin/broadcast`
2. Type a message
3. Click "Send to All Users"

**Via API (Postman/cURL):**
```bash
curl -X POST http://localhost/admin/broadcast-message \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: YOUR_TOKEN" \
  -d '{"message": "Hello everyone!"}'
```

### 3. Test Mobile App Reception

Open browser console in your mobile app and check:
```javascript
// Should see in console:
"Admin broadcast received: {message: 'Hello everyone!', ...}"
```

## Features

✅ **Auto-created admin conversation** for all users
✅ **Real-time broadcasting** to all connected users
✅ **No authentication required** for listening (public channel)
✅ **Message history** - users can see past admin messages
✅ **Admin panel UI** for sending messages
✅ **User statistics** in admin panel
✅ **Notification support** for incoming admin messages
✅ **Mobile app ready** with React/Vue examples

## Important Notes

1. **Admin Conversation is Global**: All users see the same admin conversation
2. **One-way Communication**: Users can only receive, not send messages to admin conversation
3. **Public Channel**: Uses public broadcast channel, no per-user authorization needed
4. **Reverb Server**: Must be running for real-time functionality
5. **Message Persistence**: All messages are stored in database

## Troubleshooting

### Messages not appearing in mobile app:
- Check if JWT token is stored: `localStorage.getItem('auth_token')`
- Check browser console for errors
- Verify Reverb server is running
- Check if Echo is loaded: `console.log(window.Echo)`

### Admin broadcast not working:
- Verify CSRF token is correct
- Check admin session authentication
- Review Laravel logs: `storage/logs/laravel.log`
- Test API endpoint directly via Postman

### Database errors:
- Run migrations: `php artisan migrate`
- Create admin conversation manually (see Database Migration section)
- Check conversation_id in messages table

## Next Steps

1. Add push notifications for mobile apps
2. Add message read receipts
3. Add admin message categories (announcements, updates, alerts)
4. Add scheduled messages
5. Add message templates for admin
6. Add rich media support (images, videos)
