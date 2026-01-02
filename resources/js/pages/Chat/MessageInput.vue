<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps<{
  conversation: { id: number; title: string; messages?: any[] };
  model_used?: string;
}>();

const message = ref('');
const loading = ref(false);
const canSend = ref(false);

// Activation du bouton uniquement si input non vide
watch(message, val => {
  canSend.value = val.trim().length > 0;
});

const axiosConfig = {
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN':
      document.head.querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') || '',
  },
};

async function sendMessage() {
  if (!canSend.value) return;

  loading.value = true;

  try {
    const res = await axios.post(
      `/api/chat/${props.conversation.id}/messages`,
      { role: 'user', content: message.value },
      axiosConfig
    );

    // Reset champ
    message.value = '';

    const { userMessage, botMessage, conversation: updatedConversation } =
      res.data;

    // Déclenche les events pour l'UI
    window.dispatchEvent(
      new CustomEvent('message-sent', { detail: userMessage })
    );
    window.dispatchEvent(
      new CustomEvent('message-sent', { detail: botMessage })
    );

    if (updatedConversation) {
      window.dispatchEvent(
        new CustomEvent('conversation-updated', {
          detail: updatedConversation,
        })
      );
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
      class="px-4 text-white bg-blue-500 rounded-r hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
      :disabled="!canSend || loading"
    >
      {{ loading ? 'Envoi...' : 'Envoyer' }}
    </button>
  </form>
</template>
