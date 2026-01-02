<template>
  <div>
    <h1 class="mb-4 text-2xl font-bold">
      {{ conversation.title || 'Conversation' }}
    </h1>

    <div class="mb-4 space-y-2">
      <div v-for="msg in conversation.messages" :key="msg.id" :class="{'text-right': msg.role === 'assistant'}">
        <strong>{{ msg.role }}:</strong> {{ msg.content }}
      </div>
    </div>

    <form @submit.prevent="sendMessage" class="flex space-x-2">
      <input
        v-model="newMessage"
        type="text"
        placeholder="Tape ton message..."
        class="flex-1 p-2 border rounded"
      />
      <button type="submit" class="p-2 text-white bg-blue-600 rounded">Envoyer</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

interface Message {
  id: number;
  role: string;
  content: string;
  created_at: string;
}

interface Conversation {
  id: number;
  title?: string;
  messages: Message[];
}

const props = defineProps({
  conversation: {
    type: Object as () => Conversation,
    required: true
  }
});

const newMessage = ref('');

function sendMessage() {
  if (!newMessage.value.trim()) return;

  Inertia.post(`/conversations/${props.conversation.id}/messages`, {
    role: 'user',
    content: newMessage.value
  }, {
    preserveScroll: true,
    onSuccess: () => {
      newMessage.value = '';
    }
  });
}
</script>
