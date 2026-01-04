<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';
import MarkdownIt from 'markdown-it';
import hljs from 'highlight.js';
import 'highlight.js/styles/github.css';
import axios from 'axios';

interface Message {
    id: number;
    role: 'user' | 'assistant';
    content: string;
}

const props = defineProps<{
    conversation: { id: number; title: string; messages?: Message[] };
}>();

const messages = ref<Message[]>([]);

// -----------------------------
// Markdown + syntax highlight
// -----------------------------
const md = new MarkdownIt({
    highlight: (str, lang) => {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return `<pre class="hljs"><code>${hljs.highlight(str, { language: lang }).value}</code></pre>`;
            } catch {}
        }
        return `<pre class="hljs"><code>${md.utils.escapeHtml(str)}</code></pre>`;
    },
});

// -----------------------------
// Scroll automatique vers le bas
// -----------------------------
const containerRef = ref<HTMLElement | null>(null);
function scrollToBottom() {
    nextTick(() => {
        if (containerRef.value) {
            containerRef.value.scrollTop = containerRef.value.scrollHeight;
        }
    });
}

// -----------------------------
// Fetch messages depuis l'API
// ⚡ Merge avec messages existants pour ne pas écraser SSE en cours
async function fetchMessages() {
    if (!props.conversation?.id) return;
    try {
        const res = await axios.get(`/api/chat/${props.conversation.id}`, {
            headers: { Accept: 'application/json' },
        });
        const fetchedMessages: Message[] = res.data.messages || [];

        fetchedMessages.forEach(msg => {
            const exists = messages.value.find(m => m.id === msg.id);
            if (!exists) messages.value.push(msg);
        });

        scrollToBottom();
    } catch (err) {
        console.error('Erreur fetch messages :', err);
    }
}

// -----------------------------
// Gestion du flux SSE
function onMessageSent(e: CustomEvent) {
    const newMessage = e.detail as Message;
    if (!newMessage || !newMessage.id) return;

    // ⚡ Si le message existe déjà, on met à jour (pour le flux token par token)
    const existing = messages.value.find(m => m.id === newMessage.id);
    if (existing) {
        existing.content = newMessage.content;
    } else {
        messages.value.push(newMessage);
    }

    scrollToBottom();
}

// -----------------------------
// Watch sur la conversation active
watch(() => props.conversation, fetchMessages, { immediate: true });

// -----------------------------
// Lifecycle hooks
onMounted(() => {
    window.addEventListener('message-sent', onMessageSent as EventListener);
});
onBeforeUnmount(() => {
    window.removeEventListener('message-sent', onMessageSent as EventListener);
});
</script>

<template>
    <div
        ref="containerRef"
        class="p-2 space-y-4 messages-container flex flex-1 flex-col overflow-y-auto bg-[#0F0F2F]"
    >
        <p v-if="!messages.length" class="italic text-[#52c5ff]">
            Aucun message pour le moment.
        </p>

        <div v-for="m in messages" :key="m.id" class="max-w-lg p-2 rounded break-words text-[#52c5ff]">
            <div v-html="md.render(m.content)"></div>
        </div>
    </div>
</template>

<style scoped>
.messages-container {
    display: flex;
    flex-direction: column;
    flex: 1;
}
</style>
