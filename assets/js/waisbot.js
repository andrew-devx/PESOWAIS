// Toggle Chat Window
function toggleChat() {
    const chatWindow = document.getElementById('waisBotWindow');
    chatWindow.classList.toggle('hidden');
    if(!chatWindow.classList.contains('hidden')) {
        document.getElementById('userMessage').focus();
    }
}

// Handle Enter Key
function handleEnter(e) {
    if (e.key === 'Enter') sendMessage();
}

// Send Message Logic
function sendMessage() {
    const input = document.getElementById('userMessage');
    const message = input.value.trim();
    if (!message) return;

    // 1. Show User Message
    appendMessage(message, 'user');
    input.value = '';

    // 2. Show Typing Indicator
    const typingId = showTypingIndicator();

    // 3. Send to Backend
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

// Add Message Bubble
function appendMessage(text, sender) {
    const chatBody = document.getElementById('chatBody');
    const isUser = sender === 'user';
    const messageClass = isUser ? 'chat-message chat-message--user chat-fade-in' : 'chat-message chat-message--bot chat-fade-in';
    const bubbleClass = isUser ? 'chat-bubble chat-bubble--user' : 'chat-bubble chat-bubble--bot';
    
    // Convert newlines to breaks for AI responses
    const formattedText = text.replace(/\n/g, '<br>');

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

// Typing Animation
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
    document.getElementById('chatBody').insertAdjacentHTML('beforeend', html);
    return id;
}

function removeTypingIndicator(id) {
    const el = document.getElementById(id);
    if(el) el.remove();
}