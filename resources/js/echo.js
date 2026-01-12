import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                // Get JWT token from localStorage or wherever you store it
                const token = localStorage.getItem('auth_token');
                
                window.axios.post('/api/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                }, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                })
                .then(response => {
                    callback(null, response.data);
                })
                .catch(error => {
                    callback(error);
                });
            }
        };
    },
});

// Listen to admin broadcast channel globally (public channel)
if (localStorage.getItem('auth_token')) {
    window.Echo.channel('admin-broadcast')
        .listen('.admin-message', (data) => {
            console.log('Admin broadcast received:', data);
            
            // Dispatch custom event for components to listen
            window.dispatchEvent(new CustomEvent('admin-message', { detail: data }));
            
            // Optional: Show notification
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('Admin Message', {
                    body: data.message,
                    icon: '/images/admin-icon.png'
                });
            }
        });
}
