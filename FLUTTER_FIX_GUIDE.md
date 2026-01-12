# Flutter Admin Message Fix Guide

## Issue
Getting 401 Unauthorized error when fetching admin messages.

## Root Cause
JWT middleware wasn't properly registered in Laravel bootstrap/app.php

## Fixes Applied

### 1. Backend - Registered JWT Middleware
**File:** `bootstrap/app.php`

Added JWT middleware aliases:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'session.token' => \App\Http\Middleware\SessionToken::class,
        'CheckSession' => \App\Http\Middleware\CheckSession::class,
        'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,  // ✅ Added
        'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,  // ✅ Added
    ]);
})
```

### 2. Backend - Fixed API Response Structure
**File:** `app/Http/Controllers/Api/ChatController.php`

Updated `getAdminConversation()` method to return proper structure:
```php
public function getAdminConversation(Request $request)
{
    try {
        // Get all messages from messages table (admin broadcasts)
        $messages = Message::whereNotNull('description')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'messages' => $messages,  // ✅ Matches Flutter expectation
            'conversation' => [
                'type' => 'admin_broadcast',
                'name' => 'Admin Announcements',
                'is_admin_conversation' => true
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching admin messages: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
```

### 3. Flutter - Update AdminMessage Model

**File:** `lib/data/models/admin_message_model.dart`

Make sure the model matches backend fields:

```dart
class AdminMessage {
  final int? id;
  final String description;
  final String? media;
  final String? youtubeLink;
  final String? callingNumber;
  final String? websiteLink;
  final String? instagramLink;
  final String? facebookLink;
  final String? telegramLink;
  final String? country;
  final String? state;
  final String? city;
  final bool? autoSend;
  final int? totalUsers;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  AdminMessage({
    this.id,
    required this.description,
    this.media,
    this.youtubeLink,
    this.callingNumber,
    this.websiteLink,
    this.instagramLink,
    this.facebookLink,
    this.telegramLink,
    this.country,
    this.state,
    this.city,
    this.autoSend,
    this.totalUsers,
    this.createdAt,
    this.updatedAt,
  });

  factory AdminMessage.fromJson(Map<String, dynamic> json) {
    return AdminMessage(
      id: json['id'],
      description: json['description'] ?? '',
      media: json['media'],
      youtubeLink: json['youtube_link'],
      callingNumber: json['calling_number'],
      websiteLink: json['website_link'],
      instagramLink: json['instagram_link'],
      facebookLink: json['facebook_link'],
      telegramLink: json['telegram_link'],
      country: json['country'],
      state: json['state'],
      city: json['city'],
      autoSend: json['auto_send'] == 1 || json['auto_send'] == true,
      totalUsers: json['total_users'],
      createdAt: json['created_at'] != null 
          ? DateTime.parse(json['created_at']) 
          : null,
      updatedAt: json['updated_at'] != null 
          ? DateTime.parse(json['updated_at']) 
          : null,
    );
  }

  String get mediaUrl {
    if (media == null || media!.isEmpty) return '';
    return 'http://myindiabusiness.com/storage/$media';  // ✅ Use your domain
  }

  bool get isVideo {
    if (media == null) return false;
    return media!.endsWith('.mp4') || 
           media!.endsWith('.mov') || 
           media!.endsWith('.avi');
  }

  bool get isImage {
    if (media == null) return false;
    return media!.endsWith('.jpg') || 
           media!.endsWith('.jpeg') || 
           media!.endsWith('.png');
  }
}
```

### 4. Flutter - Update AdminMessageController

**File:** `lib/controllers/admin_message_controller.dart`

Update the WebSocket initialization to use your domain:

```dart
void _initializePusher() {
  try {
    PusherOptions options = PusherOptions(
      host: 'myindiabusiness.com',  // ✅ Your domain (without http://)
      wsPort: 8080,
      encrypted: false,  // ✅ Set to true for production with SSL
      cluster: 'mt1',
    );

    _pusher = PusherClient(
      'bqefva00jou7erqcx7ob',  // Your Reverb app key
      options,
      autoConnect: true,
      enableLogging: true,
    );

    _pusher!.onConnectionStateChange((state) {
      log('🔌 Pusher connection state: ${state!.currentState}');
      isConnected.value = state.currentState == 'connected';
    });

    _pusher!.onConnectionError((error) {
      log('❌ Pusher connection error: ${error!.message}');
    });

    _pusher!.connect();

    // Subscribe to admin-broadcast channel
    _channel = _pusher!.subscribe('admin-broadcast');

    // ✅ Bind to the correct event name
    _channel!.bind('admin-message', (event) {
      log('📩 Received admin message: ${event?.data}');

      try {
        dynamic messageData = event?.data;

        if (messageData is String) {
          messageData = jsonDecode(messageData);
        }

        if (messageData != null && messageData is Map<String, dynamic>) {
          // ✅ Create message object from broadcast data
          final newMessageData = {
            'id': messageData['messageId'],
            'description': messageData['message'],
            'created_at': DateTime.now().toIso8601String(),
            'updated_at': DateTime.now().toIso8601String(),
          };
          
          final newMessage = AdminMessage.fromJson(newMessageData);
          messages.insert(0, newMessage);
          log('✅ New admin message added');
        }
      } catch (e) {
        log('❌ Error processing admin message: $e');
      }
    });

    log('✅ Pusher initialized and subscribed to admin-broadcast');
  } catch (e) {
    log('❌ Failed to initialize Pusher: $e');
  }
}
```

## Testing Steps

### 1. Clear Laravel Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### 2. Restart Reverb Server
```bash
php artisan reverb:start
```

### 3. Test API Endpoint with Postman

**Request:**
```
GET http://myindiabusiness.com/api/admin-messages
Headers:
  Authorization: Bearer YOUR_JWT_TOKEN
  Accept: application/json
```

**Expected Response:**
```json
{
  "success": true,
  "messages": {
    "data": [
      {
        "id": 1,
        "description": "Message text",
        "media": "messages/filename.png",
        "youtube_link": null,
        "calling_number": "1234567890",
        "website_link": null,
        "instagram_link": null,
        "facebook_link": null,
        "telegram_link": null,
        "country": "India",
        "state": "Maharashtra",
        "city": "Mumbai",
        "auto_send": 1,
        "total_users": null,
        "created_at": "2026-01-11T22:27:01.000000Z",
        "updated_at": "2026-01-11T22:27:01.000000Z"
      }
    ]
  },
  "conversation": {
    "type": "admin_broadcast",
    "name": "Admin Announcements",
    "is_admin_conversation": true
  }
}
```

### 4. Test in Flutter App

1. **Clear app data** (to force fresh login)
2. **Login** to get new JWT token
3. **Navigate to Admin Messages** screen
4. **Check logs** - should see:
   ```
   ✅ Fetched N admin messages
   ✅ Pusher initialized and subscribed to admin-broadcast
   🔌 Pusher connection state: connected
   ```

### 5. Test Real-Time Broadcasting

1. **Keep Flutter app open** on Admin Messages screen
2. **Go to admin panel:** `http://myindiabusiness.com/admin/message/add`
3. **Send a new message**
4. **Check Flutter logs:**
   ```
   📩 Received admin message: {...}
   ✅ New admin message added
   ```
5. **Message should appear instantly** at top of list

## Troubleshooting

### Still Getting 401 Error

1. **Check JWT token is valid:**
   ```dart
   final prefs = await SharedPreferences.getInstance();
   final token = prefs.getString('jwt_token');
   print('JWT Token: $token');
   ```

2. **Verify token in Laravel:**
   ```bash
   php artisan tinker
   ```
   ```php
   $token = 'YOUR_JWT_TOKEN';
   $user = \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->authenticate();
   dd($user);
   ```

3. **Check .env has JWT_SECRET:**
   ```env
   JWT_SECRET=your_secret_key_here
   ```
   If missing, run: `php artisan jwt:secret`

### WebSocket Connection Failing

1. **Check Reverb is running:**
   ```bash
   php artisan reverb:start
   ```
   Should show: `INFO  Starting server on 0.0.0.0:8080`

2. **Check .env Reverb config:**
   ```env
   REVERB_APP_ID=562045
   REVERB_APP_KEY=bqefva00jou7erqcx7ob
   REVERB_APP_SECRET=rw86vb1yjyevqh4jxfkc
   REVERB_HOST=localhost
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```

3. **Test WebSocket from browser:**
   Open browser console on any page and run:
   ```javascript
   const pusher = new Pusher('bqefva00jou7erqcx7ob', {
     wsHost: 'myindiabusiness.com',
     wsPort: 8080,
     forceTLS: false
   });
   
   const channel = pusher.subscribe('admin-broadcast');
   channel.bind('admin-message', data => console.log(data));
   ```

### Messages Not Appearing

1. **Check database has messages:**
   ```sql
   SELECT * FROM messages WHERE description IS NOT NULL ORDER BY created_at DESC LIMIT 10;
   ```

2. **Check API returns data:**
   ```bash
   curl -H "Authorization: Bearer YOUR_TOKEN" http://myindiabusiness.com/api/admin-messages
   ```

3. **Verify Flutter model parsing:**
   Add debug logging in `fetchAdminMessages()`:
   ```dart
   print('Response data: ${response.data}');
   print('Messages count: ${messagesData.length}');
   ```

## Production Checklist

- [ ] Set `encrypted: true` in Flutter PusherOptions
- [ ] Update domain from localhost to production domain
- [ ] Configure proper SSL certificate for WebSocket
- [ ] Update REVERB_SCHEME=https in .env
- [ ] Test on physical device, not just emulator
- [ ] Add proper error handling for network failures
- [ ] Implement push notifications for background messages

## Additional Notes

- **JWT Token Expiry:** Set to 1 year (525600 minutes) in config/jwt.php
- **Session Token:** Required for `/api/send-otp` and `/api/verify-otp` endpoints
- **Admin Auth:** Uses session-based authentication on web.php routes
- **Mobile Auth:** Uses JWT authentication on api.php routes

---

**Last Updated:** January 11, 2026
