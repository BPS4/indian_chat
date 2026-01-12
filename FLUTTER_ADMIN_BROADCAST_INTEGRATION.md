# Flutter Admin Broadcast Message Integration Guide

Complete guide to receive and display admin broadcast messages in your Flutter app.

---

## Table of Contents
1. [Dependencies Setup](#1-dependencies-setup)
2. [WebSocket Configuration](#2-websocket-configuration)
3. [Message Model](#3-message-model)
4. [WebSocket Service](#4-websocket-service)
5. [Message Provider (State Management)](#5-message-provider-state-management)
6. [Admin Conversation Screen](#6-admin-conversation-screen)
7. [Message Widget](#7-message-widget)
8. [Dashboard Integration](#8-dashboard-integration)
9. [Complete App Setup](#9-complete-app-setup)

---

## 1. Dependencies Setup

### pubspec.yaml
```yaml
name: your_app_name
description: Your app description

dependencies:
  flutter:
    sdk: flutter
  
  # HTTP & WebSocket
  http: ^1.1.0
  laravel_echo: ^0.3.0
  pusher_client: ^2.0.0
  
  # State Management
  provider: ^6.1.1
  
  # Storage
  shared_preferences: ^2.2.2
  
  # UI
  cached_network_image: ^3.3.0
  flutter_html: ^3.0.0-beta.2
  url_launcher: ^6.2.2
  video_player: ^2.8.1
  
  # Utilities
  intl: ^0.18.1
  
dev_dependencies:
  flutter_test:
    sdk: flutter
```

Run: `flutter pub get`

---

## 2. WebSocket Configuration

### lib/config/app_config.dart
```dart
class AppConfig {
  // API Configuration
  static const String baseUrl = 'http://your-domain.com'; // Change to your domain
  static const String apiUrl = '$baseUrl/api';
  
  // WebSocket Configuration (Laravel Reverb)
  static const String wsHost = 'localhost'; // or your domain without http://
  static const String wsPort = '8080';
  static const String wsAppId = '562045';
  static const String wsAppKey = 'bqefva00jou7erqcx7ob';
  static const String wsCluster = 'mt1';
  
  // API Endpoints
  static const String loginEndpoint = '$apiUrl/login';
  static const String registerEndpoint = '$apiUrl/register';
  static const String adminMessagesEndpoint = '$apiUrl/admin-messages';
  static const String broadcastAuthEndpoint = '$apiUrl/broadcasting/auth';
}
```

---

## 3. Message Model

### lib/models/admin_message_model.dart
```dart
class AdminMessage {
  final int id;
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
  final bool autoSend;
  final int? totalUsers;
  final DateTime createdAt;
  final DateTime updatedAt;
  final AdminSender? sender;

  AdminMessage({
    required this.id,
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
    this.autoSend = false,
    this.totalUsers,
    required this.createdAt,
    required this.updatedAt,
    this.sender,
  });

  factory AdminMessage.fromJson(Map<String, dynamic> json) {
    return AdminMessage(
      id: json['id'] ?? 0,
      description: json['message'] ?? json['description'] ?? '',
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
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
      sender: json['sender'] != null 
          ? AdminSender.fromJson(json['sender']) 
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'description': description,
      'media': media,
      'youtube_link': youtubeLink,
      'calling_number': callingNumber,
      'website_link': websiteLink,
      'instagram_link': instagramLink,
      'facebook_link': facebookLink,
      'telegram_link': telegramLink,
      'country': country,
      'state': state,
      'city': city,
      'auto_send': autoSend,
      'total_users': totalUsers,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }

  String get mediaUrl {
    if (media == null || media!.isEmpty) return '';
    return '${AppConfig.baseUrl}/storage/$media';
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

class AdminSender {
  final int id;
  final String name;
  final String? email;
  final String? profilePic;

  AdminSender({
    required this.id,
    required this.name,
    this.email,
    this.profilePic,
  });

  factory AdminSender.fromJson(Map<String, dynamic> json) {
    return AdminSender(
      id: json['id'] ?? 0,
      name: json['name'] ?? 'Admin',
      email: json['email'],
      profilePic: json['profile_pic'],
    );
  }

  String get profilePicUrl {
    if (profilePic == null || profilePic!.isEmpty) return '';
    return '${AppConfig.baseUrl}/storage/$profilePic';
  }
}
```

---

## 4. WebSocket Service

### lib/services/websocket_service.dart
```dart
import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:laravel_echo/laravel_echo.dart';
import 'package:pusher_client/pusher_client.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';
import '../models/admin_message_model.dart';

class WebSocketService {
  static WebSocketService? _instance;
  Echo? echo;
  PusherClient? pusher;
  
  // Callbacks
  Function(AdminMessage)? onAdminMessage;
  Function(String)? onConnectionStateChange;

  WebSocketService._internal();

  static WebSocketService get instance {
    _instance ??= WebSocketService._internal();
    return _instance!;
  }

  Future<void> connect() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('jwt_token');

      if (token == null || token.isEmpty) {
        if (kDebugMode) {
          print('❌ No JWT token found. User must login first.');
        }
        return;
      }

      // Initialize Pusher Client
      PusherOptions options = PusherOptions(
        host: AppConfig.wsHost,
        wsPort: int.parse(AppConfig.wsPort),
        encrypted: false, // Set true for wss:// (production)
        auth: PusherAuth(
          AppConfig.broadcastAuthEndpoint,
          headers: {
            'Authorization': 'Bearer $token',
            'Accept': 'application/json',
          },
        ),
        cluster: AppConfig.wsCluster,
      );

      pusher = PusherClient(
        AppConfig.wsAppKey,
        options,
        autoConnect: false,
        enableLogging: kDebugMode,
      );

      // Connection state monitoring
      pusher!.onConnectionStateChange((state) {
        if (kDebugMode) {
          print('🔌 Pusher connection state: ${state!.currentState}');
        }
        onConnectionStateChange?.call(state!.currentState);
      });

      pusher!.onConnectionError((error) {
        if (kDebugMode) {
          print('❌ Pusher connection error: ${error!.message}');
        }
      });

      // Connect
      pusher!.connect();

      // Initialize Laravel Echo
      echo = Echo(
        client: pusher,
        broadcaster: EchoBroadcasterType.Pusher,
      );

      // Subscribe to admin-broadcast channel
      _subscribeToAdminBroadcast();

      if (kDebugMode) {
        print('✅ WebSocket connected successfully');
      }
    } catch (e) {
      if (kDebugMode) {
        print('❌ WebSocket connection failed: $e');
      }
    }
  }

  void _subscribeToAdminBroadcast() {
    echo!
        .channel('admin-broadcast')
        .listen('.admin-message', (event) {
      if (kDebugMode) {
        print('📩 Received admin message: $event');
      }

      try {
        // Parse the message
        final messageData = {
          'id': event['messageId'],
          'message': event['message'],
          'description': event['message'],
          'created_at': DateTime.now().toIso8601String(),
          'updated_at': DateTime.now().toIso8601String(),
          'sender': {
            'id': event['adminId'],
            'name': event['adminName'] ?? 'Admin',
          }
        };

        final message = AdminMessage.fromJson(messageData);
        onAdminMessage?.call(message);

        if (kDebugMode) {
          print('✅ Admin message processed: ${message.description}');
        }
      } catch (e) {
        if (kDebugMode) {
          print('❌ Error processing admin message: $e');
        }
      }
    });

    if (kDebugMode) {
      print('✅ Subscribed to admin-broadcast channel');
    }
  }

  void disconnect() {
    if (echo != null) {
      echo!.leave('admin-broadcast');
      echo = null;
    }
    if (pusher != null) {
      pusher!.disconnect();
      pusher = null;
    }
    if (kDebugMode) {
      print('🔌 WebSocket disconnected');
    }
  }

  bool get isConnected {
    return pusher?.getConnection().currentState == 'connected';
  }
}
```

---

## 5. Message Provider (State Management)

### lib/providers/admin_message_provider.dart
```dart
import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';
import '../models/admin_message_model.dart';
import '../services/websocket_service.dart';

class AdminMessageProvider with ChangeNotifier {
  List<AdminMessage> _messages = [];
  bool _isLoading = false;
  String? _error;
  bool _isConnected = false;

  List<AdminMessage> get messages => _messages;
  bool get isLoading => _isLoading;
  String? get error => _error;
  bool get isConnected => _isConnected;

  final WebSocketService _wsService = WebSocketService.instance;

  AdminMessageProvider() {
    _initializeWebSocket();
  }

  void _initializeWebSocket() {
    // Set up callbacks
    _wsService.onAdminMessage = (message) {
      _addNewMessage(message);
    };

    _wsService.onConnectionStateChange = (state) {
      _isConnected = state == 'connected';
      notifyListeners();
    };

    // Connect
    _wsService.connect();
  }

  void _addNewMessage(AdminMessage message) {
    // Add to beginning of list
    _messages.insert(0, message);
    notifyListeners();

    if (kDebugMode) {
      print('✅ New admin message added to provider');
    }
  }

  Future<void> fetchAdminMessages() async {
    _isLoading = true;
    _error = null;
    notifyListeners();

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('jwt_token');

      if (token == null || token.isEmpty) {
        throw Exception('Not authenticated');
      }

      final response = await http.get(
        Uri.parse(AppConfig.adminMessagesEndpoint),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        
        if (data['success'] == true) {
          final messagesData = data['messages']['data'] as List;
          _messages = messagesData
              .map((json) => AdminMessage.fromJson(json))
              .toList();
          
          if (kDebugMode) {
            print('✅ Fetched ${_messages.length} admin messages');
          }
        } else {
          throw Exception('Failed to fetch messages');
        }
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      _error = e.toString();
      if (kDebugMode) {
        print('❌ Error fetching admin messages: $e');
      }
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void reconnect() {
    _wsService.connect();
  }

  @override
  void dispose() {
    _wsService.disconnect();
    super.dispose();
  }
}
```

---

## 6. Admin Conversation Screen

### lib/screens/admin_conversation_screen.dart
```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/admin_message_provider.dart';
import '../widgets/admin_message_widget.dart';

class AdminConversationScreen extends StatefulWidget {
  const AdminConversationScreen({Key? key}) : super(key: key);

  @override
  State<AdminConversationScreen> createState() => _AdminConversationScreenState();
}

class _AdminConversationScreenState extends State<AdminConversationScreen> {
  @override
  void initState() {
    super.initState();
    // Fetch messages when screen loads
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<AdminMessageProvider>().fetchAdminMessages();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Admin Announcements'),
        actions: [
          Consumer<AdminMessageProvider>(
            builder: (context, provider, _) {
              return IconButton(
                icon: Icon(
                  Icons.circle,
                  color: provider.isConnected ? Colors.green : Colors.red,
                  size: 12,
                ),
                onPressed: () {
                  if (!provider.isConnected) {
                    provider.reconnect();
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Reconnecting...')),
                    );
                  }
                },
              );
            },
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () {
              context.read<AdminMessageProvider>().fetchAdminMessages();
            },
          ),
        ],
      ),
      body: Consumer<AdminMessageProvider>(
        builder: (context, provider, _) {
          if (provider.isLoading && provider.messages.isEmpty) {
            return const Center(child: CircularProgressIndicator());
          }

          if (provider.error != null && provider.messages.isEmpty) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, size: 64, color: Colors.red),
                  const SizedBox(height: 16),
                  Text(
                    'Error: ${provider.error}',
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () => provider.fetchAdminMessages(),
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
                children: const [
                  Icon(Icons.message_outlined, size: 64, color: Colors.grey),
                  SizedBox(height: 16),
                  Text(
                    'No messages yet',
                    style: TextStyle(fontSize: 18, color: Colors.grey),
                  ),
                ],
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: () => provider.fetchAdminMessages(),
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: provider.messages.length,
              itemBuilder: (context, index) {
                return AdminMessageWidget(
                  message: provider.messages[index],
                );
              },
            ),
          );
        },
      ),
    );
  }
}
```

---

## 7. Message Widget

### lib/widgets/admin_message_widget.dart
```dart
import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:intl/intl.dart';
import '../models/admin_message_model.dart';

class AdminMessageWidget extends StatelessWidget {
  final AdminMessage message;

  const AdminMessageWidget({Key? key, required this.message}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header - Admin info
            Row(
              children: [
                CircleAvatar(
                  backgroundColor: Colors.blue,
                  child: Text(
                    message.sender?.name.substring(0, 1).toUpperCase() ?? 'A',
                    style: const TextStyle(color: Colors.white),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        message.sender?.name ?? 'Admin',
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                        ),
                      ),
                      Text(
                        DateFormat('MMM dd, yyyy • hh:mm a').format(message.createdAt),
                        style: TextStyle(
                          color: Colors.grey[600],
                          fontSize: 12,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),

            // Description
            Text(
              message.description,
              style: const TextStyle(fontSize: 15),
            ),
            const SizedBox(height: 12),

            // Media
            if (message.isImage)
              ClipRRect(
                borderRadius: BorderRadius.circular(8),
                child: CachedNetworkImage(
                  imageUrl: message.mediaUrl,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  placeholder: (context, url) => const Center(
                    child: CircularProgressIndicator(),
                  ),
                  errorWidget: (context, url, error) => const Icon(Icons.error),
                ),
              ),

            if (message.isVideo)
              Container(
                height: 200,
                decoration: BoxDecoration(
                  color: Colors.grey[300],
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Center(
                  child: Icon(Icons.play_circle_outline, size: 64),
                ),
              ),

            // Location info
            if (message.state != null || message.city != null)
              Padding(
                padding: const EdgeInsets.only(top: 12),
                child: Row(
                  children: [
                    const Icon(Icons.location_on, size: 16, color: Colors.grey),
                    const SizedBox(width: 4),
                    Text(
                      [message.city, message.state, message.country]
                          .where((e) => e != null && e.isNotEmpty)
                          .join(', '),
                      style: TextStyle(color: Colors.grey[700], fontSize: 13),
                    ),
                  ],
                ),
              ),

            // Action buttons
            const SizedBox(height: 16),
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children: [
                if (message.callingNumber != null)
                  _ActionButton(
                    icon: Icons.phone,
                    label: 'Call',
                    onTap: () => _launchUrl('tel:${message.callingNumber}'),
                  ),
                if (message.websiteLink != null)
                  _ActionButton(
                    icon: Icons.language,
                    label: 'Website',
                    onTap: () => _launchUrl(message.websiteLink!),
                  ),
                if (message.youtubeLink != null)
                  _ActionButton(
                    icon: Icons.play_circle_outline,
                    label: 'YouTube',
                    onTap: () => _launchUrl(message.youtubeLink!),
                  ),
                if (message.instagramLink != null)
                  _ActionButton(
                    icon: Icons.camera_alt,
                    label: 'Instagram',
                    onTap: () => _launchUrl(message.instagramLink!),
                  ),
                if (message.facebookLink != null)
                  _ActionButton(
                    icon: Icons.facebook,
                    label: 'Facebook',
                    onTap: () => _launchUrl(message.facebookLink!),
                  ),
                if (message.telegramLink != null)
                  _ActionButton(
                    icon: Icons.telegram,
                    label: 'Telegram',
                    onTap: () => _launchUrl(message.telegramLink!),
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _launchUrl(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }
}

class _ActionButton extends StatelessWidget {
  final IconData icon;
  final String label;
  final VoidCallback onTap;

  const _ActionButton({
    Key? key,
    required this.icon,
    required this.label,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        decoration: BoxDecoration(
          border: Border.all(color: Colors.blue),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 16, color: Colors.blue),
            const SizedBox(width: 4),
            Text(
              label,
              style: const TextStyle(color: Colors.blue, fontSize: 13),
            ),
          ],
        ),
      ),
    );
  }
}
```

---

## 8. Dashboard Integration

### lib/screens/dashboard_screen.dart
```dart
import 'package:flutter/material.dart';
import 'admin_conversation_screen.dart';

class DashboardScreen extends StatelessWidget {
  const DashboardScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // Admin Messages Card - Show by default
            Card(
              elevation: 4,
              child: InkWell(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => const AdminConversationScreen(),
                    ),
                  );
                },
                child: Padding(
                  padding: const EdgeInsets.all(20),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.blue.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Icon(
                          Icons.campaign,
                          size: 32,
                          color: Colors.blue,
                        ),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: const [
                            Text(
                              'Admin Announcements',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            SizedBox(height: 4),
                            Text(
                              'View important updates from admin',
                              style: TextStyle(
                                color: Colors.grey,
                                fontSize: 14,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const Icon(Icons.arrow_forward_ios),
                    ],
                  ),
                ),
              ),
            ),
            
            // Add other dashboard items here
          ],
        ),
      ),
    );
  }
}
```

---

## 9. Complete App Setup

### lib/main.dart
```dart
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'providers/admin_message_provider.dart';
import 'screens/dashboard_screen.dart';
import 'screens/login_screen.dart'; // Your login screen
import 'package:shared_preferences/shared_preferences.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AdminMessageProvider()),
        // Add other providers here
      ],
      child: MaterialApp(
        title: 'Your App Name',
        theme: ThemeData(
          primarySwatch: Colors.blue,
          visualDensity: VisualDensity.adaptivePlatformDensity,
        ),
        home: const SplashScreen(),
        debugShowCheckedModeBanner: false,
      ),
    );
  }
}

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    _checkAuthStatus();
  }

  Future<void> _checkAuthStatus() async {
    await Future.delayed(const Duration(seconds: 2));
    
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('jwt_token');
    
    if (!mounted) return;
    
    if (token != null && token.isNotEmpty) {
      // User is logged in, go to dashboard
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const DashboardScreen()),
      );
    } else {
      // User not logged in, go to login
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      body: Center(
        child: CircularProgressIndicator(),
      ),
    );
  }
}
```

### lib/screens/login_screen.dart (Example)
```dart
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../config/app_config.dart';
import 'dashboard_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;

  Future<void> _login() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    try {
      final response = await http.post(
        Uri.parse(AppConfig.loginEndpoint),
        headers: {'Accept': 'application/json'},
        body: {
          'email': _emailController.text,
          'password': _passwordController.text,
        },
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['token'] != null) {
        // Save JWT token
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('jwt_token', data['token']);
        await prefs.setInt('user_id', data['user']['id']);
        await prefs.setString('user_name', data['user']['name']);

        if (!mounted) return;

        // Navigate to dashboard
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => const DashboardScreen()),
        );
      } else {
        throw Exception(data['message'] ?? 'Login failed');
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e')),
      );
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Form(
            key: _formKey,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text(
                  'Welcome Back',
                  style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 40),
                TextFormField(
                  controller: _emailController,
                  decoration: const InputDecoration(
                    labelText: 'Email',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.email),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Please enter email';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: _passwordController,
                  obscureText: true,
                  decoration: const InputDecoration(
                    labelText: 'Password',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.lock),
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Please enter password';
                    }
                    return null;
                  },
                ),
                const SizedBox(height: 24),
                SizedBox(
                  width: double.infinity,
                  height: 50,
                  child: ElevatedButton(
                    onPressed: _isLoading ? null : _login,
                    child: _isLoading
                        ? const CircularProgressIndicator(color: Colors.white)
                        : const Text('Login', style: TextStyle(fontSize: 16)),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
```

---

## Usage Flow

1. **User logs in** → JWT token saved
2. **App navigates to Dashboard** → AdminMessageProvider initializes WebSocket
3. **Dashboard shows "Admin Announcements" card** → Always visible by default
4. **User taps card** → Opens AdminConversationScreen
5. **Screen fetches past messages** → Shows all previous admin broadcasts
6. **Admin sends new message** → Instantly received via WebSocket and displayed in real-time

---

## Testing

### 1. Start Reverb Server
```bash
php artisan reverb:start
```

### 2. Test Admin Broadcast
- Go to admin panel: `http://localhost/admin/message/add`
- Fill form and send message
- Check Flutter app - message should appear instantly

### 3. Debug WebSocket Connection
```dart
// In your Flutter app, check logs:
// ✅ WebSocket connected successfully
// ✅ Subscribed to admin-broadcast channel
// 📩 Received admin message: {...}
// ✅ Admin message processed: Your message text
```

---

## Troubleshooting

### WebSocket not connecting
1. Check Reverb is running on port 8080
2. Verify `AppConfig.wsHost` and `wsPort` are correct
3. Check JWT token is saved: `SharedPreferences.getInstance().then((p) => print(p.getString('jwt_token')))`

### Messages not appearing
1. Check API endpoint returns data: `curl http://your-domain.com/api/admin-messages -H "Authorization: Bearer YOUR_TOKEN"`
2. Verify `AdminMessage.fromJson()` matches your API response structure
3. Check provider is added to `main.dart`: `ChangeNotifierProvider(create: (_) => AdminMessageProvider())`

### Real-time not working
1. Ensure Reverb server is running
2. Check browser console for WebSocket errors
3. Verify channel name is `admin-broadcast` and event is `.admin-message`
4. Test with: `echo.js` should show connection in browser dev tools

---

## Production Checklist

- [ ] Change `AppConfig.baseUrl` to production domain
- [ ] Change `AppConfig.wsHost` to production domain (without http://)
- [ ] Set `encrypted: true` in PusherOptions for wss://
- [ ] Use environment variables for sensitive config
- [ ] Test on physical device, not just emulator
- [ ] Add error handling for network failures
- [ ] Implement notification for new messages when app is in background
- [ ] Add message caching for offline access

---

## Additional Features to Consider

1. **Push Notifications** - Use Firebase Cloud Messaging to notify users of new messages
2. **Message Search** - Add search functionality to find specific messages
3. **Filters** - Filter messages by state/city
4. **Mark as Read** - Track which messages user has seen
5. **Share** - Allow users to share messages via social media
6. **Pagination** - Load more messages on scroll

---

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Check Reverb output in terminal
- Enable Flutter debug mode: `flutter run -v`
- Check API responses with Postman

---

**Last Updated:** January 11, 2026
