# Flutter App Integration Guide - Admin Broadcast Chat

## Overview
This guide shows how to integrate the Laravel Reverb admin broadcast chat system into your Flutter mobile application.

## Prerequisites
- Flutter SDK installed
- Laravel API running with Reverb server
- JWT authentication configured

## Installation

### 1. Add Dependencies

Add these packages to your `pubspec.yaml`:

```yaml
dependencies:
  flutter:
    sdk: flutter
  
  # HTTP requests
  http: ^1.1.0
  dio: ^5.4.0
  
  # WebSocket & Real-time
  web_socket_channel: ^2.4.0
  laravel_echo: ^0.4.0
  pusher_client: ^2.0.0
  
  # State Management
  provider: ^6.1.1
  
  # Local Storage
  shared_preferences: ^2.2.2
  
  # JSON Serialization
  json_annotation: ^4.8.1

dev_dependencies:
  build_runner: ^2.4.7
  json_serializable: ^6.7.1
```

Run:
```bash
flutter pub get
```

## Project Structure

```
lib/
├── models/
│   ├── message.dart
│   ├── conversation.dart
│   └── user.dart
├── services/
│   ├── api_service.dart
│   ├── auth_service.dart
│   └── websocket_service.dart
├── providers/
│   └── admin_chat_provider.dart
├── screens/
│   └── admin_chat_screen.dart
└── widgets/
    ├── message_bubble.dart
    └── admin_badge.dart
```

## Implementation

### 1. Models

#### message.dart
```dart
import 'package:json_annotation/json_annotation.dart';

part 'message.g.dart';

@JsonSerializable()
class Message {
  final int id;
  @JsonKey(name: 'conversation_id')
  final int conversationId;
  @JsonKey(name: 'sender_id')
  final int senderId;
  final String message;
  @JsonKey(name: 'is_read')
  final bool isRead;
  @JsonKey(name: 'created_at')
  final String createdAt;
  final MessageSender? sender;

  Message({
    required this.id,
    required this.conversationId,
    required this.senderId,
    required this.message,
    required this.isRead,
    required this.createdAt,
    this.sender,
  });

  factory Message.fromJson(Map<String, dynamic> json) => _$MessageFromJson(json);
  Map<String, dynamic> toJson() => _$MessageToJson(this);
}

@JsonSerializable()
class MessageSender {
  final int id;
  final String? name;
  final String? email;
  @JsonKey(name: 'profile_pic')
  final String? profilePic;

  MessageSender({
    required this.id,
    this.name,
    this.email,
    this.profilePic,
  });

  factory MessageSender.fromJson(Map<String, dynamic> json) => 
      _$MessageSenderFromJson(json);
  Map<String, dynamic> toJson() => _$MessageSenderToJson(this);
}
```

#### conversation.dart
```dart
import 'package:json_annotation/json_annotation.dart';

part 'conversation.g.dart';

@JsonSerializable()
class Conversation {
  final int id;
  final String type;
  final String? name;
  @JsonKey(name: 'is_admin_conversation')
  final bool? isAdminConversation;

  Conversation({
    required this.id,
    required this.type,
    this.name,
    this.isAdminConversation,
  });

  factory Conversation.fromJson(Map<String, dynamic> json) => 
      _$ConversationFromJson(json);
  Map<String, dynamic> toJson() => _$ConversationToJson(this);
}
```

### 2. API Service

#### api_service.dart
```dart
import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/message.dart';
import '../models/conversation.dart';

class ApiService {
  static const String baseUrl = 'http://localhost/api';
  late Dio _dio;

  ApiService() {
    _dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: const Duration(seconds: 30),
      receiveTimeout: const Duration(seconds: 30),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));

    // Add interceptor for JWT token
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final prefs = await SharedPreferences.getInstance();
        final token = prefs.getString('auth_token');
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
      onError: (error, handler) {
        print('API Error: ${error.response?.data}');
        return handler.next(error);
      },
    ));
  }

  // Get admin conversation
  Future<Map<String, dynamic>> getAdminMessages() async {
    try {
      final response = await _dio.get('/admin-messages');
      return response.data;
    } catch (e) {
      print('Error getting admin messages: $e');
      rethrow;
    }
  }

  // Send OTP
  Future<Map<String, dynamic>> sendOtp(String mobile) async {
    try {
      final response = await _dio.post('/send-otp', data: {'mobile': mobile});
      return response.data;
    } catch (e) {
      print('Error sending OTP: $e');
      rethrow;
    }
  }

  // Verify OTP
  Future<Map<String, dynamic>> verifyOtp(String mobile, String otp) async {
    try {
      final response = await _dio.post('/verify-otp', data: {
        'login': mobile,
        'otp': otp,
      });
      
      // Save token
      if (response.data['access_token'] != null) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', response.data['access_token']);
      }
      
      return response.data;
    } catch (e) {
      print('Error verifying OTP: $e');
      rethrow;
    }
  }

  // Get current user
  Future<Map<String, dynamic>> getProfile() async {
    try {
      final response = await _dio.get('/profile');
      return response.data;
    } catch (e) {
      print('Error getting profile: $e');
      rethrow;
    }
  }
}
```

### 3. WebSocket Service

#### websocket_service.dart
```dart
import 'dart:async';
import 'dart:convert';
import 'package:web_socket_channel/web_socket_channel.dart';
import 'package:shared_preferences/shared_preferences.dart';

class WebSocketService {
  static const String wsUrl = 'ws://localhost:8080/app/bqefva00jou7erqcx7ob';
  WebSocketChannel? _channel;
  final _messageController = StreamController<Map<String, dynamic>>.broadcast();
  
  Stream<Map<String, dynamic>> get messageStream => _messageController.stream;
  bool _isConnected = false;

  // Connect to Reverb WebSocket
  Future<void> connect() async {
    try {
      if (_isConnected) {
        print('WebSocket already connected');
        return;
      }

      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('auth_token');

      final uri = Uri.parse(wsUrl);
      _channel = WebSocketChannel.connect(uri);
      
      _isConnected = true;
      print('WebSocket connected');

      // Listen to messages
      _channel!.stream.listen(
        (message) {
          _handleMessage(message);
        },
        onError: (error) {
          print('WebSocket error: $error');
          _isConnected = false;
        },
        onDone: () {
          print('WebSocket connection closed');
          _isConnected = false;
          // Auto reconnect after 3 seconds
          Future.delayed(const Duration(seconds: 3), () {
            if (!_isConnected) connect();
          });
        },
      );

      // Subscribe to admin broadcast channel
      await Future.delayed(const Duration(milliseconds: 500));
      subscribeToAdminBroadcast();
      
    } catch (e) {
      print('WebSocket connection error: $e');
      _isConnected = false;
    }
  }

  // Handle incoming messages
  void _handleMessage(dynamic message) {
    try {
      final data = jsonDecode(message);
      print('WebSocket message received: $data');
      
      // Check if it's an event message
      if (data['event'] != null) {
        _messageController.add(data);
      }
    } catch (e) {
      print('Error parsing WebSocket message: $e');
    }
  }

  // Subscribe to admin broadcast channel
  void subscribeToAdminBroadcast() {
    if (_channel == null || !_isConnected) return;

    final subscribeMessage = jsonEncode({
      'event': 'pusher:subscribe',
      'data': {
        'channel': 'admin-broadcast',
      }
    });

    _channel!.sink.add(subscribeMessage);
    print('Subscribed to admin-broadcast channel');
  }

  // Disconnect
  void disconnect() {
    _channel?.sink.close();
    _isConnected = false;
    print('WebSocket disconnected');
  }

  void dispose() {
    disconnect();
    _messageController.close();
  }
}
```

### 4. Provider (State Management)

#### admin_chat_provider.dart
```dart
import 'package:flutter/foundation.dart';
import '../models/message.dart';
import '../models/conversation.dart';
import '../services/api_service.dart';
import '../services/websocket_service.dart';
import 'dart:async';

class AdminChatProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();
  final WebSocketService _wsService = WebSocketService();
  
  Conversation? _conversation;
  List<Message> _messages = [];
  bool _isLoading = false;
  String? _error;
  StreamSubscription? _wsSubscription;

  Conversation? get conversation => _conversation;
  List<Message> get messages => _messages;
  bool get isLoading => _isLoading;
  String? get error => _error;

  AdminChatProvider() {
    _initializeWebSocket();
  }

  // Initialize WebSocket connection
  void _initializeWebSocket() {
    _wsService.connect();
    
    // Listen to WebSocket messages
    _wsSubscription = _wsService.messageStream.listen((data) {
      _handleWebSocketMessage(data);
    });
  }

  // Handle WebSocket messages
  void _handleWebSocketMessage(Map<String, dynamic> data) {
    try {
      // Check if it's an admin message event
      if (data['event'] == 'admin-message') {
        final messageData = data['data'];
        
        // Create new message from broadcast
        final newMessage = Message(
          id: messageData['message_id'] ?? 0,
          conversationId: _conversation?.id ?? 1,
          senderId: messageData['admin_id'],
          message: messageData['message'],
          isRead: false,
          createdAt: messageData['timestamp'] ?? DateTime.now().toIso8601String(),
          sender: MessageSender(
            id: messageData['admin_id'],
            name: messageData['admin_name'] ?? 'Admin',
            email: null,
            profilePic: null,
          ),
        );

        // Add to messages list
        _messages.add(newMessage);
        notifyListeners();

        print('New admin message received: ${newMessage.message}');
      }
    } catch (e) {
      print('Error handling WebSocket message: $e');
    }
  }

  // Load admin conversation
  Future<void> loadAdminConversation() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final response = await _apiService.getAdminMessages();
      
      if (response['status'] == true) {
        // Parse conversation
        _conversation = Conversation.fromJson(response['conversation']);
        
        // Parse messages
        final messagesData = response['messages']['data'] as List;
        _messages = messagesData
            .map((msg) => Message.fromJson(msg))
            .toList();
        
        print('Loaded ${_messages.length} admin messages');
      }
    } catch (e) {
      _error = 'Failed to load admin messages: $e';
      print(_error);
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  // Refresh messages
  Future<void> refresh() async {
    await loadAdminConversation();
  }

  @override
  void dispose() {
    _wsSubscription?.cancel();
    _wsService.dispose();
    super.dispose();
  }
}
```

### 5. UI Screen

#### admin_chat_screen.dart
```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/admin_chat_provider.dart';
import '../widgets/message_bubble.dart';

class AdminChatScreen extends StatefulWidget {
  const AdminChatScreen({Key? key}) : super(key: key);

  @override
  State<AdminChatScreen> createState() => _AdminChatScreenState();
}

class _AdminChatScreenState extends State<AdminChatScreen> {
  @override
  void initState() {
    super.initState();
    // Load admin conversation on screen open
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<AdminChatProvider>().loadAdminConversation();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          children: [
            const Icon(Icons.campaign, color: Colors.white),
            const SizedBox(width: 8),
            const Text('Admin Announcements'),
          ],
        ),
        backgroundColor: Colors.deepPurple,
        actions: [
          Container(
            margin: const EdgeInsets.only(right: 16),
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(20),
            ),
            child: const Text(
              'OFFICIAL',
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
          ),
        ],
      ),
      body: Consumer<AdminChatProvider>(
        builder: (context, provider, child) {
          if (provider.isLoading && provider.messages.isEmpty) {
            return const Center(
              child: CircularProgressIndicator(),
            );
          }

          if (provider.error != null) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, size: 64, color: Colors.red),
                  const SizedBox(height: 16),
                  Text(provider.error!),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () => provider.refresh(),
                    child: const Text('Retry'),
                  ),
                ],
              ),
            );
          }

          if (provider.messages.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.inbox, size: 64, color: Colors.grey[400]),
                  const SizedBox(height: 16),
                  Text(
                    'No announcements yet',
                    style: TextStyle(fontSize: 16, color: Colors.grey[600]),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: provider.refresh,
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: provider.messages.length,
              itemBuilder: (context, index) {
                final message = provider.messages[index];
                return MessageBubble(message: message);
              },
            ),
          );
        },
      ),
    );
  }
}
```

### 6. Widgets

#### message_bubble.dart
```dart
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../models/message.dart';

class MessageBubble extends StatelessWidget {
  final Message message;

  const MessageBubble({Key? key, required this.message}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            Colors.deepPurple.shade50,
            Colors.deepPurple.shade100,
          ],
        ),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: Colors.deepPurple.shade200,
          width: 2,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              CircleAvatar(
                backgroundColor: Colors.deepPurple,
                child: Text(
                  message.sender?.name?.substring(0, 1).toUpperCase() ?? 'A',
                  style: const TextStyle(color: Colors.white),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Text(
                          message.sender?.name ?? 'Admin',
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 2,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.deepPurple,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: const Text(
                            'ADMIN',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 4),
                    Text(
                      _formatDate(message.createdAt),
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey[600],
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            message.message,
            style: const TextStyle(
              fontSize: 15,
              height: 1.4,
            ),
          ),
        ],
      ),
    );
  }

  String _formatDate(String dateStr) {
    try {
      final date = DateTime.parse(dateStr);
      final now = DateTime.now();
      final difference = now.difference(date);

      if (difference.inDays == 0) {
        return 'Today at ${DateFormat('HH:mm').format(date)}';
      } else if (difference.inDays == 1) {
        return 'Yesterday at ${DateFormat('HH:mm').format(date)}';
      } else if (difference.inDays < 7) {
        return DateFormat('EEEE at HH:mm').format(date);
      } else {
        return DateFormat('MMM dd, yyyy at HH:mm').format(date);
      }
    } catch (e) {
      return dateStr;
    }
  }
}
```

### 7. Main App Setup

#### main.dart
```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'providers/admin_chat_provider.dart';
import 'screens/admin_chat_screen.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AdminChatProvider()),
      ],
      child: MaterialApp(
        title: 'Hotel App',
        theme: ThemeData(
          primarySwatch: Colors.deepPurple,
          useMaterial3: true,
        ),
        home: const AdminChatScreen(),
      ),
    );
  }
}
```

## Configuration

### 1. Update API Base URL

In `api_service.dart`, change:
```dart
static const String baseUrl = 'http://YOUR_IP_ADDRESS/api';
```

For Android emulator: `http://10.0.2.2/api`
For iOS simulator: `http://localhost/api`
For real device: `http://192.168.x.x/api`

### 2. Update WebSocket URL

In `websocket_service.dart`, change:
```dart
static const String wsUrl = 'ws://YOUR_IP_ADDRESS:8080/app/bqefva00jou7erqcx7ob';
```

Use your Reverb app key from `.env` file.

## Generate Model Files

Run this command to generate JSON serialization code:
```bash
flutter pub run build_runner build --delete-conflicting-outputs
```

## Testing

### 1. Run the App
```bash
flutter run
```

### 2. Login Flow
```dart
// Example login in your login screen
final apiService = ApiService();

// Send OTP
await apiService.sendOtp('9876543210');

// Verify OTP
final response = await apiService.verifyOtp('9876543210', '1234');

// Token is automatically saved, navigate to AdminChatScreen
Navigator.push(
  context,
  MaterialPageRoute(builder: (_) => AdminChatScreen()),
);
```

### 3. Test Real-time Messages

1. Open your Flutter app
2. Go to admin panel: `http://localhost/admin/broadcast`
3. Send a message from admin
4. Message should appear in Flutter app instantly!

## Advanced Features

### Add Notification Badge

```dart
class DashboardScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Consumer<AdminChatProvider>(
      builder: (context, provider, child) {
        final unreadCount = provider.messages
            .where((msg) => !msg.isRead)
            .length;
            
        return ListTile(
          leading: const Icon(Icons.campaign),
          title: const Text('Admin Announcements'),
          trailing: unreadCount > 0
              ? CircleBadge(count: unreadCount)
              : null,
          onTap: () {
            Navigator.push(
              context,
              MaterialPageRoute(
                builder: (_) => AdminChatScreen(),
              ),
            );
          },
        );
      },
    );
  }
}
```

### Add Push Notifications

Add to `pubspec.yaml`:
```yaml
firebase_messaging: ^14.7.6
flutter_local_notifications: ^16.3.0
```

Handle background messages:
```dart
import 'package:firebase_messaging/firebase_messaging.dart';

Future<void> _firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  print('Background message: ${message.notification?.title}');
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  FirebaseMessaging.onBackgroundMessage(_firebaseMessagingBackgroundHandler);
  
  runApp(const MyApp());
}
```

## Troubleshooting

### WebSocket Not Connecting
- Check if Reverb server is running: `php artisan reverb:start`
- Verify WebSocket URL and port (default: 8080)
- Check firewall settings
- For Android, add to `AndroidManifest.xml`:
```xml
<uses-permission android:name="android.permission.INTERNET" />
```

### API Requests Failing
- Check base URL is correct
- Verify JWT token is saved in SharedPreferences
- Test API endpoints with Postman first
- Check Laravel logs: `storage/logs/laravel.log`

### Messages Not Appearing
- Check WebSocket connection status
- Verify channel name: `admin-broadcast`
- Check console logs for errors
- Ensure admin is sending messages with correct event name

## Production Checklist

- [ ] Use HTTPS for API requests
- [ ] Use WSS (secure WebSocket) for Reverb
- [ ] Store JWT token securely
- [ ] Handle token refresh
- [ ] Add error handling and retry logic
- [ ] Implement offline message queue
- [ ] Add message caching
- [ ] Test on multiple devices
- [ ] Add analytics tracking
- [ ] Implement proper error reporting

## Summary

Your Flutter app now has:
✅ Real-time admin broadcast messages
✅ JWT authentication
✅ WebSocket connection with auto-reconnect
✅ Beautiful UI with message bubbles
✅ State management with Provider
✅ Pull-to-refresh
✅ Error handling

Users will automatically see admin announcements in real-time when admin sends messages from the web panel!
