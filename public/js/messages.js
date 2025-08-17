// Real-time message handling for Laravel Events
class MessageHandler {
    constructor() {
        console.log('MessageHandler constructor called');
        
        this.messagesContainer = document.getElementById('messages-container');
        this.messageForm = document.getElementById('message-form');
        this.messageInput = document.getElementById('message');
        this.sendButton = document.getElementById('send-button');
        
        console.log('Elements found:', {
            messagesContainer: !!this.messagesContainer,
            messageForm: !!this.messageForm,
            messageInput: !!this.messageInput,
            sendButton: !!this.sendButton
        });
        
        this.setupEventListeners();
        
        console.log('MessageHandler initialized successfully');
    }

    setupEventListeners() {
        // Listen for Enter key in message textarea
        if (this.messageInput) {
            this.messageInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && e.ctrlKey) {
                    e.preventDefault();
                    this.handleFormSubmit();
                }
            });
        }
    }

    async handleFormSubmit() {
        console.log('handleFormSubmit called');
        
        if (!this.messageInput || !this.sendButton) {
            console.error('Required elements not found:', {
                messageInput: !!this.messageInput,
                sendButton: !!this.sendButton
            });
            return;
        }

        const message = this.messageInput.value.trim();
        if (!message) {
            return;
        }

        console.log('Sending message:', message);

        // Disable button and show loading state
        this.sendButton.disabled = true;
        this.sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        try {
            const formData = new FormData();
            formData.append('message', message);
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            console.log('Sending request to /messages/send');

            const response = await fetch('/messages/send', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            console.log('Response received:', response.status, response.statusText);

            if (response.ok) {
                // Clear the message field
                this.messageInput.value = '';
                
                // Reset textarea height
                this.messageInput.style.height = '48px';
                
                // Add the sent message to display immediately
                const sentMessage = {
                    message: message,
                    timestamp: new Date().toISOString(),
                    user_id: 'You',
                    source: 'laravel'
                };
                this.addMessageToDisplay(sentMessage);
                
                console.log('Message sent successfully');
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to send message');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.showNotification(error.message || 'Failed to send message. Please try again.', 'error');
        } finally {
            // Re-enable button
            this.sendButton.disabled = false;
            this.sendButton.innerHTML = '<span>Send</span><i class="fas fa-arrow-right"></i>';
        }
    }

    addMessageToDisplay(data) {
        if (!this.messagesContainer) return;

        // Remove the welcome message if it exists
        const welcomeMessage = this.messagesContainer.querySelector('.text-center');
        if (welcomeMessage) {
            welcomeMessage.remove();
        }

        const messageDiv = document.createElement('div');
        
        let messageText = '';
        let timestamp = '';
        let userId = '';
        let source = '';
        
        if (typeof data === 'string') {
            messageText = data;
            timestamp = new Date().toLocaleTimeString();
            userId = 'Anonymous';
            source = 'unknown';
        } else if (data.message) {
            messageText = data.message;
            timestamp = data.timestamp ? new Date(data.timestamp).toLocaleTimeString() : new Date().toLocaleTimeString();
            userId = data.user_id || 'Anonymous';
            source = data.source || 'unknown';
        } else {
            messageText = JSON.stringify(data);
            timestamp = new Date().toLocaleTimeString();
            userId = 'Anonymous';
            source = 'unknown';
        }

        // Determine if this is a system message or user message
        const isSystemMessage = source === 'system' || userId === 'system';
        const isCurrentUser = userId === 'You' || userId === 'anonymous' || userId === 'Anonymous';

        if (isSystemMessage) {
            // System message styling
            messageDiv.className = 'flex justify-center my-4';
            messageDiv.innerHTML = `
                <div class="bg-gray-100 text-gray-600 px-4 py-2 rounded-full text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    ${this.escapeHtml(messageText)}
                </div>
            `;
        } else {
            // User message styling
            const messageAlignment = isCurrentUser ? 'justify-end' : 'justify-start';
            const bubbleColor = isCurrentUser ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800';
            const avatarColor = this.getAvatarColor(userId);
            
            messageDiv.className = `flex ${messageAlignment} mb-4`;
            messageDiv.innerHTML = `
                <div class="flex items-end space-x-2 max-w-xs lg:max-w-md">
                    ${!isCurrentUser ? `
                        <div class="w-8 h-8 ${avatarColor} rounded-full flex items-center justify-center text-white text-sm font-medium flex-shrink-0">
                            ${this.getInitials(userId)}
                        </div>
                    ` : ''}
                    <div class="flex flex-col">
                        <div class="flex items-center space-x-2 mb-1">
                            ${!isCurrentUser ? `
                                <span class="text-xs font-medium text-gray-600">${this.escapeHtml(userId)}</span>
                            ` : ''}
                            <span class="text-xs text-gray-400">${timestamp}</span>
                        </div>
                        <div class="px-4 py-2 ${bubbleColor} rounded-2xl ${isCurrentUser ? 'rounded-br-md' : 'rounded-bl-md'} shadow-sm">
                            <p class="text-sm">${this.escapeHtml(messageText)}</p>
                        </div>
                    </div>
                    ${isCurrentUser ? `
                        <div class="w-8 h-8 ${avatarColor} rounded-full flex items-center justify-center text-white text-sm font-medium flex-shrink-0">
                            ${this.getInitials(userId)}
                        </div>
                    ` : ''}
                </div>
            `;
        }
        
        // Add new message at the bottom
        this.messagesContainer.appendChild(messageDiv);
        
        // Auto-scroll to bottom
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        
        // Keep only last 100 messages
        const messages = this.messagesContainer.querySelectorAll('.flex');
        if (messages.length > 100) {
            messages[0].remove();
        }
    }

    getAvatarColor(userId) {
        // Generate consistent colors for user avatars
        const colors = [
            'bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500',
            'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500'
        ];
        
        let hash = 0;
        for (let i = 0; i < userId.length; i++) {
            hash = userId.charCodeAt(i) + ((hash << 5) - hash);
        }
        
        return colors[Math.abs(hash) % colors.length];
    }

    getInitials(userId) {
        if (!userId || userId === 'Anonymous' || userId === 'anonymous') {
            return '?';
        }
        
        if (userId === 'You') {
            return 'Y';
        }
        
        const words = userId.split(/[\s\-_]+/);
        if (words.length >= 2) {
            return (words[0][0] + words[1][0]).toUpperCase();
        }
        
        return userId.substring(0, 2).toUpperCase();
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, MessageHandler class defined');
});

// Also try to initialize immediately if DOM is already ready
if (document.readyState === 'loading') {
    console.log('DOM still loading, MessageHandler class defined');
} else {
    console.log('DOM already ready, MessageHandler class defined');
}
