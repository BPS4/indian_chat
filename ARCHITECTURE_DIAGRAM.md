# 🎯 City-Wise Broadcasting Architecture

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         ADMIN PANEL                             │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Select City:  [ Mumbai ▼ ]  or  [ All Cities ▼ ]       │  │
│  │                                                           │  │
│  │  Message: [...................................]           │  │
│  │                                                           │  │
│  │  Contact: [1800-123-456]                                 │  │
│  │                                                           │  │
│  │  Media:   [Upload file]                                  │  │
│  │                                                           │  │
│  │  Preview: 📊 This will send to 1,234 users in Mumbai    │  │
│  │                                                           │  │
│  │  [Send Broadcast]                                        │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                   AdminChatController.php                       │
│                                                                 │
│  1. Validate input                                             │
│  2. Get admin details (session/auth)                           │
│  3. Save message to database                                   │
│  4. Determine target city                                      │
│  5. Count affected users                                       │
│  6. Broadcast to channel(s)                                    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                  AdminBroadcastMessage.php                      │
│                         (Event)                                 │
│                                                                 │
│  IF city == "Mumbai":                                          │
│     └─► Channel: admin-broadcast.mumbai                        │
│                                                                 │
│  IF city == "all":                                             │
│     ├─► Channel: admin-broadcast.mumbai                        │
│     ├─► Channel: admin-broadcast.delhi                         │
│     ├─► Channel: admin-broadcast.bangalore                     │
│     └─► Channel: admin-broadcast.{all_other_cities}            │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     Laravel Reverb Server                       │
│                   (WebSocket Broadcasting)                      │
│                                                                 │
│  Listening on: ws://your-server:8080                           │
│                                                                 │
│  Active Channels:                                              │
│  ├─ admin-broadcast.mumbai     [245 subscribers]              │
│  ├─ admin-broadcast.delhi      [189 subscribers]              │
│  ├─ admin-broadcast.bangalore  [156 subscribers]              │
│  └─ admin-broadcast.pune       [98 subscribers]               │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌──────────────────┬──────────────────┬──────────────────────────┐
│   User (Mumbai)  │  User (Delhi)    │  User (Bangalore)        │
├──────────────────┼──────────────────┼──────────────────────────┤
│                  │                  │                          │
│  Subscribed to:  │  Subscribed to:  │  Subscribed to:          │
│  admin-broadcast │  admin-broadcast │  admin-broadcast         │
│    .mumbai       │    .delhi        │    .bangalore            │
│                  │                  │                          │
│  ✅ RECEIVES     │  ❌ NOT RECEIVED │  ❌ NOT RECEIVED         │
│  if Mumbai       │  if Mumbai       │  if Mumbai               │
│  selected        │  selected        │  selected                │
│                  │                  │                          │
│  ✅ RECEIVES     │  ✅ RECEIVES     │  ✅ RECEIVES             │
│  if "All"        │  if "All"        │  if "All"                │
│  selected        │  selected        │  selected                │
│                  │                  │                          │
└──────────────────┴──────────────────┴──────────────────────────┘
```

---

## Channel Naming Convention

```
admin-broadcast.{sanitized_city_name}
```

### Sanitization Rules:
- Convert to lowercase
- Replace spaces with underscores
- Replace hyphens with underscores  
- Replace commas/periods with underscores
- Trim whitespace

### Examples:
```
Input           →  Channel Name
────────────────────────────────────────
"Mumbai"        →  admin-broadcast.mumbai
"New Delhi"     →  admin-broadcast.new_delhi
"Navi Mumbai"   →  admin-broadcast.navi_mumbai
"Port Blair"    →  admin-broadcast.port_blair
```

---

## Database Flow

```
┌────────────────────────────────────────────────────────────┐
│                      USERS TABLE                           │
├────────────────────────────────────────────────────────────┤
│  id  │  name       │  city       │  state       │ role_id │
├──────┼─────────────┼─────────────┼──────────────┼─────────┤
│  1   │  John Doe   │  Mumbai     │  Maharashtra │   2     │
│  2   │  Jane Smith │  Delhi      │  Delhi       │   2     │
│  3   │  Bob Wilson │  Mumbai     │  Maharashtra │   2     │
│  4   │  Alice Lee  │  Bangalore  │  Karnataka   │   2     │
└────────────────────────────────────────────────────────────┘
                           │
                           │ FILTERS USERS BY CITY
                           ▼
┌────────────────────────────────────────────────────────────┐
│              TARGET USERS (Mumbai Example)                 │
├────────────────────────────────────────────────────────────┤
│  ✅ John Doe   (Mumbai)                                    │
│  ✅ Bob Wilson (Mumbai)                                    │
│  ❌ Jane Smith (Delhi)     - SKIPPED                       │
│  ❌ Alice Lee  (Bangalore) - SKIPPED                       │
└────────────────────────────────────────────────────────────┘
                           │
                           │ MESSAGE SAVED
                           ▼
┌────────────────────────────────────────────────────────────┐
│                    MESSAGES TABLE                          │
├────────────────────────────────────────────────────────────┤
│  id │ description          │ city    │ calling_number │... │
├─────┼──────────────────────┼─────────┼────────────────┼───┤
│  1  │ "Special offer!"     │ Mumbai  │ 1800-123-456   │   │
│  2  │ "New announcement"   │ Delhi   │ 1800-789-012   │   │
│  3  │ "Update available"   │ all     │ 1800-555-999   │   │
└────────────────────────────────────────────────────────────┘
```

---

## Real-Time Broadcasting Sequence

```
STEP 1: Admin Sends Message
════════════════════════════════════════════════════
Admin Panel → POST /admin/message/store
{
  city: "Mumbai",
  description: "Special offer for Mumbai users!",
  calling_number: "1800-123-456"
}

STEP 2: Controller Processes
════════════════════════════════════════════════════
AdminChatController
  │
  ├─→ Validate data
  ├─→ Get admin info
  ├─→ Save to messages table
  ├─→ Count Mumbai users (1,234)
  └─→ Trigger broadcast event

STEP 3: Event Broadcasts
════════════════════════════════════════════════════
AdminBroadcastMessage
  │
  ├─→ Channel: admin-broadcast.mumbai
  ├─→ Event: admin-message
  └─→ Data: {
        message_id: 123,
        description: "Special offer...",
        admin_name: "Admin",
        target_city: "Mumbai",
        timestamp: "2026-01-14T10:30:00Z"
      }

STEP 4: Reverb Distributes
════════════════════════════════════════════════════
Reverb Server
  │
  ├─→ Find subscribers to "admin-broadcast.mumbai"
  ├─→ Push message to connected clients
  └─→ 1,234 WebSocket connections notified

STEP 5: Users Receive
════════════════════════════════════════════════════
Frontend (Mumbai Users)
  │
  ├─→ Receive on channel "admin-broadcast.mumbai"
  ├─→ Event: admin-message
  ├─→ Display notification
  └─→ Show message content with links
```

---

## Broadcasting to All Cities

```
When city = "all" is selected:

┌─────────────────────────────────────────────────────┐
│         Fetch All Distinct Cities from DB           │
│                                                     │
│  SELECT DISTINCT city FROM users                    │
│  WHERE city IS NOT NULL AND city != ''              │
│  AND role_id = 2                                    │
└─────────────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────┐
│              Cities Found: 28                       │
│                                                     │
│  ["Mumbai", "Delhi", "Bangalore", "Pune", ...]     │
└─────────────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────┐
│         Broadcast to ALL City Channels              │
│                                                     │
│  admin-broadcast.mumbai                             │
│  admin-broadcast.delhi                              │
│  admin-broadcast.bangalore                          │
│  admin-broadcast.pune                               │
│  ... (24 more)                                      │
└─────────────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────┐
│           All Users Receive Message                 │
│                                                     │
│  Total Recipients: 15,420 users                     │
│  Across 28 cities                                   │
└─────────────────────────────────────────────────────┘
```

---

## API Request/Response Flow

### Request: Send Broadcast
```http
POST /admin/message/store
Content-Type: multipart/form-data

description=Special%20offer%20for%20Mumbai!
calling_number=1800-123-456
city=Mumbai
state=Maharashtra
media=@image.jpg
```

### Processing:
```
1. Validation ✓
2. Admin authentication ✓
3. Save to database (message_id: 123) ✓
4. Count Mumbai users (1,234) ✓
5. Broadcast to admin-broadcast.mumbai ✓
```

### Response:
```http
HTTP/1.1 302 Found
Location: /admin/message/list

Session:
  success: "Message sent to users in Mumbai (1,234 users)"
```

---

## User Count API Flow

### Request:
```http
GET /admin/users/count?city=Mumbai
```

### Query Execution:
```sql
SELECT COUNT(*) FROM users
WHERE city = 'Mumbai'
  AND role_id = 2
  AND city IS NOT NULL
  AND city != ''
```

### Response:
```json
{
  "success": true,
  "city": "Mumbai",
  "count": 1234,
  "message": "This will send to 1,234 users in Mumbai"
}
```

---

## Frontend Subscription Flow

### JavaScript Example:
```javascript
// 1. Get user info
const user = await fetch('/api/profile').then(r => r.json());

// 2. Sanitize city name
const cityChannel = user.city
    .toLowerCase()
    .replace(/[ \-,.]/g, '_');

// 3. Subscribe to channel
Echo.channel(`admin-broadcast.${cityChannel}`)
    .listen('admin-message', (event) => {
        // 4. Handle received message
        displayNotification({
            title: `From ${event.admin_name}`,
            body: event.description,
            media: event.media,
            links: event.links
        });
    });
```

### Flutter Example:
```dart
// 1. Get user city
String userCity = currentUser.city;

// 2. Subscribe to broadcasts
await BroadcastService().subscribeToCityBroadcast(
  city: userCity,
  onMessage: (message) {
    // 3. Show notification
    showNotification(message);
  },
);
```

---

## Performance Metrics

```
┌────────────────────────────────────────────────────┐
│              Expected Performance                  │
├────────────────────────────────────────────────────┤
│  Message Save:          < 100ms                    │
│  User Count Query:      < 50ms                     │
│  Broadcast Event:       < 200ms                    │
│  WebSocket Delivery:    < 500ms per user           │
├────────────────────────────────────────────────────┤
│  Total Time (Mumbai):   ~850ms                     │
│  Concurrent Users:      Up to 10,000               │
│  Messages/Hour:         Unlimited                  │
└────────────────────────────────────────────────────┘
```

---

## Error Handling Flow

```
IF broadcast fails:
├─→ Message still saved to database ✓
├─→ Error logged to laravel.log
├─→ Admin notified: "Message saved (Broadcasting unavailable)"
└─→ Can retry broadcast later

IF user count fails:
├─→ Return 0 users
├─→ Log error
└─→ Allow admin to proceed anyway

IF database save fails:
├─→ Transaction rolled back
├─→ No broadcast sent
├─→ Admin sees error message
└─→ User can retry
```

---

**This visual guide complements the technical documentation.**  
Refer to [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) for details.
