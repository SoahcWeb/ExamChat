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
// Fetch messages depuis l'API
// -----------------------------
async function fetchMessages() {
    if (!props.conversation?.id) return;
    try {
        const res = await axios.get(`/api/chat/${props.conversation.id}`, {
            headers: { Accept: 'application/json' },
        });
        messages.value = res.data.messages || [];
        scrollToBottom();
    } catch (err) {
        console.error('Erreur fetch messages :', err);
    }
}

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
// Événement pour messages envoyés en temps réel
// -----------------------------
function onMessageSent(e: CustomEvent) {
    const newMessage = e.detail as Message;
    if (newMessage && newMessage.id && newMessage.content) {
        messages.value.push(newMessage);
        scrollToBottom();
    }
}

// -----------------------------
// Watch sur la conversation active
// -----------------------------
watch(() => props.conversation, fetchMessages, { immediate: true });

// -----------------------------
// Lifecycle hooks
// -----------------------------
onMounted(() =>
    window.addEventListener('message-sent', onMessageSent as EventListener),
);
onBeforeUnmount(() =>
    window.removeEventListener('message-sent', onMessageSent as EventListener),
);
</script>

<template>
    <div
        ref="containerRef"
        class="p-2 space-y-4 messages-container flex flex-1 flex-col overflow-y-auto"
    >
        <!-- Message si aucun message -->
        <p v-if="!messages.length" class="text-gray-400 italic">
            Aucun message pour le moment.
        </p>

        <div
            v-for="m in messages"
            :key="m.id"
            class="max-w-lg p-2 rounded break-words"
            :class="{
                'bg-blue-100 self-end': m.role === 'user',
                'bg-gray-100 self-start': m.role === 'assistant',
            }"
        >
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
