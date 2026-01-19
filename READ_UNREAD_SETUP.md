# 🚀 Quick Setup Guide - Read/Unread Functionality

## Step 1: Run Migration
```bash
php artisan migrate
```

This creates the `message_reads` table.

## Step 2: Test API Endpoints

### Get all messages with read status
```bash
curl -X GET "http://localhost/api/all-chats" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Get unread count
```bash
curl -X GET "http://localhost/api/messages/unread-count" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Mark message as read
```bash
curl -X POST "http://localhost/api/messages/123/mark-read" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## Step 3: Update Your Frontend

Use the Flutter or JavaScript examples in [MESSAGE_READ_UNREAD_API.md](MESSAGE_READ_UNREAD_API.md)

## Available Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/all-chats` | All messages with read status |
| GET | `/api/messages/unread` | Unread messages only |
| GET | `/api/messages/unread-count` | Count of unread messages |
| POST | `/api/messages/{id}/mark-read` | Mark one message as read |
| POST | `/api/messages/mark-all-read` | Mark all as read |
| POST | `/api/messages/mark-multiple-read` | Mark multiple as read |

## Response Format

### All Messages Response
```json
{
    "status": true,
    "unread_count": 5,
    "messages": [
        {
            "id": 123,
            "description": "Message content",
            "city": "Mumbai",
            "is_read": 0,
            "read_at": null,
            "created_at": "2026-01-14T10:30:00Z"
        }
    ]
}
```

### Key Fields
- `is_read`: 0 = unread, 1 = read
- `read_at`: Timestamp when message was read (null if unread)
- `unread_count`: Total unread messages for the user

## Database Structure

The `message_reads` table tracks:
- Which user read which message
- When they read it
- Prevents duplicate reads (unique constraint)

## Testing

1. Create a test message from admin panel
2. Check unread count via API
3. Mark message as read
4. Verify count decreases

## Complete Documentation

See [MESSAGE_READ_UNREAD_API.md](MESSAGE_READ_UNREAD_API.md) for:
- Detailed API documentation
- Flutter integration examples
- JavaScript/React examples
- Performance tips
- Analytics queries

---

**Ready to use!** All endpoints are authenticated with JWT.
