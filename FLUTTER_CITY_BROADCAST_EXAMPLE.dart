// Flutter City-Wise Broadcast Integration
// This file shows how to integrate city-specific broadcasts in your Flutter app

import 'package:flutter/material.dart';
import 'package:laravel_echo/laravel_echo.dart';
import 'package:pusher_client/pusher_client.dart';

/// Broadcast Service for handling city-specific admin messages
class BroadcastService {
  static BroadcastService? _instance;
  Echo? _echo;
  String? _currentCity;
  Channel? _broadcastChannel;

  // Singleton pattern
  factory BroadcastService() {
    _instance ??= BroadcastService._internal();
    return _instance!;
  }

  BroadcastService._internal();

  /// Initialize Echo with Reverb/Pusher configuration
  Future<void> initialize({
    required String appKey,
    required String host,
    required int port,
    required String scheme,
    String? authToken,
  }) async {
    try {
      PusherOptions options = PusherOptions(
        host: host,
        wsPort: port,
        encrypted: scheme == 'https',
        auth: PusherAuth(
          '$scheme://$host/api/broadcasting/auth',
          headers: {
            'Authorization': 'Bearer $authToken',
            'Accept': 'application/json',
          },
        ),
      );

      PusherClient pusherClient = PusherClient(
        appKey,
        options,
        autoConnect: false,
      );

      _echo = Echo(
        client: pusherClient,
        broadcaster: EchoBroadcasterType.Pusher,
      );

      await _echo?.connect();
      
      print('✅ Broadcast service initialized');
    } catch (e) {
      print('❌ Error initializing broadcast service: $e');
    }
  }

  /// Sanitize city name to match backend format
  String _sanitizeCityName(String city) {
    return city
        .toLowerCase()
        .trim()
        .replaceAll(RegExp(r'[ \-,.]'), '_');
  }

  /// Subscribe to city-specific broadcast channel
  Future<void> subscribeToCityBroadcast({
    required String city,
    required Function(AdminBroadcastMessage) onMessage,
  }) async {
    if (_echo == null) {
      print('❌ Echo not initialized. Call initialize() first.');
      return;
    }

    // Unsubscribe from previous channel if exists
    if (_broadcastChannel != null && _currentCity != null) {
      await unsubscribe();
    }

    _currentCity = city;
    final channelName = 'admin-broadcast.${_sanitizeCityName(city)}';

    print('📡 Subscribing to channel: $channelName');

    _broadcastChannel = _echo!.channel(channelName);

    _broadcastChannel!.listen('admin-message', (event) {
      print('📨 Received admin message: ${event.data}');
      
      try {
        final message = AdminBroadcastMessage.fromJson(event.data);
        onMessage(message);
      } catch (e) {
        print('❌ Error parsing message: $e');
      }
    });

    print('✅ Subscribed to $channelName');
  }

  /// Unsubscribe from current broadcast channel
  Future<void> unsubscribe() async {
    if (_broadcastChannel != null && _currentCity != null) {
      final channelName = 'admin-broadcast.${_sanitizeCityName(_currentCity!)}';
      _echo?.leave(channelName);
      _broadcastChannel = null;
      _currentCity = null;
      print('👋 Unsubscribed from city broadcast');
    }
  }

  /// Disconnect from Echo
  Future<void> disconnect() async {
    await unsubscribe();
    _echo?.disconnect();
    _echo = null;
    print('🔌 Disconnected from broadcast service');
  }
}

/// Model for Admin Broadcast Message
class AdminBroadcastMessage {
  final int messageId;
  final String description;
  final String? media;
  final BroadcastLinks links;
  final String adminName;
  final String targetCity;
  final String? state;
  final String? country;
  final DateTime timestamp;
  final String broadcastType;

  AdminBroadcastMessage({
    required this.messageId,
    required this.description,
    this.media,
    required this.links,
    required this.adminName,
    required this.targetCity,
    this.state,
    this.country,
    required this.timestamp,
    required this.broadcastType,
  });

  factory AdminBroadcastMessage.fromJson(Map<String, dynamic> json) {
    return AdminBroadcastMessage(
      messageId: json['message_id'] ?? 0,
      description: json['description'] ?? '',
      media: json['media'],
      links: BroadcastLinks.fromJson(json['links'] ?? {}),
      adminName: json['admin_name'] ?? 'Admin',
      targetCity: json['target_city'] ?? '',
      state: json['state'],
      country: json['country'],
      timestamp: DateTime.parse(json['timestamp']),
      broadcastType: json['broadcast_type'] ?? 'specific_city',
    );
  }
}

/// Links attached to broadcast message
class BroadcastLinks {
  final String? youtube;
  final String? website;
  final String? instagram;
  final String? facebook;
  final String? telegram;
  final String? call;

  BroadcastLinks({
    this.youtube,
    this.website,
    this.instagram,
    this.facebook,
    this.telegram,
    this.call,
  });

  factory BroadcastLinks.fromJson(Map<String, dynamic> json) {
    return BroadcastLinks(
      youtube: json['youtube'],
      website: json['website'],
      instagram: json['instagram'],
      facebook: json['facebook'],
      telegram: json['telegram'],
      call: json['call'],
    );
  }

  bool get hasAnyLink {
    return youtube != null ||
        website != null ||
        instagram != null ||
        facebook != null ||
        telegram != null ||
        call != null;
  }
}

/// Example Widget: Broadcast Notification Display
class BroadcastNotificationWidget extends StatefulWidget {
  final String userCity;

  const BroadcastNotificationWidget({
    Key? key,
    required this.userCity,
  }) : super(key: key);

  @override
  State<BroadcastNotificationWidget> createState() =>
      _BroadcastNotificationWidgetState();
}

class _BroadcastNotificationWidgetState
    extends State<BroadcastNotificationWidget> {
  final BroadcastService _broadcastService = BroadcastService();
  final List<AdminBroadcastMessage> _messages = [];

  @override
  void initState() {
    super.initState();
    _setupBroadcast();
  }

  Future<void> _setupBroadcast() async {
    // Get auth token from your storage/provider
    final authToken = await _getAuthToken();

    // Initialize broadcast service
    await _broadcastService.initialize(
      appKey: 'your-reverb-app-key',
      host: 'your-server-host.com',
      port: 8080,
      scheme: 'https',
      authToken: authToken,
    );

    // Subscribe to city-specific broadcasts
    await _broadcastService.subscribeToCityBroadcast(
      city: widget.userCity,
      onMessage: (message) {
        setState(() {
          _messages.insert(0, message);
        });

        // Show notification
        _showNotification(message);
      },
    );
  }

  Future<String> _getAuthToken() async {
    // Replace with your actual token retrieval logic
    return 'your-jwt-token';
  }

  void _showNotification(AdminBroadcastMessage message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              'Message from ${message.adminName}',
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 4),
            Text(message.description),
          ],
        ),
        action: SnackBarAction(
          label: 'View',
          onPressed: () => _showMessageDetails(message),
        ),
        duration: const Duration(seconds: 5),
      ),
    );
  }

  void _showMessageDetails(AdminBroadcastMessage message) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Message from ${message.adminName}'),
        content: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              if (message.media != null)
                Image.network(
                  'https://your-server.com/storage/${message.media}',
                  height: 200,
                  fit: BoxFit.cover,
                ),
              const SizedBox(height: 16),
              Text(message.description),
              const SizedBox(height: 16),
              if (message.links.hasAnyLink) ...[
                const Text(
                  'Quick Links:',
                  style: TextStyle(fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                if (message.links.call != null)
                  _buildLinkButton(
                    'Call',
                    Icons.phone,
                    'tel:${message.links.call}',
                  ),
                if (message.links.youtube != null)
                  _buildLinkButton(
                    'YouTube',
                    Icons.play_circle,
                    message.links.youtube!,
                  ),
                if (message.links.website != null)
                  _buildLinkButton(
                    'Website',
                    Icons.language,
                    message.links.website!,
                  ),
              ],
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Close'),
          ),
        ],
      ),
    );
  }

  Widget _buildLinkButton(String label, IconData icon, String url) {
    return ElevatedButton.icon(
      onPressed: () {
        // Launch URL using url_launcher package
        // launch(url);
      },
      icon: Icon(icon),
      label: Text(label),
      style: ElevatedButton.styleFrom(
        minimumSize: const Size(double.infinity, 40),
      ),
    );
  }

  @override
  void dispose() {
    _broadcastService.disconnect();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return ListView.builder(
      itemCount: _messages.length,
      itemBuilder: (context, index) {
        final message = _messages[index];
        return Card(
          margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: ListTile(
            leading: const CircleAvatar(
              child: Icon(Icons.campaign),
            ),
            title: Text(message.description),
            subtitle: Text(
              '${message.adminName} • ${_formatTimestamp(message.timestamp)}',
            ),
            trailing: message.links.hasAnyLink
                ? const Icon(Icons.link)
                : null,
            onTap: () => _showMessageDetails(message),
          ),
        );
      },
    );
  }

  String _formatTimestamp(DateTime timestamp) {
    final now = DateTime.now();
    final difference = now.difference(timestamp);

    if (difference.inMinutes < 1) {
      return 'Just now';
    } else if (difference.inHours < 1) {
      return '${difference.inMinutes}m ago';
    } else if (difference.inDays < 1) {
      return '${difference.inHours}h ago';
    } else {
      return '${difference.inDays}d ago';
    }
  }
}

/// Example: How to use in your app
/// 
/// ```dart
/// // In your main app or profile screen
/// class HomePage extends StatelessWidget {
///   final String userCity;
/// 
///   const HomePage({required this.userCity});
/// 
///   @override
///   Widget build(BuildContext context) {
///     return Scaffold(
///       appBar: AppBar(title: const Text('Home')),
///       body: BroadcastNotificationWidget(userCity: userCity),
///     );
///   }
/// }
/// ```
