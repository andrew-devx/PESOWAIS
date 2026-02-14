document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const chatWindow = document.getElementById('waisBotWindow');
    const userMessageInput = document.getElementById('userMessage');
    const sendButton = document.querySelector('.waisbot-send');
    const fabButton = document.querySelector('.waisbot-fab');
    const closeButton = document.querySelector('.waisbot-close');
    const chatBody = document.getElementById('chatBody');
    const recommendedChips = document.querySelectorAll('.waisbot-chip');

    // Toggle Chat
    function toggleChat() {
        chatWindow.classList.toggle('hidden');
        if (!chatWindow.classList.contains('hidden')) {
            userMessageInput.focus();
        }
    }

    if (fabButton) fabButton.addEventListener('click', toggleChat);
    if (closeButton) closeButton.addEventListener('click', toggleChat);

    // Send Message
    function sendMessage() {
        const message = userMessageInput.value.trim();
        if (!message) return;

        // 1. Show User Message
        appendMessage(message, 'user');
        userMessageInput.value = '';

        // 2. Hide recommended chips if they exist
        const recommended = document.querySelector('.waisbot-recommended');
        if (recommended && recommended.style.display !== 'none') {
            recommended.style.opacity = '0';
            recommended.style.transform = 'scale(0.95)';
            setTimeout(() => {
                recommended.style.display = 'none';
            }, 200);
        }

        // 3. Show Typing Indicator
        const typingId = showTypingIndicator();

        // 4. Send to Backend
        const formData = new FormData();
        formData.append('message', message);

        fetch('logic/ask_waisbot.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                removeTypingIndicator(typingId);
                const modelInfo = data.model ? ` _(${data.model})_` : '';
                appendMessage(data.reply + modelInfo, 'bot');
            })
            .catch(error => {
                removeTypingIndicator(typingId);
                appendMessage("Sorry, I can't connect right now.", 'bot');
            });
    }

    if (sendButton) sendButton.addEventListener('click', sendMessage);
    if (userMessageInput) {
        userMessageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    }

    // Recommended Chips
    recommendedChips.forEach(chip => {
        chip.addEventListener('click', function () {
            // Get the text from the onclick attribute in the original HTML 
            // or we can infer it. 
            // Better strategy: The refactored HTML will use data-question attribute
            const question = this.dataset.question;
            if (question) {
                userMessageInput.value = question;
                sendMessage();
            }
        });
    });

    // Helper: Append Message
    function appendMessage(text, sender) {
        const isUser = sender === 'user';
        const messageClass = isUser ? 'chat-message chat-message--user chat-fade-in' : 'chat-message chat-message--bot chat-fade-in';
        const bubbleClass = isUser ? 'chat-bubble chat-bubble--user' : 'chat-bubble chat-bubble--bot';

        // Parse simple markdown
        const formattedText = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Bold
            .replace(/\*(.*?)\*/g, '<em>$1</em>')             // Italic
            .replace(/`(.*?)`/g, '<code class="bg-gray-200 px-1 rounded text-sm font-mono text-red-600">$1</code>') // Inline Code
            .replace(/\n/g, '<br>');

        const html = `
            <div class="${messageClass}">
                <div class="${bubbleClass}">
                    <p class="chat-text">${formattedText}</p>
                </div>
            </div>
        `;
        chatBody.insertAdjacentHTML('beforeend', html);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // Helper: Typing Animation
    function showTypingIndicator() {
        const id = 'typing-' + Date.now();
        const html = `
            <div id="${id}" class="chat-message chat-message--bot chat-fade-in">
                <div class="chat-bubble chat-bubble--bot chat-typing-bubble">
                    <div class="typing-dots">
                        <span class="typing-dot typing-dot--1"></span>
                        <span class="typing-dot typing-dot--2"></span>
                        <span class="typing-dot typing-dot--3"></span>
                    </div>
                </div>
            </div>`;
        chatBody.insertAdjacentHTML('beforeend', html);
        chatBody.scrollTop = chatBody.scrollHeight;
        return id;
    }

    function removeTypingIndicator(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }
});