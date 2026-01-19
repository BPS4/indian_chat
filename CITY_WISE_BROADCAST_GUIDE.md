# 🏙️ City-Wise Broadcasting Implementation Guide

## Overview
This guide explains how the city-wise broadcast messaging system works in your Laravel application. Users now receive broadcast messages only for their specific city.

---

## 🎯 How It Works

### 1. **Backend Implementation**

#### Broadcasting Channels
The system uses **city-specific channels** instead of a global broadcast channel:

**Previous (All Users):**
```
admin-broadcast  → All users receive all messages
```

**Current (City-Specific):**
```
admin-broadcast.mumbai     → Only Mumbai users
admin-broadcast.delhi      → Only Delhi users
admin-broadcast.bangalore  → Only Bangalore users
admin-broadcast.all        → Broadcast to all cities
```

#### Channel Name Format
City names are sanitized to create valid channel names:
- Spaces, hyphens, commas → replaced with underscores
- Converted to lowercase
- Example: `"New Delhi"` → `"new_delhi"`

---

### 2. **Database Structure**

#### Users Table (Already Has These Fields ✅)
```php
- city           → User's city (e.g., "Mumbai", "Delhi")
- state          → User's state
- country        → User's country
```

#### Messages Table
```php
- description    → Message content
- media          → Optional media file
- city           → Target city (or "all")
- state          → Target state
- country        → Target country (default: India)
- calling_number → Contact number
- youtube_link, website_link, instagram_link, etc.
```

---

## 📡 Broadcasting Flow

### Step 1: Admin Sends Message
```
Admin Panel → Select City → Enter Message → Submit
```

### Step 2: Controller Processes Request
```php
// AdminChatController.php
1. Validates input
2. Saves message to database
3. Determines target city (specific or "all")
4. Broadcasts to city-specific channel(s)
```

### Step 3: Event Broadcasts
```php
// AdminBroadcastMessage.php
- If city = "all" → Broadcasts to all city channels
- If city = specific → Broadcasts to that city's channel only
```

### Step 4: Frontend Receives
```javascript
// User's device listens to their city channel
Echo.channel('admin-broadcast.mumbai')
    .listen('admin-message', (data) => {
        // Display notification
        showNotification(data);
    });
```

---

## 🚀 Usage Examples

### Example 1: Broadcast to Mumbai Only
```php
POST /admin/broadcast-message
{
    "description": "Special offer for Mumbai users!",
    "calling_number": "1800-123-4567",
    "city": "Mumbai",
    "state": "Maharashtra"
}
```
**Result:** Only users with `city = "Mumbai"` in their profile will receive this.

---

### Example 2: Broadcast to All Cities
```php
POST /admin/broadcast-message
{
    "description": "National holiday announcement",
    "calling_number": "1800-123-4567",
    "city": "all",  // or leave empty
    "state": null
}
```
**Result:** All users in all cities receive the message.

---

### Example 3: Count Users Before Sending
```php
// Get user count for a specific city
$userCount = User::where('city', 'Mumbai')
    ->where('role_id', User::CUSTOMER)
    ->count();

// Returns: "Message sent to users in Mumbai (1,234 users)"
```

---

## 🔧 Frontend Integration (Flutter/JavaScript)

### Flutter Example
```dart
import 'package:laravel_echo/laravel_echo.dart';

// Get user's city from their profile
String userCity = currentUser.city;

// Sanitize city name (same as backend)
String channelName = 'admin-broadcast.${sanitizeCityName(userCity)}';

// Listen to city-specific broadcasts
Echo.channel(channelName)
    .listen('admin-message', (event) {
        print('New admin message: ${event['description']}');
        showNotification(
            title: 'Message from ${event['admin_name']}',
            body: event['description'],
            media: event['media'],
            links: event['links']
        );
    });

String sanitizeCityName(String city) {
    return city.toLowerCase()
        .replaceAll(RegExp(r'[ \-,.]'), '_')
        .trim();
}
```

### JavaScript Example
```javascript
// Get user's city from API
const userProfile = await fetch('/api/profile').then(r => r.json());
const userCity = userProfile.city;

// Sanitize city name
const sanitizeCityName = (city) => {
    return city.toLowerCase()
        .replace(/[ \-,.]/g, '_')
        .trim();
};

const channelName = `admin-broadcast.${sanitizeCityName(userCity)}`;

// Listen to broadcasts
Echo.channel(channelName)
    .listen('admin-message', (event) => {
        console.log('Admin broadcast:', event);
        
        // Display notification
        displayBroadcast({
            title: `Message from ${event.admin_name}`,
            description: event.description,
            media: event.media,
            links: event.links,
            timestamp: event.timestamp
        });
    });
```

---

## 🔐 Security & Authorization

### Channel Authorization (Optional)
If you want to make channels private:

```php
// routes/channels.php
Broadcast::channel('admin-broadcast.{city}', function ($user, $city) {
    // Verify user belongs to this city
    return strtolower(str_replace(' ', '_', $user->city)) === $city;
});
```

**Note:** Currently using **public channels** since admin messages are not sensitive.

---

## 🎨 Admin Panel Form Example

### Blade View
```blade
<form action="{{ route('admin.broadcast.send') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="form-group">
        <label>Target City</label>
        <select name="city" class="form-control" id="citySelect">
            <option value="all">All Cities</option>
            @foreach($cities as $city)
                <option value="{{ $city->city }}">{{ $city->city }}</option>
            @endforeach
        </select>
    </div>
    
    <div class="form-group">
        <label>Message</label>
        <textarea name="description" class="form-control" required></textarea>
    </div>
    
    <div class="form-group">
        <label>Contact Number</label>
        <input type="text" name="calling_number" class="form-control" required>
    </div>
    
    <div class="form-group">
        <label>Media (Optional)</label>
        <input type="file" name="media" class="form-control" accept="image/*,video/*">
    </div>
    
    <button type="submit" class="btn btn-primary">Send Broadcast</button>
</form>

<script>
// Show user count when city is selected
$('#citySelect').on('change', function() {
    const city = $(this).val();
    
    $.get('/admin/users/count', { city: city }, function(data) {
        alert(`This will send to ${data.count} users`);
    });
});
</script>
```

---

## 📊 Monitoring & Analytics

### Get Broadcast Statistics
```php
// Controller method to get statistics
public function getBroadcastStats(Request $request)
{
    $cityStats = User::select('city', DB::raw('count(*) as user_count'))
        ->whereNotNull('city')
        ->where('city', '!=', '')
        ->where('role_id', User::CUSTOMER)
        ->groupBy('city')
        ->orderBy('user_count', 'DESC')
        ->get();
    
    return response()->json([
        'total_users' => User::where('role_id', User::CUSTOMER)->count(),
        'cities' => $cityStats,
        'total_cities' => $cityStats->count()
    ]);
}
```

**Example Response:**
```json
{
    "total_users": 15420,
    "total_cities": 28,
    "cities": [
        { "city": "Mumbai", "user_count": 3245 },
        { "city": "Delhi", "user_count": 2890 },
        { "city": "Bangalore", "user_count": 2156 },
        ...
    ]
}
```

---

## 🧪 Testing

### Test Broadcasting to Specific City
```bash
# Start Reverb server
php artisan reverb:start

# In another terminal, test the broadcast
php artisan tinker

# Test Mumbai broadcast
$message = \App\Models\Message::create([
    'description' => 'Test message',
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

### Test Broadcasting to All Cities
```php
broadcast(new \App\Events\AdminBroadcastMessage(
    $message, 
    $admin->id, 
    $admin->name, 
    'all'  // Broadcasts to all cities
));
```

---

## 🐛 Troubleshooting

### Issue: Users Not Receiving Messages
**Check:**
1. Is Reverb server running? → `php artisan reverb:start`
2. Does user have a city in their profile? → Check `users.city` column
3. Is frontend subscribed to correct channel? → Check browser console
4. Channel name matches? → Both should use same sanitization

### Issue: Broadcasting to Wrong Cities
**Check:**
1. City name spelling matches exactly
2. Sanitization function is consistent between backend/frontend
3. Check logs: `storage/logs/laravel.log`

### Issue: All Cities Not Working
**Check:**
1. Ensure city field is either "all" or empty
2. Verify distinct cities query returns results
3. Check if User model has correct namespace import

---

## 📝 Summary

### What Changed:
✅ Broadcasts now target **city-specific channels**  
✅ Option to broadcast to **all cities** at once  
✅ User count shown when sending broadcasts  
✅ Consistent city name sanitization  
✅ Better error handling and logging  

### Next Steps:
1. Update your Flutter/JavaScript frontend to listen to city-specific channels
2. Test with different cities
3. Add broadcast analytics dashboard (optional)
4. Implement read receipts per city (optional)

---

## 📞 Support

If you encounter issues, check:
- Laravel logs: `storage/logs/laravel.log`
- Reverb server output
- Browser console (frontend)
- Network tab for WebSocket connections

---

**Last Updated:** January 2026  
**Version:** 2.0
