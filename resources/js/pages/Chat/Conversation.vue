<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import MarkdownIt from 'markdown-it'
import hljs from 'highlight.js'
import 'highlight.js/styles/github.css'
import axios from 'axios'

// 🔹 Images des modèles
import imgCreativite from '@/assets/images/models/nethra-creativite.png'
import imgPhilosophe from '@/assets/images/models/nethra-philosophe.png'
import imgStratege from '@/assets/images/models/nethra-stratege.png'
import imgPersonnalise from '@/assets/images/models/nethra-personnalise.png'

interface Message {
  id: number
  role: 'user' | 'assistant'
  content: string
}

const props = defineProps<{
  conversation: {
    id: number
    title: string
    model_used?: string
    messages?: Message[]
  }
}>()

const messages = ref<Message[]>([])

// 🔹 Avatar selon modèle
const modelAvatarMap: Record<string, string> = {
  CoachCréativité: imgCreativite,
  PhilosopheModerne: imgPhilosophe,
  StratègeDeVie: imgStratege,
  custom: imgPersonnalise,
}

const assistantAvatar = () =>
  modelAvatarMap[props.conversation.model_used || 'custom'] || imgPersonnalise

const md = new MarkdownIt({
  highlight: (str, lang) => {
    if (lang && hljs.getLanguage(lang)) {
      try {
        return `<pre class="hljs"><code>${hljs.highlight(str, { language: lang }).value}</code></pre>`
      } catch {}
    }
    return `<pre class="hljs"><code>${md.utils.escapeHtml(str)}</code></pre>`
  },
})

const containerRef = ref<HTMLElement | null>(null)

function scrollToBottom() {
  nextTick(() => {
    if (containerRef.value) {
      containerRef.value.scrollTop = containerRef.value.scrollHeight
    }
  })
}

async function fetchMessages() {
  if (!props.conversation?.id) return
  try {
    const res = await axios.get(`/api/chat/${props.conversation.id}`, {
      headers: { Accept: 'application/json' },
    })

    const fetchedMessages: Message[] = res.data.messages || []

    fetchedMessages.forEach(msg => {
      const exists = messages.value.find(m => m.id === msg.id)
      if (!exists) messages.value.push(msg)
    })

    scrollToBottom()
  } catch (err) {
    console.error('Erreur fetch messages :', err)
  }
}

function onMessageSent(e: CustomEvent) {
  const newMessage = e.detail as Message
  if (!newMessage || !newMessage.id) return

  const existing = messages.value.find(m => m.id === newMessage.id)
  if (existing) {
    existing.content = newMessage.content
  } else {
    messages.value.push(newMessage)
  }

  scrollToBottom()
}

watch(() => props.conversation, fetchMessages, { immediate: true })

watch(
  () => props.conversation.messages,
  () => scrollToBottom(),
  { deep: true }
)

onMounted(() => {
  window.addEventListener('message-sent', onMessageSent as EventListener)
})

onBeforeUnmount(() => {
  window.removeEventListener('message-sent', onMessageSent as EventListener)
})
</script>

<template>
  <div
    ref="containerRef"
    class="messages-container dashboard-scroll p-2 space-y-4 flex flex-col bg-[#0F0F2F]"
  >
    <p v-if="!messages.length" class="italic text-[#52c5ff]">
      Aucun message pour le moment.
    </p>

    <div
      v-for="m in messages"
      :key="m.id"
      class="flex w-full"
      :class="m.role === 'user' ? 'justify-start' : 'justify-end'"
    >
      <!-- MESSAGE UTILISATEUR -->
      <div
        v-if="m.role === 'user'"
        class="max-w-[70%] p-3 rounded-xl bg-[#1A1A3C] text-[#52c5ff]"
      >
        <div v-html="md.render(m.content)"></div>
      </div>

      <!-- MESSAGE ASSISTANT -->
      <div
        v-else
        class="flex items-start gap-3 max-w-[70%]"
      >
        <div class="w-8 h-8 rounded-full overflow-hidden border border-[#C96BFF] shrink-0">
          <img
            :src="assistantAvatar()"
            alt="Assistant"
            class="object-cover w-full h-full"
          />
        </div>

        <div class="p-3 rounded-xl bg-[#2A1F3D] text-[#C96BFF]">
          <div v-html="md.render(m.content)"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.messages-container {
  display: flex;
  flex-direction: column;
  flex: 1 1 0;
  min-height: 0;
}

/* Scrollbar personnalisée */
.dashboard-scroll {
  flex: 1;
  overflow-y: auto;
  padding-right: 12px;
  margin-right: -6px;
}

.dashboard-scroll::-webkit-scrollbar {
  width: 8px;
}

.dashboard-scroll::-webkit-scrollbar-track {
  background: rgba(15, 15, 47, 0.8);
  border-radius: 8px;
}

.dashboard-scroll::-webkit-scrollbar-thumb {
  background-color: #52c5ff;
  border-radius: 8px;
  border: 2px solid rgba(15, 15, 47, 0.8);
}

.dashboard-scroll {
  scrollbar-width: thin;
  scrollbar-color: #52c5ff rgba(15, 15, 47, 0.8);
}
</style>
