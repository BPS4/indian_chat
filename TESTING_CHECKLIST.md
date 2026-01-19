# ✅ City-Wise Broadcasting - Testing Checklist

## Pre-Flight Checks

### Environment Setup
- [ ] Laravel application is running
- [ ] Database connection is working
- [ ] Reverb server is installed (`composer require laravel/reverb`)
- [ ] `.env` has Reverb configuration:
  ```env
  BROADCAST_CONNECTION=reverb
  REVERB_APP_ID=your-app-id
  REVERB_APP_KEY=your-app-key
  REVERB_APP_SECRET=your-app-secret
  REVERB_HOST=127.0.0.1
  REVERB_PORT=8080
  REVERB_SCHEME=http
  ```

### Database Checks
- [ ] Users table has `city` column
- [ ] Users table has `state` column  
- [ ] Users table has `country` column
- [ ] Messages table exists with proper columns
- [ ] At least some users have `city` populated
- [ ] Sample query works:
  ```sql
  SELECT city, COUNT(*) FROM users 
  WHERE city IS NOT NULL AND city != '' 
  GROUP BY city;
  ```

---

## Backend Testing

### 1. Start Reverb Server
```bash
php artisan reverb:start
```

**Expected Output:**
```
Reverb server started successfully.
Server: http://127.0.0.1:8080
App ID: your-app-id
```

**Checklist:**
- [ ] Server starts without errors
- [ ] Port 8080 is accessible
- [ ] No firewall blocking

### 2. Test User Count Endpoint
```bash
curl "http://localhost/admin/users/count?city=Mumbai"
```

**Expected Response:**
```json
{
    "success": true,
    "city": "Mumbai",
    "count": 1234,
    "message": "This will send to 1,234 users in Mumbai"
}
```

**Checklist:**
- [ ] Endpoint returns 200 status
- [ ] JSON response is valid
- [ ] User count is accurate

### 3. Test Broadcast Statistics
```bash
curl "http://localhost/admin/broadcast-stats"
```

**Expected Response:**
```json
{
    "success": true,
    "total_users": 15420,
    "total_cities": 28,
    "city_breakdown": [...]
}
```

**Checklist:**
- [ ] Statistics load successfully
- [ ] City breakdown shows all cities
- [ ] Counts match database

### 4. Test Broadcast with Tinker
```bash
php artisan tinker
```

```php
// Create test message
$message = App\Models\Message::create([
    'description' => 'Test message for Mumbai',
    'calling_number' => '1234567890',
    'city' => 'Mumbai',
    'state' => 'Maharashtra',
    'country' => 'India'
]);

// Get admin
$admin = App\Models\User::where('role_id', 1)->first();

// Broadcast
broadcast(new App\Events\AdminBroadcastMessage(
    $message,
    $admin->id,
    $admin->name,
    'Mumbai'
));
```

**Checklist:**
- [ ] Message created successfully
- [ ] No errors in broadcast
- [ ] Check Reverb server console for activity
- [ ] Message saved in database

### 5. Verify Broadcast Logs
```bash
tail -f storage/logs/laravel.log
```

**Look for:**
- [ ] No errors during broadcast
- [ ] Event dispatched successfully
- [ ] Channel name is correct (admin-broadcast.mumbai)

---

## Admin Panel Testing

### 1. Access Message Form
**URL:** `/admin/message/add`

**Checklist:**
- [ ] Page loads successfully
- [ ] City dropdown populated
- [ ] "All Cities" option available
- [ ] Form fields visible

### 2. Test User Count Preview
1. Select "Mumbai" from dropdown
2. Wait for preview

**Expected:**
```
✅ Mumbai
Recipients: 1,234 users
```

**Checklist:**
- [ ] Preview appears automatically
- [ ] User count is accurate
- [ ] Loading indicator shows first

### 3. Send Test Broadcast (Single City)
**Fill Form:**
- City: Mumbai
- Description: "Test broadcast for Mumbai"
- Calling Number: "1800-123-456"
- Media: (optional)

**Click:** Send Broadcast

**Expected:**
- Success message: "Message sent to users in Mumbai (1,234 users)"
- Redirect to message list
- Message appears in list

**Checklist:**
- [ ] Form submits successfully
- [ ] Success message shows
- [ ] User count displayed
- [ ] Redirect works
- [ ] Message in database
- [ ] Reverb server shows activity

### 4. Send Test Broadcast (All Cities)
**Fill Form:**
- City: All Cities
- Description: "Test broadcast to everyone"
- Calling Number: "1800-123-456"

**Click:** Send Broadcast

**Expected:**
- Success message: "Message broadcast to all cities (15,420 users)"

**Checklist:**
- [ ] Form submits successfully
- [ ] Total user count shown
- [ ] All city channels receive
- [ ] Message saved with city='all'

---

## Frontend Testing

### JavaScript/Web Testing

#### 1. Setup Echo Client
```javascript
// In browser console
Echo.channel('admin-broadcast.mumbai')
    .listen('admin-message', (event) => {
        console.log('Received:', event);
    });
```

**Checklist:**
- [ ] No console errors
- [ ] WebSocket connection established
- [ ] Channel subscribed successfully

#### 2. Send Test Message
Send a broadcast to Mumbai from admin panel

**Expected in Console:**
```javascript
Received: {
    message_id: 123,
    description: "Test message",
    admin_name: "Admin",
    target_city: "Mumbai",
    ...
}
```

**Checklist:**
- [ ] Message received in console
- [ ] All fields present
- [ ] Timestamp is correct
- [ ] Links are included

#### 3. Test City Filtering
**Scenario:** User in Mumbai subscribed to Mumbai channel

1. Send message to **Delhi** from admin
2. User should **NOT** receive it

3. Send message to **Mumbai** from admin  
4. User **SHOULD** receive it

5. Send message to **All Cities**
6. User **SHOULD** receive it

**Checklist:**
- [ ] Mumbai user doesn't get Delhi messages
- [ ] Mumbai user gets Mumbai messages
- [ ] Mumbai user gets "All Cities" messages
- [ ] Filtering works correctly

### Flutter Testing

#### 1. Setup Broadcast Service
```dart
await BroadcastService().initialize(
    appKey: 'your-key',
    host: 'your-host',
    port: 8080,
    scheme: 'http',
);

await BroadcastService().subscribeToCityBroadcast(
    city: 'Mumbai',
    onMessage: (message) {
        print('Received: ${message.description}');
    },
);
```

**Checklist:**
- [ ] No initialization errors
- [ ] WebSocket connects
- [ ] Channel subscription succeeds

#### 2. Receive Test Message
Send broadcast from admin panel

**Expected in Debug Console:**
```
Received: Test message for Mumbai
```

**Checklist:**
- [ ] Message received
- [ ] Notification shows
- [ ] Media loads (if present)
- [ ] Links work

---

## Integration Testing

### Scenario 1: Mumbai User Journey
1. [ ] User registers with city = "Mumbai"
2. [ ] User logs in
3. [ ] Frontend subscribes to admin-broadcast.mumbai
4. [ ] Admin sends message to Mumbai
5. [ ] User receives notification immediately
6. [ ] User clicks notification
7. [ ] Message details display correctly

### Scenario 2: Multi-City Test
1. [ ] Create 3 test users:
   - User A: Mumbai
   - User B: Delhi  
   - User C: Bangalore
2. [ ] All users login and subscribe
3. [ ] Admin sends to Mumbai only
4. [ ] **Only User A** receives message
5. [ ] Admin sends to "All Cities"
6. [ ] **All 3 users** receive message

### Scenario 3: Edge Cases
1. [ ] User with NULL city → Doesn't receive city messages
2. [ ] User with empty city → Doesn't receive city messages
3. [ ] City name with spaces → Works correctly (e.g., "New Delhi")
4. [ ] City name case-insensitive → Mumbai = MUMBAI = mumbai
5. [ ] Special characters in city → Handled correctly

---

## Performance Testing

### Load Test Parameters
- [ ] Send broadcast to largest city
- [ ] Monitor server CPU/memory
- [ ] Check Reverb server performance
- [ ] Verify message delivery time

**Expected:**
- Message saved: < 100ms
- Broadcast triggered: < 200ms
- User receives: < 1 second total

**Checklist:**
- [ ] No timeouts
- [ ] No memory leaks
- [ ] All users receive message
- [ ] Server stays responsive

---

## Security Testing

### Authorization Checks
- [ ] Only admins can access `/admin/message/add`
- [ ] Only admins can POST to `/admin/message/store`
- [ ] Only admins can access `/admin/users/count`
- [ ] Regular users cannot send broadcasts
- [ ] Session validation works

### Input Validation
- [ ] XSS protection in message content
- [ ] SQL injection protection
- [ ] File upload validation (media)
- [ ] URL validation for links
- [ ] Max message length enforced

---

## Error Handling Testing

### Test Error Scenarios

#### 1. Reverb Server Down
1. [ ] Stop Reverb server: `Ctrl+C`
2. [ ] Send broadcast from admin panel
3. [ ] Expected: "Message saved (Broadcasting unavailable)"
4. [ ] Message still in database
5. [ ] Can retry when server back up

#### 2. Invalid City
1. [ ] Send to city with 0 users
2. [ ] Expected: "This will send to 0 users in XYZ"
3. [ ] Broadcast still sent
4. [ ] No errors

#### 3. Database Error
1. [ ] Simulate DB disconnect
2. [ ] Try to send broadcast
3. [ ] Expected: Error message
4. [ ] No partial data saved
5. [ ] Transaction rolled back

---

## Browser Testing

### Test in Multiple Browsers
- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari (if available)
- [ ] Mobile browsers

**For Each Browser:**
- [ ] WebSocket connects
- [ ] Messages received
- [ ] Notifications work
- [ ] No console errors

---

## Mobile Testing (Flutter)

### Android
- [ ] App connects to WebSocket
- [ ] Receives broadcasts
- [ ] Push notifications work
- [ ] Background reception works

### iOS
- [ ] App connects to WebSocket
- [ ] Receives broadcasts
- [ ] Push notifications work
- [ ] Background reception works

---

## Production Readiness

### Infrastructure
- [ ] Reverb server configured for production
- [ ] SSL/TLS enabled (wss:// instead of ws://)
- [ ] Firewall rules allow WebSocket
- [ ] Load balancer configured (if needed)
- [ ] Monitoring setup

### Configuration
- [ ] `.env` production values set
- [ ] Debug mode OFF
- [ ] Logging configured
- [ ] Error reporting setup
- [ ] Backup strategy in place

### Documentation
- [ ] API endpoints documented
- [ ] Frontend integration guide ready
- [ ] Admin user guide created
- [ ] Troubleshooting guide available

---

## Final Verification

### Code Quality
- [ ] No PHP errors or warnings
- [ ] No JavaScript console errors
- [ ] Code follows PSR standards
- [ ] Comments added where needed
- [ ] No debug statements left

### Functionality
- [ ] City-wise filtering works
- [ ] "All cities" works
- [ ] User count accurate
- [ ] Statistics correct
- [ ] Media uploads work
- [ ] Links work

### User Experience
- [ ] Admin panel intuitive
- [ ] User count preview helpful
- [ ] Success messages clear
- [ ] Error messages informative
- [ ] Notifications attractive

---

## Sign-Off Checklist

I confirm that:
- [ ] All backend tests passed
- [ ] All frontend tests passed
- [ ] All integration tests passed
- [ ] Security checks completed
- [ ] Performance acceptable
- [ ] Documentation complete
- [ ] Team trained on new feature
- [ ] Ready for production deployment

**Tested By:** _________________  
**Date:** _________________  
**Environment:** [ ] Staging  [ ] Production  
**Status:** [ ] Pass  [ ] Fail (with notes)

---

## Quick Test Commands

```bash
# Start Reverb
php artisan reverb:start

# Test broadcast in Tinker
php artisan tinker
>>> broadcast(new App\Events\AdminBroadcastMessage(...));

# Check logs
tail -f storage/logs/laravel.log

# Test API
curl http://localhost/admin/users/count?city=Mumbai

# Verify routes
php artisan route:list | grep broadcast

# Check database
php artisan tinker
>>> App\Models\User::select('city')->distinct()->pluck('city');
>>> App\Models\Message::where('city', 'Mumbai')->count();
```

---

**Need Help?** Refer to:
- [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) - Overview
- [CITY_WISE_BROADCAST_GUIDE.md](CITY_WISE_BROADCAST_GUIDE.md) - Detailed guide
- [ARCHITECTURE_DIAGRAM.md](ARCHITECTURE_DIAGRAM.md) - Visual diagrams
