<script setup lang="ts">
import { ref, nextTick } from 'vue';
import axios from 'axios';

interface MessageType {
  id: number;
  role: 'user' | 'assistant';
  content: string;
}

interface ConversationType {
  id: number;
  messages: MessageType[];
  model_used?: string;
}

const props = defineProps<{
  conversation: ConversationType;
  model_used: string;
}>();

const userInput = ref('');
const sending = ref(false);

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const tokenMeta = document.head.querySelector('meta[name="csrf-token"]');
if (tokenMeta) axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content') || '';
const axiosConfig = { headers: { Accept: 'application/json' } };

// ----------------------------
// Fonction pour envoyer le message utilisateur
// ----------------------------
async function sendMessage() {
  if (!userInput.value.trim() || sending.value) return;
  sending.value = true;

  const userText = userInput.value.trim();
  userInput.value = '';

  let userMessage: MessageType;
  let botMessage: MessageType;

  try {
    // ⚡ Crée user + assistant en une seule requête
    const res = await axios.post(`/api/chat/${props.conversation.id}/messages`, {
      content: userText,
      role: 'user'
    }, axiosConfig);

    userMessage = res.data.userMessage;
    botMessage = res.data.assistantMessage;

    // ⚡ Ajoute directement dans la conversation locale
    props.conversation.messages.push(userMessage, botMessage);
  } catch (err) {
    console.error('Erreur création message user/assistant :', err);
    sending.value = false;
    return;
  }

  // ⚡ Lancer le streaming SSE seulement si on a l'ID correct du bot
  if (botMessage?.id) {
    streamAssistantMessage(botMessage.id, userText);
  } else {
    console.error('Impossible de récupérer l’ID du message assistant.');
    sending.value = false;
  }
}

// ----------------------------
// Streaming SSE
// ----------------------------
function streamAssistantMessage(botMessageId: number, userText: string) {
  const url = `/chat/${props.conversation.id}/stream?message=${encodeURIComponent(userText)}&messageId=${botMessageId}&model=${props.model_used}`;
  const eventSource = new EventSource(url);

  let partialMessage = '';

  eventSource.onmessage = async (event) => {
    try {
      const data = JSON.parse(event.data);
      partialMessage += data.token;

      // Mettre à jour le message assistant en local
      let msg = props.conversation.messages.find(m => m.id === botMessageId);
      if (!msg) {
        msg = { id: botMessageId, role: 'assistant', content: '' };
        props.conversation.messages.push(msg);
      }
      msg.content = partialMessage;

      await nextTick();
    } catch (err) {
      console.error('Erreur traitement SSE :', err);
    }
  };

  // 🔹 Quand le flux SSE se termine, sauvegarder le message complet
  eventSource.addEventListener('end', async () => {
    try {
      await axios.post(
        `/api/chat/${props.conversation.id}/messages/${botMessageId}/save`,
        { content: partialMessage },
        axiosConfig
      );
    } catch (err) {
      console.error('Erreur sauvegarde finale message assistant :', err);
    } finally {
      sending.value = false; // 🔹 Permet d’envoyer un nouveau message
      eventSource.close();
    }
  });

  eventSource.onerror = () => {
    sending.value = false;
    eventSource.close();
  };
}
</script>

<template>
  <div class="flex gap-2 mt-2">
    <input
      v-model="userInput"
      @keyup.enter="sendMessage"
      type="text"
      placeholder="Écris ton message..."
      class="flex-1 px-3 py-2 rounded border border-[#0F4F8F] bg-[#0F0F2F] text-white"
      :disabled="sending"
    />
    <button
      @click="sendMessage"
      class="px-4 py-2 bg-[#52c5ff] rounded hover:bg-[#44b0f0] transition"
      :disabled="sending"
    >
      Envoyer
    </button>
  </div>
</template>
