# 📬 Message Read/Unread Functionality - API Documentation

## Overview
This API provides comprehensive read/unread tracking for broadcast messages. Users can see which messages they've read, mark messages as read individually or in bulk, and get unread counts.

---

## 🗄️ Database Structure

### New Table: `message_reads`
```sql
CREATE TABLE message_reads (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    message_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY (message_id, user_id),
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Note:** Each user can only mark a message as read once (enforced by unique constraint).

---

## 📡 API Endpoints

### 1. Get All Messages (with read status)
```http
GET /api/all-chats
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "unread_count": 5,
    "messages": [
        {
            "id": 123,
            "description": "Special offer for Mumbai",
            "city": "Mumbai",
            "calling_number": "1800-123-456",
            "media": "messages/image.jpg",
            "youtube_link": null,
            "website_link": "https://...",
            "is_read": 0,
            "read_at": null,
            "created_at": "2026-01-14T10:30:00Z"
        },
        {
            "id": 122,
            "description": "Important update",
            "city": "all",
            "is_read": 1,
            "read_at": "2026-01-14T09:15:00Z",
            "created_at": "2026-01-13T15:00:00Z"
        }
    ]
}
```

**Features:**
- ✅ Returns messages for user's city + messages sent to "all" cities
- ✅ Includes `is_read` flag (0 = unread, 1 = read)
- ✅ Includes `read_at` timestamp when message was read
- ✅ Returns total `unread_count`
- ✅ Ordered by creation date (newest first)

---

### 2. Get Unread Messages Only
```http
GET /api/messages/unread
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "count": 5,
    "messages": [
        {
            "id": 125,
            "description": "New promotion",
            "city": "Mumbai",
            "created_at": "2026-01-14T12:00:00Z"
        },
        {
            "id": 124,
            "description": "System maintenance",
            "city": "all",
            "created_at": "2026-01-14T11:00:00Z"
        }
    ]
}
```

**Use Case:** Display unread messages badge, notification list

---

### 3. Get Unread Count
```http
GET /api/messages/unread-count
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "unread_count": 5
}
```

**Use Case:** Display notification badge number, real-time count updates

---

### 4. Mark Single Message as Read
```http
POST /api/messages/{messageId}/mark-read
Authorization: Bearer {token}
```

**Example:**
```http
POST /api/messages/123/mark-read
```

**Response:**
```json
{
    "status": true,
    "message": "Message marked as read"
}
```

**Use Case:** User opens/views a specific message

---

### 5. Mark All Messages as Read
```http
POST /api/messages/mark-all-read
Authorization: Bearer {token}
```

**Response:**
```json
{
    "status": true,
    "message": "All messages marked as read",
    "marked_count": 5
}
```

**Use Case:** "Mark all as read" button in notification panel

---

### 6. Mark Multiple Messages as Read (Bulk)
```http
POST /api/messages/mark-multiple-read
Authorization: Bearer {token}
Content-Type: application/json

{
    "message_ids": [123, 124, 125]
}
```

**Response:**
```json
{
    "status": true,
    "message": "Messages marked as read",
    "marked_count": 3
}
```

**Use Case:** User swipes to mark multiple messages as read

---

## 🎨 Frontend Integration Examples

### Flutter Example

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class MessageService {
  final String baseUrl = 'https://your-api.com/api';
  final String token;

  MessageService(this.token);

  // Get all messages with read status
  Future<Map<String, dynamic>> getAllMessages() async {
    final response = await http.get(
      Uri.parse('$baseUrl/all-chats'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      return json.decode(response.body);
    }
    throw Exception('Failed to load messages');
  }

  // Get unread count
  Future<int> getUnreadCount() async {
    final response = await http.get(
      Uri.parse('$baseUrl/messages/unread-count'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      return data['unread_count'] ?? 0;
    }
    return 0;
  }

  // Mark message as read
  Future<void> markAsRead(int messageId) async {
    await http.post(
      Uri.parse('$baseUrl/messages/$messageId/mark-read'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
  }

  // Mark all as read
  Future<void> markAllAsRead() async {
    await http.post(
      Uri.parse('$baseUrl/messages/mark-all-read'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
  }

  // Mark multiple as read
  Future<void> markMultipleAsRead(List<int> messageIds) async {
    await http.post(
      Uri.parse('$baseUrl/messages/mark-multiple-read'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: json.encode({'message_ids': messageIds}),
    );
  }
}
```

### Flutter Widget Example

```dart
class MessageListScreen extends StatefulWidget {
  @override
  _MessageListScreenState createState() => _MessageListScreenState();
}

class _MessageListScreenState extends State<MessageListScreen> {
  final MessageService _service = MessageService(authToken);
  List<dynamic> messages = [];
  int unreadCount = 0;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadMessages();
  }

  Future<void> _loadMessages() async {
    try {
      final data = await _service.getAllMessages();
      setState(() {
        messages = data['messages'];
        unreadCount = data['unread_count'];
        isLoading = false;
      });
    } catch (e) {
      print('Error loading messages: $e');
      setState(() => isLoading = false);
    }
  }

  Future<void> _markAsRead(int messageId) async {
    await _service.markAsRead(messageId);
    _loadMessages(); // Refresh list
  }

  Future<void> _markAllAsRead() async {
    await _service.markAllAsRead();
    _loadMessages(); // Refresh list
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Messages'),
        actions: [
          if (unreadCount > 0)
            IconButton(
              icon: Badge(
                label: Text('$unreadCount'),
                child: Icon(Icons.notifications),
              ),
              onPressed: _markAllAsRead,
            ),
        ],
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator())
          : ListView.builder(
              itemCount: messages.length,
              itemBuilder: (context, index) {
                final message = messages[index];
                final isRead = message['is_read'] == 1;

                return Card(
                  color: isRead ? Colors.white : Colors.blue[50],
                  child: ListTile(
                    leading: Icon(
                      isRead ? Icons.mark_email_read : Icons.mark_email_unread,
                      color: isRead ? Colors.grey : Colors.blue,
                    ),
                    title: Text(
                      message['description'],
                      style: TextStyle(
                        fontWeight: isRead ? FontWeight.normal : FontWeight.bold,
                      ),
                    ),
                    subtitle: Text(
                      'City: ${message['city']} • ${_formatDate(message['created_at'])}',
                    ),
                    onTap: () {
                      if (!isRead) {
                        _markAsRead(message['id']);
                      }
                      _showMessageDetails(message);
                    },
                  ),
                );
              },
            ),
    );
  }

  String _formatDate(String dateStr) {
    final date = DateTime.parse(dateStr);
    final now = DateTime.now();
    final diff = now.difference(date);

    if (diff.inHours < 1) {
      return '${diff.inMinutes}m ago';
    } else if (diff.inDays < 1) {
      return '${diff.inHours}h ago';
    } else {
      return '${diff.inDays}d ago';
    }
  }

  void _showMessageDetails(Map<String, dynamic> message) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Message Details'),
        content: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              if (message['media'] != null)
                Image.network('https://your-server.com/storage/${message['media']}'),
              SizedBox(height: 16),
              Text(message['description']),
              SizedBox(height: 16),
              if (message['calling_number'] != null)
                ElevatedButton.icon(
                  icon: Icon(Icons.phone),
                  label: Text('Call ${message['calling_number']}'),
                  onPressed: () {
                    // Launch phone dialer
                  },
                ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('Close'),
          ),
        ],
      ),
    );
  }
}
```

### JavaScript/React Example

```javascript
// API Service
class MessageAPI {
  constructor(token) {
    this.baseUrl = 'https://your-api.com/api';
    this.token = token;
  }

  async getAllMessages() {
    const response = await fetch(`${this.baseUrl}/all-chats`, {
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json'
      }
    });
    return response.json();
  }

  async getUnreadCount() {
    const response = await fetch(`${this.baseUrl}/messages/unread-count`, {
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json'
      }
    });
    const data = await response.json();
    return data.unread_count;
  }

  async markAsRead(messageId) {
    return fetch(`${this.baseUrl}/messages/${messageId}/mark-read`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json'
      }
    });
  }

  async markAllAsRead() {
    return fetch(`${this.baseUrl}/messages/mark-all-read`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json'
      }
    });
  }
}

// React Component
function MessageList() {
  const [messages, setMessages] = useState([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const api = new MessageAPI(authToken);

  useEffect(() => {
    loadMessages();
  }, []);

  const loadMessages = async () => {
    const data = await api.getAllMessages();
    setMessages(data.messages);
    setUnreadCount(data.unread_count);
  };

  const handleMarkAsRead = async (messageId) => {
    await api.markAsRead(messageId);
    loadMessages();
  };

  const handleMarkAllAsRead = async () => {
    await api.markAllAsRead();
    loadMessages();
  };

  return (
    <div>
      <div className="header">
        <h2>Messages</h2>
        {unreadCount > 0 && (
          <button onClick={handleMarkAllAsRead}>
            Mark All as Read ({unreadCount})
          </button>
        )}
      </div>
      
      <div className="message-list">
        {messages.map(message => (
          <div 
            key={message.id}
            className={message.is_read ? 'message read' : 'message unread'}
            onClick={() => handleMarkAsRead(message.id)}
          >
            <div className="message-icon">
              {message.is_read ? '📭' : '📬'}
            </div>
            <div className="message-content">
              <h4>{message.description}</h4>
              <p>City: {message.city}</p>
              <small>{new Date(message.created_at).toLocaleString()}</small>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
```

---

## 🧪 Testing

### Test with cURL

```bash
# Get all messages
curl -X GET "http://localhost/api/all-chats" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get unread count
curl -X GET "http://localhost/api/messages/unread-count" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Mark message as read
curl -X POST "http://localhost/api/messages/123/mark-read" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Mark all as read
curl -X POST "http://localhost/api/messages/mark-all-read" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Mark multiple as read
curl -X POST "http://localhost/api/messages/mark-multiple-read" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"message_ids": [123, 124, 125]}'
```

---

## 🚀 Migration

Run the migration to create the `message_reads` table:

```bash
php artisan migrate
```

This creates the tracking table with proper foreign keys and indexes.

---

## 📊 Performance Considerations

### Indexes
- ✅ `message_id` index for fast message lookups
- ✅ `user_id` index for user-specific queries
- ✅ `(user_id, read_at)` composite index for unread queries
- ✅ `(message_id, user_id)` unique constraint prevents duplicates

### Query Optimization
- Uses LEFT JOIN for efficient read status checking
- Bulk insert for marking multiple messages
- Indexed columns for fast filtering

---

## 🔐 Security

- ✅ JWT authentication required on all endpoints
- ✅ Users can only mark their own messages as read
- ✅ Users only see messages for their city
- ✅ Foreign key constraints maintain data integrity

---

## 📈 Analytics Potential

You can track:
- Read rates by city
- Average time to read
- Most read messages
- User engagement patterns

Example query:
```sql
SELECT 
    m.city,
    COUNT(DISTINCT mr.user_id) as readers,
    COUNT(*) as total_reads,
    AVG(TIMESTAMPDIFF(MINUTE, m.created_at, mr.read_at)) as avg_minutes_to_read
FROM messages m
LEFT JOIN message_reads mr ON m.id = mr.message_id
GROUP BY m.city;
```

---

## ✅ Summary

**What You Can Do:**
- ✅ Track which messages each user has read
- ✅ Display unread count badges
- ✅ Mark messages as read individually or in bulk
- ✅ Filter to show only unread messages
- ✅ Show read timestamps
- ✅ Prevent duplicate read records

**API Endpoints:**
- GET `/api/all-chats` - All messages with read status
- GET `/api/messages/unread` - Unread messages only
- GET `/api/messages/unread-count` - Unread count
- POST `/api/messages/{id}/mark-read` - Mark one as read
- POST `/api/messages/mark-all-read` - Mark all as read
- POST `/api/messages/mark-multiple-read` - Bulk mark as read

**Database:**
- New `message_reads` table with proper indexes
- Foreign key constraints for data integrity
- Unique constraint prevents duplicates

---

**Ready to Use!** 🎉
