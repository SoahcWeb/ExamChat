<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps<{
    conversation: { id: number; title: string; messages?: any[] };
    model_used: string;
}>()

const message = ref('');
const loading = ref(false);
const canSend = ref(false);

const currentModel = ref(props.model_used);

watch(() => props.model_used, (newVal) => {
    currentModel.value = newVal;
});

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

const modelPersona: Record<string, string> = {
  CoachCréativité: `💡 Salut ! Je suis Nethra Créativité...`,
  PhilosopheModerne: `🧐 Salut, je suis Nethra Philosophe...`,
  StratègeDeVie: `📊 Salut ! Je suis Nethra Stratège...`,
  custom: `✨ Salut ! Je suis ton Nethra Personnalisé...`
};

function streamAssistantMessage(conversationId: number, botMessageId: number, userMessage: string) {
    let messageWithPersona = currentModel.value !== 'custom' ? modelPersona[currentModel.value] + '\n' + userMessage : ((window as any).customInstructions || '') + '\n' + userMessage;

    const eventSource = new EventSource(
        `/chat/${conversationId}/stream?message=${encodeURIComponent(messageWithPersona)}&messageId=${botMessageId}&model=${encodeURIComponent(currentModel.value)}`
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

async function sendMessage() {
    if (!canSend.value) return;

    loading.value = true;
    const userText = message.value;

    try {
        const res = await axios.post(
            `/api/chat/${props.conversation.id}/messages`,
            { role: 'user', content: userText, model: currentModel.value },
            axiosConfig
        );

        const { userMessage, botMessage, conversation: updatedConversation } = res.data;
        message.value = '';

        window.dispatchEvent(new CustomEvent('message-sent', { detail: userMessage }));

        if (updatedConversation) {
            window.dispatchEvent(new CustomEvent('conversation-updated', { detail: updatedConversation }));
        }

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
    <form @submit.prevent="sendMessage" class="flex mt-auto gap-2 p-2 border-t border-[#333] bg-[#0F0F2F]">
        <input
            v-model="message"
            type="text"
            placeholder="Écrire un message..."
            class="flex-1 px-3 py-2 rounded bg-[#1A1A3C] text-white focus:outline-none"
        />
        <button
            type="submit"
            class="px-4 py-2 bg-[#52c5ff] rounded text-black font-semibold disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!canSend || loading"
        >
            {{ loading ? 'Envoi...' : 'Envoyer' }}
        </button>
    </form>
</template>
