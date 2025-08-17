<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Laravel Echo Chat - laravel-events</title>
    <style>
        body {
            margin: 0;
            padding-bottom: 3rem;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        #form {
            background: rgba(0, 0, 0, 0.15);
            padding: 0.25rem;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            height: 3rem;
            box-sizing: border-box;
            backdrop-filter: blur(10px);
        }
        #input {
            border: none;
            padding: 0 1rem;
            flex-grow: 1;
            border-radius: 2rem;
            margin: 0.25rem;
        }
        #input:focus { outline: none; }
        #form > button {
            background: #333;
            border: none;
            padding: 0 1rem;
            margin: 0.25rem;
            border-radius: 3px;
            outline: none;
            color: #fff;
        }

        #messages {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        #messages > li {
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            max-width: 70%;
            border-radius: 10px;
            clear: both;
        }

        /* My message (right side) */
        .my-message {
            background: #007bff;
            color: white;
            float: right;
            text-align: right;
        }

        /* Received message (left side) */
        .other-message {
            background: #efefef;
            color: black;
            float: left;
            text-align: left;
        }

        /* Connection status */
        .status-bar {
            background: rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #333;
        }

        .status-connected {
            background: rgba(0, 255, 0, 0.2);
            color: #006400;
        }

        .status-disconnected {
            background: rgba(255, 0, 0, 0.2);
            color: #8B0000;
        }

        .status-connecting {
            background: rgba(255, 255, 0, 0.2);
            color: #8B8B00;
        }
    </style>
</head>
<body>

    <ul id="messages"></ul>

    <form id="form" action="">
        <input id="input" autocomplete="off" placeholder="Type your message..." />
        <button type="submit">Send</button>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.4.0/socket.io.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

    <script>
        const form = document.getElementById('form');
        const input = document.getElementById('input');
        const messages = document.getElementById('messages');
        const statusBar = document.getElementById('status-bar');

        let echo = null;
        let isConnected = false;

        // Initialize Laravel Echo
        // function initializeEcho() {
        //     try {
        //         // Check if Socket.IO is available
        //         if (typeof io === 'undefined') {
        //             console.log('Socket.IO not available, trying alternative...');
                    
        //             if (typeof window.io !== 'undefined') {
        //                 console.log('Found Socket.IO in window.io');
        //                 window.io = window.io;
        //             } else {
        //                 console.log('Socket.IO not found anywhere');
        //                 updateStatus('Socket.IO not loaded', 'disconnected');
        //                 return;
        //             }
        //         }

        //         // Check if Echo is available
        //         if (typeof Echo === 'undefined') {
        //             console.log('Laravel Echo not available');
        //             updateStatus('Laravel Echo not loaded', 'disconnected');
        //             return;
        //         }

        //         console.log('Socket.IO and Echo available, initializing...');
        //         console.log('Socket.IO version:', io.version);
        //         console.log('Echo version:', Echo.version);

        //         // Initialize Echo with Socket.IO v2 compatibility
        //         echo = new Echo({
        //             broadcaster: 'socket.io',
        //             host: window.location.hostname + ':6001',
        //             transports: ['websocket', 'polling'],
        //             forceTLS: false,
        //             // Socket.IO v2 compatibility options
        //             path: '/socket.io',
        //             upgrade: true,
        //             rememberUpgrade: true,
        //             // Explicitly pass the Socket.IO client
        //             client: io
        //         });

        //         // Listen for connection events
        //         echo.connector.socket.on('connect', () => {
        //             console.log('WebSocket connected successfully');
        //             isConnected = true;
        //             updateStatus('Connected to laravel-events channel', 'connected');
                    
        //             // Setup Echo listeners for laravel-events channel
        //             setupEchoListeners();
        //         });

        //         echo.connector.socket.on('connect_error', (error) => {
        //             console.log('WebSocket connection failed:', error);
        //             isConnected = false;
        //             updateStatus('Failed to connect - Start Laravel Echo Server', 'disconnected');
        //         });

        //         echo.connector.socket.on('disconnect', () => {
        //             console.log('WebSocket disconnected');
        //             isConnected = false;
        //             updateStatus('Disconnected from laravel-events channel', 'disconnected');
        //         });

        //         // Set a timeout for connection
        //         setTimeout(() => {
        //             if (!isConnected) {
        //                 console.log('WebSocket connection timeout');
        //                 updateStatus('Connection timeout - Start Laravel Echo Server on port 6001', 'disconnected');
        //             }
        //         }, 6000);

        //     } catch (error) {
        //         console.error('Error initializing Echo:', error);
        //         updateStatus('Error initializing connection: ' + error.message, 'disconnected');
        //     }
        // }

        // // Setup Echo listeners for laravel-events channel
        // function setupEchoListeners() {
        //     if (!echo || !isConnected) return;

        //     try {
        //         // Listen to the laravel-events channel
        //         echo.channel('laravel-events')
        //             .listen('.message', (e) => {
        //                 console.log('Message received via Echo .message:', e);
        //                 appendMessage(e.message || e, 'other-message');
        //             })
        //             .listen('MessageSent', (e) => {
        //                 console.log('MessageSent event received:', e);
        //                 appendMessage(e.message || e, 'other-message');
        //             });

        //         // Listen for direct Redis messages on laravel-events channel
        //         echo.connector.socket.on('laravel-events', (data) => {
        //             console.log('Direct Redis message received on laravel-events:', data);
        //             appendMessage(data.message || data, 'other-message');
        //         });

        //         // Listen for any socket events (Socket.IO v2 compatible)
        //         echo.connector.socket.on('message', (data) => {
        //             console.log('Raw socket message received:', data);
        //             if (data.message) {
        //                 appendMessage(data.message, 'other-message');
        //             }
        //         });

        //     } catch (error) {
        //         console.error('Error setting up Echo listeners:', error);
        //     }
        // }

        // // Update status bar
        // function updateStatus(message, status) {
        //     statusBar.textContent = message;
        //     statusBar.className = `status-bar status-${status}`;
        // }

        // Send message form handler
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (input.value.trim()) {
                const message = input.value.trim();
                
                // Show my message on the right immediately
                appendMessage(message, 'my-message');

                // Send to Laravel backend
                try {
                    const formData = new FormData();
                    formData.append('message', message);
                    formData.append('_token', '{{ csrf_token() }}');

                    const response = await fetch('/messages/send', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to send message');
                    }

                    console.log('Message sent successfully to Laravel');
                    
                } catch (error) {
                    console.error('Error sending message:', error);
                    // Show error message
                    appendMessage('Error: Failed to send message', 'other-message');
                }

                // Clear input
                input.value = '';
            }
        });

        // Append message to chat
        function appendMessage(msg, className) {
            const item = document.createElement('li');
            item.textContent = msg;
            item.classList.add(className);
            messages.appendChild(item);
            
            // Scroll to bottom
            messages.scrollTop = messages.scrollHeight;
        }

        // Initialize when page loads
        // document.addEventListener('DOMContentLoaded', function() {
        //     console.log('DOM loaded, initializing Laravel Echo...');
        //     initializeEcho();
        // });

        // Add welcome message
        // setTimeout(() => {
        //     appendMessage('Welcome to Laravel Echo Chat on #laravel-events channel!', 'other-message');
        // }, 1000);
    </script>
</body>
</html>
