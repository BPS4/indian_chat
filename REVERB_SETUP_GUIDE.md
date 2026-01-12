# Laravel Reverb Chat Setup Guide

## Changes Made

### 1. Event Classes Fixed
All event classes now use `PrivateChannel` instead of `Channel` for proper authentication:
- `MessageSent.php` - broadcasts on `private-chat.{chatId}` with event name `message-sent`
- `TypingStatus.php` - broadcasts on `private-chat.{chatId}` with event name `typing`
- `UserOnline.php` - broadcasts on `private-user.{userId}` with event name `user-online`
- `UserOffline.php` - broadcasts on `private-user.{userId}` with event name `user-offline`
- `MessageDelivered.php` - broadcasts on `private-user.{receiverId}` with event name `message-delivered`
- `MessageSeen.php` - broadcasts on `private-user.{receiverId}` with event name `message-seen`

### 2. ChatController Updated
- Fixed `MessageSent` case sensitivity issue
- Enabled broadcasting in `sendMessage` method using `broadcast()->toOthers()`
- Messages now broadcast to all users in the conversation except the sender

### 3. Frontend Echo Configuration
Updated `resources/js/echo.js` with:
- JWT token authorization for private channels
- Custom authorizer that sends JWT token to `/api/broadcasting/auth` endpoint

### 4. Chatroom View Updated
- Changed from public channel to private channel
- Properly listen to `.message-sent` event with leading dot
- Uses chat ID for private channel subscription

## Setup Instructions

### Step 1: Start Reverb Server
```bash
php artisan reverb:start
```

Or run in background on Windows:
```bash
Start-Process powershell -ArgumentList "-NoExit", "-Command", "php artisan reverb:start"
```

### Step 2: Run Queue Worker (if using queued broadcasts)
```bash
php artisan queue:work
```

### Step 3: Build Frontend Assets
```bash
npm run build
# or for development with hot reload
npm run dev
```

### Step 4: Test the Chat

#### Using API (Recommended)

1. **Start a Private Chat:**
```javascript
POST /api/start-private-chat
Headers: {
  "Authorization": "Bearer YOUR_JWT_TOKEN"
}
Body: {
  "receiver_id": 2
}
```

2. **Send a Message:**
```javascript
POST /api/send-message
Headers: {
  "Authorization": "Bearer YOUR_JWT_TOKEN"
}
Body: {
  "chat_id": 1,
  "message": "Hello from Reverb!"
}
```

3. **Listen to Messages (Frontend):**
```javascript
// Store JWT token
localStorage.setItem('auth_token', 'YOUR_JWT_TOKEN');

// Subscribe to private chat channel
Echo.private(`chat.${chatId}`)
    .listen('.message-sent', (data) => {
        console.log('New message:', data);
        // Update UI with new message
    });

// Listen to typing indicator
Echo.private(`chat.${chatId}`)
    .listen('.typing', (data) => {
        console.log('User typing:', data);
        // Show typing indicator
    });
```

4. **Send Typing Status:**
```javascript
POST /api/typing
Headers: {
  "Authorization": "Bearer YOUR_JWT_TOKEN"
}
Body: {
  "chat_id": 1,
  "is_typing": true
}
```

## Frontend Example (Vue/React Component)

### Vue 3 Example:
```vue
<template>
  <div class="chat-container">
    <div ref="chatBox" class="messages">
      <div 
        v-for="msg in messages" 
        :key="msg.id"
        :class="msg.sender_id === currentUserId ? 'message-right' : 'message-left'"
      >
        <strong>{{ msg.sender.name }}:</strong> {{ msg.message }}
      </div>
    </div>
    
    <div v-if="isTyping" class="typing-indicator">
      Someone is typing...
    </div>
    
    <form @submit.prevent="sendMessage">
      <input 
        v-model="newMessage" 
        @input="handleTyping"
        placeholder="Type a message..."
      >
      <button type="submit">Send</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps(['chatId', 'currentUserId']);

const messages = ref([]);
const newMessage = ref('');
const isTyping = ref(false);
let typingTimer = null;

onMounted(() => {
  loadMessages();
  subscribeToChannel();
});

const loadMessages = async () => {
  const response = await axios.get(`/api/conversations/${props.chatId}/messages`);
  messages.value = response.data.messages.data;
};

const subscribeToChannel = () => {
  Echo.private(`chat.${props.chatId}`)
    .listen('.message-sent', (data) => {
      if (data.sender_id !== props.currentUserId) {
        messages.value.push(data.message);
      }
    })
    .listen('.typing', (data) => {
      if (data.userId !== props.currentUserId) {
        isTyping.value = data.isTyping;
      }
    });
};

const sendMessage = async () => {
  if (!newMessage.value.trim()) return;
  
  try {
    const response = await axios.post('/api/send-message', {
      chat_id: props.chatId,
      message: newMessage.value
    });
    
    messages.value.push(response.data.data);
    newMessage.value = '';
    
    // Send typing stopped
    await axios.post('/api/typing', {
      chat_id: props.chatId,
      is_typing: false
    });
  } catch (error) {
    console.error('Error sending message:', error);
  }
};

const handleTyping = () => {
  clearTimeout(typingTimer);
  
  axios.post('/api/typing', {
    chat_id: props.chatId,
    is_typing: true
  });
  
  typingTimer = setTimeout(() => {
    axios.post('/api/typing', {
      chat_id: props.chatId,
      is_typing: false
    });
  }, 1000);
};

onUnmounted(() => {
  Echo.leave(`chat.${props.chatId}`);
});
</script>
```

### React Example:
```jsx
import { useState, useEffect, useRef } from 'react';
import axios from 'axios';

function ChatRoom({ chatId, currentUserId }) {
  const [messages, setMessages] = useState([]);
  const [newMessage, setNewMessage] = useState('');
  const [isTyping, setIsTyping] = useState(false);
  const typingTimer = useRef(null);
  
  useEffect(() => {
    loadMessages();
    subscribeToChannel();
    
    return () => {
      Echo.leave(`chat.${chatId}`);
    };
  }, [chatId]);
  
  const loadMessages = async () => {
    const response = await axios.get(`/api/conversations/${chatId}/messages`);
    setMessages(response.data.messages.data);
  };
  
  const subscribeToChannel = () => {
    Echo.private(`chat.${chatId}`)
      .listen('.message-sent', (data) => {
        if (data.sender_id !== currentUserId) {
          setMessages(prev => [...prev, data.message]);
        }
      })
      .listen('.typing', (data) => {
        if (data.userId !== currentUserId) {
          setIsTyping(data.isTyping);
        }
      });
  };
  
  const sendMessage = async (e) => {
    e.preventDefault();
    if (!newMessage.trim()) return;
    
    try {
      const response = await axios.post('/api/send-message', {
        chat_id: chatId,
        message: newMessage
      });
      
      setMessages(prev => [...prev, response.data.data]);
      setNewMessage('');
      
      await axios.post('/api/typing', {
        chat_id: chatId,
        is_typing: false
      });
    } catch (error) {
      console.error('Error sending message:', error);
    }
  };
  
  const handleTyping = () => {
    clearTimeout(typingTimer.current);
    
    axios.post('/api/typing', {
      chat_id: chatId,
      is_typing: true
    });
    
    typingTimer.current = setTimeout(() => {
      axios.post('/api/typing', {
        chat_id: chatId,
        is_typing: false
      });
    }, 1000);
  };
  
  return (
    <div className="chat-container">
      <div className="messages">
        {messages.map((msg) => (
          <div 
            key={msg.id}
            className={msg.sender_id === currentUserId ? 'message-right' : 'message-left'}
          >
            <strong>{msg.sender.name}:</strong> {msg.message}
          </div>
        ))}
      </div>
      
      {isTyping && <div className="typing-indicator">Someone is typing...</div>}
      
      <form onSubmit={sendMessage}>
        <input 
          value={newMessage}
          onChange={(e) => {
            setNewMessage(e.target.value);
            handleTyping();
          }}
          placeholder="Type a message..."
        />
        <button type="submit">Send</button>
      </form>
    </div>
  );
}

export default ChatRoom;
```

## Important Notes

1. **JWT Authentication**: Make sure your JWT token is stored and sent with every request:
   ```javascript
   localStorage.setItem('auth_token', 'YOUR_JWT_TOKEN');
   ```

2. **Channel Authorization**: The `/api/broadcasting/auth` endpoint must be accessible and should validate the JWT token.

3. **Environment Variables**: Ensure your `.env` file has correct Reverb configuration:
   ```
   BROADCAST_CONNECTION=reverb
   REVERB_APP_ID=562045
   REVERB_APP_KEY=bqefva00jou7erqcx7ob
   REVERB_APP_SECRET=6xcubaqz010xxzzju1el
   REVERB_HOST=localhost
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```

4. **Reverb Server**: Must be running on `http://localhost:8080` for WebSocket connections.

5. **Private vs Public Channels**:
   - Use `Echo.private()` for authenticated channels (requires authorization)
   - Use `Echo.channel()` for public channels (no authentication)

6. **Event Names**: When listening to events, prefix with a dot: `.message-sent` not `message-sent`

## Troubleshooting

### Messages not appearing:
- Check browser console for errors
- Verify Reverb server is running: `php artisan reverb:start`
- Check JWT token is valid and being sent
- Verify channel authorization in [routes/channels.php](routes/channels.php)

### "Unable to connect to Reverb":
- Check `REVERB_HOST` and `REVERB_PORT` in `.env`
- Ensure no firewall blocking port 8080
- Verify Reverb server is running

### Authentication fails:
- Check JWT middleware in [routes/channels.php](routes/channels.php)
- Verify token is being sent in Authorization header
- Check token expiry (JWT_TTL in .env)

### Events not firing:
- Verify event implements `ShouldBroadcastNow` interface
- Check event is being dispatched in controller
- Review Laravel logs for broadcasting errors
