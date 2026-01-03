<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps<{
    conversation: { id: number; title: string; messages?: any[] };
}>()

const message = ref('');
const loading = ref(false);
const canSend = ref(false);

watch(message, (val) => {
    canSend.value = val.trim().length > 0;
});

const axiosConfig = {
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN':
            document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    },
};

// -----------------------------
// Streaming SSE assistant
function streamAssistantMessage(conversationId: number, botMessageId: number, userMessage: string) {
    const eventSource = new EventSource(
        `/chat/${conversationId}/stream?message=${encodeURIComponent(userMessage)}&messageId=${botMessageId}`
    );
    let partialMessage = '';

    eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        partialMessage += data.token;

        window.dispatchEvent(
            new CustomEvent('message-sent', {
                detail: {
                    id: botMessageId,
                    role: 'assistant',
                    content: partialMessage,
                },
            })
        );
    };

    eventSource.onerror = () => eventSource.close();
}

// -----------------------------
// Envoi message utilisateur
async function sendMessage() {
    if (!canSend.value) return;

    loading.value = true;
    const userText = message.value;

    try {
        // 1️⃣ Envoie message user au backend
        const res = await axios.post(
            `/api/chat/${props.conversation.id}/messages`,
            { role: 'user', content: userText },
            axiosConfig
        );

        const { userMessage, botMessage, conversation: updatedConversation } = res.data;

        message.value = '';

        // 2️⃣ Déclenche event pour afficher message user
        window.dispatchEvent(new CustomEvent('message-sent', { detail: userMessage }));

        // 3️⃣ Déclenche mise à jour de la conversation si besoin
        if (updatedConversation) {
            window.dispatchEvent(new CustomEvent('conversation-updated', { detail: updatedConversation }));
        }

        // 4️⃣ Lance le flux SSE avec le bon ID du message assistant
        if (botMessage?.id) {
            streamAssistantMessage(props.conversation.id, botMessage.id, userText);
        }

    } catch (err) {
        console.error('Erreur envoi message :', err);
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <form @submit.prevent="sendMessage" class="flex mt-4">
        <input
            v-model="message"
            type="text"
            placeholder="Écrire un message..."
            class="flex-1 px-3 py-2 border border-gray-300 rounded-l focus:outline-none"
        />
        <button
            type="submit"
            class="px-4 text-white bg-blue-500 rounded-r hover:bg-blue-600 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!canSend || loading"
        >
            {{ loading ? 'Envoi...' : 'Envoyer' }}
        </button>
    </form>
</template>
