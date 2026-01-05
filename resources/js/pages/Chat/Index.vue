<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import HeaderFooterLayout from '@/layouts/HeaderFooterLayout.vue';
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

// États
const conversations = ref<ConversationType[]>([]);
const activeConversation = ref<ConversationType | null>(null);
const activeModel = ref('gpt-3.5-turbo');
const loadingConversation = ref(false);

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const tokenMeta = document.head.querySelector('meta[name="csrf-token"]');
if (tokenMeta) axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content') || '';

const axiosConfig = { headers: { Accept: 'application/json' } };

// Modèles preset
const presetModels = [
    { id: 'CoachCréativité', title: 'Nethra Créativité', description: 'Stimule tes idées et structure tes projets créatifs avec des méthodes concrètes.' },
    { id: 'PhilosopheModerne', title: 'Nethra Philosophe', description: 'Réfléchis sur tes valeurs et tes choix, et applique la philosophie à ta vie quotidienne.' },
    { id: 'StratègeDeVie', title: 'Nethra Stratège', description: 'Planifie et organise ta vie avec des étapes claires pour atteindre tes objectifs personnels et professionnels.' },
    { id: 'custom', title: 'Nethra Personnalisé', description: 'Crée ton propre assistant selon tes besoins uniques. Définis le ton, le style, les domaines de spécialité et la manière dont Nethra doit t’accompagner.' },
];

// Query string
const urlParams = new URLSearchParams(window.location.search);
const initialConversationId = urlParams.get('conversation_id');
const initialModelFromUrl = urlParams.get('model');

if (initialModelFromUrl) activeModel.value = initialModelFromUrl;

// Description du modèle actif
const activeModelDescription = computed(() => {
    const model = presetModels.find(m => m.id === activeModel.value);
    return model ? model.description : '';
});

// Fonctions
async function fetchConversations() {
    try {
        const res = await axios.get('/api/chat', axiosConfig);
        conversations.value = Array.isArray(res.data) ? res.data : [];
        if (!activeConversation.value && conversations.value.length > 0) {
            await selectConversation(conversations.value[0]);
        }
    } catch (err) {
        console.error('Erreur fetch conversations :', err);
    }
}

async function loadConversation(id: number) {
    loadingConversation.value = true;
    try {
        const res = await axios.get(`/api/chat/${id}`, axiosConfig);
        activeConversation.value = res.data;
        if (!activeConversation.value.messages) activeConversation.value.messages = [];
        activeModel.value = activeConversation.value.model_used || 'gpt-3.5-turbo';
    } catch (err) {
        console.error('Erreur fetch conversation :', err);
    } finally {
        loadingConversation.value = false;
    }
}

async function selectConversation(convo: ConversationType) {
    loadingConversation.value = true;
    try {
        await loadConversation(convo.id);
    } finally {
        loadingConversation.value = false;
    }
}

async function newConversation() {
    try {
        const res = await axios.post(
            '/api/chat',
            { title: 'Nouvelle conversation', model_used: activeModel.value },
            axiosConfig
        );
        if (res.data) {
            res.data.messages = [];
            res.data.model_used = activeModel.value;
            conversations.value.unshift(res.data);
            await selectConversation(res.data);
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

function selectPresetModel(modelName: string) {
    activeModel.value = modelName;
    changeModel();
}

function goToCustomModel() {
    activeModel.value = 'custom';
    changeModel();
    window.location.href = '/settings/instructions';
}

async function deleteConversation(conversationId: number) {
    if (!confirm('Voulez-vous vraiment supprimer cette conversation ?')) return;

    try {
        conversations.value = conversations.value.filter(c => c.id !== conversationId);
        if (activeConversation.value?.id === conversationId) {
            activeConversation.value = conversations.value[0] || null;
            if (activeConversation.value) await loadConversation(activeConversation.value.id);
        }
        await axios.delete(`/api/chat/${conversationId}`, axiosConfig);
    } catch (err) {
        console.error('Erreur suppression conversation :', err);
    }
}

// Mounted
onMounted(async () => {
    await fetchConversations();

    if (initialConversationId) {
        const convo = conversations.value.find(c => c.id === Number(initialConversationId));
        if (convo) {
            await selectConversation(convo);
        } else {
            await loadConversation(Number(initialConversationId));
        }
    } else if (initialModelFromUrl) {
        await newConversation();
    }

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
    <!-- SIDEBAR + CHAT avec espace sous le header -->
    <div class="flex flex-1 min-h-[80vh] gap-4 p-4 pt-8"> <!-- pt-16 ajoute l'espace sous le header -->
      <!-- SIDEBAR -->
      <aside class="w-1/4 bg-[#0F0F2F]/80 p-4 rounded-xl border border-[#0F4F8F] space-y-4">
        <button
          @click="newConversation"
          class="w-full px-3 py-2 text-white bg-[#52c5ff] rounded-lg hover:bg-[#44b0f0] transition"
        >
          Nouvelle conversation
        </button>

        <div class="flex items-center mt-2 space-x-2">
          <span>Modèle :</span>
          <select
            v-model="activeModel"
            @change="changeModel"
            class="px-2 py-1 rounded border border-[#0F4F8F] bg-[#0F0F2F] text-[#E0E6F0] flex-1"
          >
            <option v-for="model in presetModels" :key="model.id" :value="model.id">
              {{ model.title }}
            </option>
          </select>
        </div>

        <p class="mt-1 text-sm text-[#E0E6F0]">{{ activeModelDescription }}</p>

        <ul v-if="conversations.length > 0" class="mt-2 space-y-2">
          <li
            v-for="c in conversations" :key="c.id"
            class="flex justify-between items-center p-2 rounded cursor-pointer transition hover:bg-[#0F4F8F]/50"
          >
            <span
              @click="selectConversation(c)"
              :class="activeConversation?.id === c.id ? 'bg-[#0F4F8F]/70 font-semibold p-1 rounded' : ''"
            >
              {{ c.title }}
            </span>
            <button
              @click.stop="deleteConversation(c.id)"
              class="px-2 py-1 ml-2 text-sm text-red-500 transition border border-red-500 rounded hover:text-red-700"
            >
              Supprimer
            </button>
          </li>
        </ul>

        <p v-else class="mt-2 italic text-gray-400">Aucune conversation</p>
      </aside>

      <!-- CHAT -->
      <section class="flex flex-col w-3/4 space-y-4">
        <p v-if="loadingConversation" class="italic text-gray-400">
          Chargement de la conversation...
        </p>

        <p v-else-if="!activeConversation" class="italic text-gray-400">
          Commencez une discussion
        </p>

        <div v-else class="flex flex-col flex-1 space-y-4 bg-[#0F0F2F]/80 p-4 rounded-xl border border-[#0F4F8F]">
          <Conversation :conversation="activeConversation" />
          <MessageInput :conversation="activeConversation" :model_used="activeConversation.model_used || activeModel" />
        </div>
      </section>
    </div>
  </HeaderFooterLayout>
</template>
