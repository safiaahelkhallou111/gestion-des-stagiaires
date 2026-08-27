/**
 * AJAX Real-time Chat Engine - WhatsApp Style (Exact Match)
 */

function initChat(stageId, currentUserId) {
    const chatContainer = document.getElementById('chat-container');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');

    if (!chatContainer || !chatForm || !messageInput) return;

    let isFirstLoad = true;
    let lastMessageCount = 0;

    function getApiUrl(path) {
        const base = (typeof BASE_URL !== 'undefined' ? BASE_URL : '').replace(/\/+$/, '');
        return base + '/' + path.replace(/^\/+/, '');
    }

    function scrollToBottom(force = false) {
        if (force || isUserNearBottom()) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }

    function isUserNearBottom() {
        const threshold = 150;
        return (chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight) <= threshold;
    }

    function fetchMessages() {
        fetch(getApiUrl('api/get_messages?stage_id=' + stageId))
            .then(res => res.json())
            .then(data => {
                if (data.success && data.messages) {
                    renderMessages(data.messages);
                } else if (data.error) {
                    console.warn("Chat error:", data.error);
                }
            })
            .catch(err => console.error("Chat polling error:", err));
    }

    function renderMessages(messages) {
        if (messages.length === lastMessageCount && !isFirstLoad) {
            return; // No new messages, avoid re-rendering DOM
        }

        let html = '';
        if (messages.length === 0) {
            html = `
                <div class="text-center text-muted py-5 my-auto">
                    <i class="bi bi-chat-dots display-3 d-block mb-3" style="color: #8696a0;"></i>
                    <h6 class="fw-bold" style="color: #e9edef;">Discussion sécurisée (RG9)</h6>
                    <p class="small mb-0" style="color: #8696a0;">Aucun message pour le moment. Lancez la conversation !</p>
                </div>
            `;
        } else {
            messages.forEach(msg => {
                const isMe = msg.expediteur_id == currentUserId;
                const bubbleClass = isMe ? 'me' : 'other';
                const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                // WhatsApp double checkmarks: bi-check2-all
                let statusCheck = '';
                if (isMe) {
                    if (msg.lu == 1) {
                        statusCheck = '<span class="wa-checks read" title="Lu"><i class="bi bi-check2-all"></i></span>';
                    } else {
                        statusCheck = '<span class="wa-checks unread" title="Envoyé"><i class="bi bi-check2-all"></i></span>';
                    }
                }

                html += `
                    <div class="wa-bubble ${bubbleClass}">
                        <div class="wa-bubble-text">${msg.message}</div>
                        <div class="wa-bubble-meta">
                            <span>${time}</span>
                            ${statusCheck}
                        </div>
                    </div>
                `;
            });
        }

        const shouldScroll = isFirstLoad || isUserNearBottom();
        chatContainer.innerHTML = html;
        lastMessageCount = messages.length;

        if (shouldScroll) {
            scrollToBottom(true);
        }
        isFirstLoad = false;
    }

    // Submit message handler
    function sendMessage() {
        const text = messageInput.value.trim();
        if (!text) return;

        messageInput.value = '';

        const formData = new FormData();
        formData.append('stage_id', stageId);
        formData.append('message', text);

        fetch(getApiUrl('api/send_message'), {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(text => {
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error("Non-JSON API response:", text);
                alert("Erreur de réponse du serveur : " + text.substring(0, 100));
                return;
            }

            if (data.success) {
                fetchMessages();
                setTimeout(() => scrollToBottom(true), 100);
            } else {
                alert("Erreur d'envoi : " + (data.error || "Impossible d'envoyer le message."));
            }
        })
        .catch(err => {
            console.error("Error sending message:", err);
            alert("Erreur de connexion lors de l'envoi.");
        });
    }

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        sendMessage();
    });

    // Enter key sends message (Shift+Enter for newline)
    messageInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Initial load + interval polling every 3 seconds
    fetchMessages();
    setInterval(fetchMessages, 3000);
}
