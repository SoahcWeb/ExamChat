<template>
    <div>
        <h1 class="mb-4 text-2xl font-bold">Conversations</h1>
        <ul>
            <li v-for="conv in conversations" :key="conv.id" class="mb-2">
                <inertia-link
                    :href="`/conversations/${conv.id}`"
                    class="text-blue-600"
                >
                    {{ conv.title || 'Sans titre' }}
                </inertia-link>
            </li>
        </ul>
    </div>
</template>

<script setup lang="ts">
import { PropType } from 'vue';

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
    created_at: string;
    updated_at: string;
}

const props = defineProps({
    conversations: {
        type: Array as PropType<Conversation[]>,
        required: true,
    },
});
</script>
