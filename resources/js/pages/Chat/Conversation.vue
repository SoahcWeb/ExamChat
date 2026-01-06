<script setup lang="ts">
import { ref, watch, nextTick } from 'vue'
import MarkdownIt from 'markdown-it'
import hljs from 'highlight.js'
import 'highlight.js/styles/github.css'

import imgCreativite from '@/assets/images/models/nethra-creativite.png'
import imgPhilosophe from '@/assets/images/models/nethra-philosophe.png'
import imgStratege from '@/assets/images/models/nethra-stratege.png'
import imgPersonnalise from '@/assets/images/models/nethra-personnalise.png'

interface Message {
  id: number
  role: 'user' | 'assistant'
  content: string | null // ✅ autorise null pour le streaming initial
}

const props = defineProps<{
  conversation: {
    id: number
    title: string
    model_used?: string
    messages: Message[]
  }
}>()

// ✅ Ref locale pour messages
const messages = ref([...props.conversation.messages || []])

// 🔹 Synchronisation automatique à chaque changement de conversation
watch(
  () => props.conversation,
  (newConvo) => {
    messages.value = [...newConvo.messages || []]
    nextTick(scrollToBottom)
  },
  { deep: true, immediate: true }
)

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
      return `<pre class="hljs"><code>${hljs.highlight(str, { language: lang }).value}</code></pre>`
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
      <div
        v-if="m.role === 'user'"
        class="max-w-[70%] p-3 rounded-xl bg-[#1A1A3C] text-[#52c5ff]"
      >
        <div v-html="md.render(m.content || '')"></div> <!-- ✅ safe render -->
      </div>

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
          <div v-html="md.render(m.content || '')"></div> <!-- ✅ safe render -->
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
