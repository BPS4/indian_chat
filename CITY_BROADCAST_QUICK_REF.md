# 🚀 City-Wise Broadcasting - Quick Reference

## ✅ What Was Implemented

### Backend Changes:

1. **[AdminBroadcastMessage.php](app/Events/AdminBroadcastMessage.php)**
   - Changed from single `admin-broadcast` channel → city-specific channels
   - Format: `admin-broadcast.{city}` (e.g., `admin-broadcast.mumbai`)
   - Supports broadcasting to "all cities" at once
   - Sanitizes city names (spaces/special chars → underscores)

2. **[AdminChatController.php](app/Http/Controllers/Admin/AdminChatController.php)**
   - Added user count display when sending broadcasts
   - New methods:
     - `getUserCountByCity()` - Preview recipient count
     - `getBroadcastStats()` - Analytics dashboard data
   - Shows: "Message sent to Mumbai (1,234 users)"

3. **[routes/web.php](routes/web.php)**
   - New routes:
     - `GET /admin/users/count` - Get user count by city
     - `GET /admin/broadcast-stats` - Get statistics

4. **Helper Files Created:**
   - `public/js/admin-broadcast.js` - Frontend helper for admin panel
   - `CITY_WISE_BROADCAST_GUIDE.md` - Complete documentation
   - `FLUTTER_CITY_BROADCAST_EXAMPLE.dart` - Flutter integration

---

## 📋 How It Works

### Old System (Before):
```
Admin sends message → Everyone receives it (no filtering)
```

### New System (Now):
```
Admin selects city → Only users in that city receive it
```

### Channel Structure:
```
admin-broadcast.mumbai      → Mumbai users only
admin-broadcast.delhi       → Delhi users only  
admin-broadcast.bangalore   → Bangalore users only
admin-broadcast.all         → All cities (when "all" selected)
```

---

## 🎯 Quick Start

### 1. Backend Testing (Laravel Tinker)
```php
php artisan tinker

// Test Mumbai broadcast
$message = \App\Models\Message::create([
    'description' => 'Test message for Mumbai',
    'calling_number' => '1234567890',
    'city' => 'Mumbai',
    'state' => 'Maharashtra'
]);

$admin = \App\Models\User::find(1);

broadcast(new \App\Events\AdminBroadcastMessage(
    $message, 
    $admin->id, 
    $admin->name, 
    'Mumbai'
));
```

### 2. Check User Count
```bash
curl "http://your-domain/admin/users/count?city=Mumbai"
```

Response:
```json
{
    "success": true,
    "city": "Mumbai",
    "count": 1234,
    "message": "This will send to 1,234 users in Mumbai"
}
```

### 3. Frontend Integration

#### JavaScript (Web):
```javascript
// Get user's city from profile
const userCity = currentUser.city; // e.g., "Mumbai"

// Sanitize city name
const channelName = `admin-broadcast.${userCity.toLowerCase().replace(/[ \-,.]/g, '_')}`;

// Subscribe to broadcasts
Echo.channel(channelName)
    .listen('admin-message', (event) => {
        showNotification({
            title: `Message from ${event.admin_name}`,
            body: event.description,
            links: event.links
        });
    });
```

#### Flutter:
```dart
// Initialize broadcast service
final broadcastService = BroadcastService();
await broadcastService.initialize(
  appKey: 'your-key',
  host: 'your-host.com',
  port: 8080,
  scheme: 'https',
);

// Subscribe to city broadcasts
await broadcastService.subscribeToCityBroadcast(
  city: userCity, // e.g., "Mumbai"
  onMessage: (message) {
    showNotification(message);
  },
);
```

---

## 🔧 API Endpoints

### Get User Count
```http
GET /admin/users/count?city=Mumbai
```

### Get All Statistics
```http
GET /admin/broadcast-stats
```

Response:
```json
{
    "success": true,
    "total_users": 15420,
    "total_cities": 28,
    "total_messages_sent": 342,
    "messages_today": 12,
    "city_breakdown": [
        {"city": "Mumbai", "user_count": 3245},
        {"city": "Delhi", "user_count": 2890},
        ...
    ]
}
```

### Send Broadcast
```http
POST /admin/message/store
Content-Type: multipart/form-data

description: "Your message here"
calling_number: "1800-123-456"
city: "Mumbai"  // or "all" for all cities
state: "Maharashtra"  // optional
media: [file]  // optional
youtube_link: "https://..."  // optional
website_link: "https://..."  // optional
```

---

## 🧪 Testing Checklist

- [ ] Backend: Reverb server running (`php artisan reverb:start`)
- [ ] Backend: Broadcast sends successfully
- [ ] Backend: User count shows correctly
- [ ] Frontend: User subscribed to correct city channel
- [ ] Frontend: Notification displays when broadcast received
- [ ] Frontend: Channel name sanitization matches backend
- [ ] Test: Broadcast to specific city (only that city receives)
- [ ] Test: Broadcast to "all" (all cities receive)

---

## 🐛 Common Issues & Solutions

### Issue: Users not receiving messages
**Solution:** 
1. Check user has `city` field populated in database
2. Verify Reverb server is running
3. Check channel name matches exactly (case-sensitive)
4. View browser console for errors

### Issue: Wrong city receiving messages
**Solution:**
1. Verify city name sanitization is same on backend/frontend
2. Check `users.city` field spelling matches exactly
3. Review broadcast event logs

### Issue: "All cities" not working
**Solution:**
1. Set city field to "all" or leave empty
2. Verify distinct cities query returns results
3. Check if User model properly imported

---

## 📊 Database Fields Used

### Users Table:
- `city` - User's city (REQUIRED for city-wise broadcasting)
- `state` - User's state (optional)
- `country` - User's country (optional)
- `role_id` - Must be `2` (CUSTOMER) to receive broadcasts

### Messages Table:
- `description` - Message content
- `city` - Target city (or "all")
- `state` - Target state
- `calling_number` - Contact number
- `media` - Optional media file
- `youtube_link, website_link, etc.` - Optional links

---

## 🎨 Admin Panel Integration

Add this to your broadcast form view:

```blade
<script src="{{ asset('js/admin-broadcast.js') }}"></script>

<select name="city" id="citySelect" class="form-control">
    <option value="all">All Cities</option>
    @foreach($cities as $city)
        <option value="{{ $city->city }}">{{ $city->city }}</option>
    @endforeach
</select>

<div id="userCountDisplay"></div>
```

The JavaScript file will automatically show user counts when cities are selected.

---

## 📚 Files Modified/Created

### Modified:
- ✅ `app/Events/AdminBroadcastMessage.php`
- ✅ `app/Http/Controllers/Admin/AdminChatController.php`
- ✅ `routes/web.php`

### Created:
- ✅ `public/js/admin-broadcast.js`
- ✅ `CITY_WISE_BROADCAST_GUIDE.md`
- ✅ `FLUTTER_CITY_BROADCAST_EXAMPLE.dart`
- ✅ `CITY_BROADCAST_QUICK_REF.md` (this file)

---

## 🚀 Next Steps

1. **Test the Implementation:**
   ```bash
   php artisan reverb:start
   ```

2. **Update Frontend:**
   - Update Flutter app to subscribe to city-specific channels
   - Use the example code in `FLUTTER_CITY_BROADCAST_EXAMPLE.dart`

3. **Add Analytics (Optional):**
   - Create dashboard showing broadcast reach
   - Track message open rates by city
   - Add city-wise engagement metrics

4. **Enhance Features (Optional):**
   - Schedule broadcasts for specific times
   - Add targeting by state + city combination
   - Implement user preferences (opt-in/opt-out)

---

## 💡 Pro Tips

1. **City Name Consistency:** Ensure city names in database match exactly
2. **Error Logging:** Check `storage/logs/laravel.log` for broadcast issues
3. **Performance:** For large user bases, consider queuing broadcasts
4. **Testing:** Use Laravel Tinker for quick broadcast testing
5. **Security:** Ensure only admins can access broadcast endpoints

---

**Questions?** Review `CITY_WISE_BROADCAST_GUIDE.md` for detailed explanations.
