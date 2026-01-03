<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import HeaderFooterLayout from '@/layouts/HeaderFooterLayout.vue'; // Layout commun avec header/footer
import Conversation from './Conversation.vue';
import MessageInput from './MessageInput.vue';

interface MessageType {
    id: number;
    role: 'user' | 'assistant';
    content: string;
}

interface ConversationType {
    id: number;
    title: string;
    updated_at: string;
    model_used?: string;
    messages?: MessageType[];
}

const conversations = ref<ConversationType[]>([]);
const activeConversation = ref<ConversationType | null>(null);
const activeModel = ref('gpt-3.5-turbo');
const loadingMessage = ref(false);

// Axios setup
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const tokenMeta = document.head.querySelector('meta[name="csrf-token"]');
if (tokenMeta) axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content') || '';

const axiosConfig = { headers: { Accept: 'application/json' } };

// Fonctions principales
async function fetchConversations() {
    try {
        const res = await axios.get('/api/chat', axiosConfig);
        conversations.value = Array.isArray(res.data) ? res.data : [];
        if (!activeConversation.value && conversations.value.length > 0) {
            activeConversation.value = conversations.value[0];
            await loadConversation(conversations.value[0].id);
        }
    } catch (err) {
        console.error('Erreur fetch conversations :', err);
    }
}

async function loadConversation(id: number) {
    try {
        const res = await axios.get(`/api/chat/${id}`, axiosConfig);
        activeConversation.value = res.data;
        if (!activeConversation.value.messages) activeConversation.value.messages = [];
        activeModel.value = activeConversation.value.model_used || 'gpt-3.5-turbo';
    } catch (err) {
        console.error('Erreur fetch conversation :', err);
    }
}

async function selectConversation(convo: ConversationType) {
    activeConversation.value = convo;
    await loadConversation(convo.id);
}

async function newConversation() {
    try {
        const res = await axios.post('/api/chat', { title: 'Nouvelle conversation' }, axiosConfig);
        if (res.data) {
            res.data.messages = [];
            conversations.value.unshift(res.data);
            activeConversation.value = res.data;
            activeModel.value = 'gpt-3.5-turbo';
        }
    } catch (err) {
        console.error('Erreur création conversation :', err);
    }
}

async function changeModel() {
    if (!activeConversation.value) return;
    try {
        const res = await axios.patch(
            `/api/chat/${activeConversation.value.id}/model`,
            { model_used: activeModel.value },
            axiosConfig
        );
        const updatedConvo: ConversationType = res.data;
        activeConversation.value.model_used = updatedConvo.model_used;
        const index = conversations.value.findIndex(c => c.id === updatedConvo.id);
        if (index !== -1) conversations.value[index] = { ...conversations.value[index], ...updatedConvo };
    } catch (err) {
        console.error('Erreur mise à jour modèle :', err);
    }
}

async function generateTitle(conversationId: number, firstMessage: string) {
    try {
        const res = await axios.post(`/api/chat/${conversationId}/generate-title`, { content: firstMessage }, axiosConfig);
        const newTitle = res.data.title;
        if (activeConversation.value) {
            activeConversation.value.title = newTitle;
            const index = conversations.value.findIndex(c => c.id === activeConversation.value?.id);
            if (index !== -1) conversations.value[index].title = newTitle;
        }
    } catch (err) {
        console.error('Erreur génération titre :', err);
    }
}

// Listeners
onMounted(() => {
    fetchConversations();
    window.addEventListener('message-sent', async (e: any) => {
        const newMessage: MessageType = e.detail;
        if (!activeConversation.value) return;
        activeConversation.value.messages?.push(newMessage);
        if (activeConversation.value.messages?.length === 2 && newMessage.role === 'assistant') {
            const firstUserMessage = activeConversation.value.messages[0].content;
            await generateTitle(activeConversation.value.id, firstUserMessage);
        }
    });
});
</script>

<template>
  <HeaderFooterLayout>
    <div class="flex flex-1 min-h-[80vh] gap-4 p-4">
      <!-- SIDEBAR LEFT (25%) -->
      <aside class="w-1/4 bg-[#0F0F2F]/80 p-4 rounded-xl border border-[#0F4F8F] space-y-4">
        <button
          @click="newConversation"
          class="w-full px-3 py-2 text-white bg-[#52c5ff] rounded-lg hover:bg-[#44b0f0] transition"
        >
          Nouvelle conversation
        </button>

        <div class="flex items-center space-x-2">
          <span>Modèle :</span>
          <select
            v-model="activeModel"
            @change="changeModel"
            class="px-2 py-1 rounded border border-[#0F4F8F] bg-[#0F0F2F] text-[#E0E6F0] flex-1"
          >
            <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
            <option value="gpt-4">GPT-4</option>
            <option value="gpt-4-32k">GPT-4-32k</option>
          </select>
        </div>

        <ul v-if="conversations.length > 0" class="space-y-2">
          <li
            v-for="c in conversations"
            :key="c.id"
            @click="selectConversation(c)"
            class="p-2 rounded cursor-pointer transition hover:bg-[#0F4F8F]/50"
            :class="activeConversation?.id === c.id ? 'bg-[#0F4F8F]/70 font-semibold' : ''"
          >
            {{ c.title }}
          </li>
        </ul>

        <p v-else class="italic text-gray-400">Aucune conversation</p>
      </aside>

      <!-- CHAT RIGHT (75%) -->
      <section class="flex flex-col w-3/4 space-y-4">
        <p v-if="!activeConversation" class="italic text-gray-400">
          Commencez une discussion
        </p>

        <div v-else class="flex flex-col flex-1 space-y-4 bg-[#0F0F2F]/80 p-4 rounded-xl border border-[#0F4F8F]">
          <Conversation :conversation="activeConversation" />
          <MessageInput :conversation="activeConversation" :model_used="activeModel" />
        </div>
      </section>
    </div>
  </HeaderFooterLayout>
</template>
