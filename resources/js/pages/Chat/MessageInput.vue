<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps<{
    conversation: { id: number; title: string; messages?: any[] };
    model_used: string;
}>()

const message = ref('');
const loading = ref(false);
const canSend = ref(false);

// State interne pour suivre le modèle actif
const currentModel = ref(props.model_used);

// Met à jour le modèle si le prop change
watch(() => props.model_used, (newVal) => {
    currentModel.value = newVal;
});

// Activation du bouton envoyer
watch(message, (val) => {
    canSend.value = val.trim().length > 0;
});

const axiosConfig = {
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN':
            document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    },
};

// 🔹 Prompts système stricts pour chaque modèle
const modelPersona: Record<string, string> = {
  CoachCréativité: `
💡 Salut ! Je suis Nethra Créativité. Je vais booster tes idées et t'aider à structurer tes projets.
🔹 Conseil : Commence par noter toutes tes idées, même les folles, puis on trie et structure.
🎨 Astuce : N'hésite pas à mélanger les inspirations de différents domaines pour créer quelque chose d'unique.
`,
  PhilosopheModerne: `
🧐 Salut, je suis Nethra Philosophe. On va réfléchir ensemble à tes choix et à tes valeurs.
📜 Conseil : Pose-toi toujours la question "Pourquoi ?" pour approfondir ta réflexion.
💭 Astuce : Utilise les analogies et exemples concrets pour mieux comprendre les concepts abstraits.
`,
  StratègeDeVie: `
📊 Salut ! Je suis Nethra Stratège. Je t'aide à organiser la vie et atteindre tes objectifs.
🗂 Conseil : Décompose tes projets en étapes claires et mesurables.
⚡ Astuce : Priorise les actions qui auront le plus grand impact et planifie-les sur ton agenda.
`,
  custom: `
✨ Salut ! Je suis ton Nethra Personnalisé. Je vais suivre tes instructions et ton style préféré.
🔧 Conseil : Plus tes instructions sont précises, plus mes réponses seront adaptées.
🌟 Astuce : Indique-moi ton ton, tes domaines de spécialité et tes préférences pour rendre nos échanges uniques.
`
};

// -----------------------------
// Streaming SSE assistant
function streamAssistantMessage(conversationId: number, botMessageId: number, userMessage: string) {
    let messageWithPersona = userMessage;

    // Applique le prompt système selon le modèle
    if (currentModel.value !== 'custom') {
        messageWithPersona = modelPersona[currentModel.value] + '\n' + userMessage;
    } else {
        // Pour custom, on récupère les instructions personnalisées depuis la session
        const customInstructions = (window as any).customInstructions || '';
        messageWithPersona = customInstructions + '\n' + userMessage;
    }

    const eventSource = new EventSource(
        `/chat/${conversationId}/stream?message=${encodeURIComponent(messageWithPersona)}&messageId=${botMessageId}&model=${encodeURIComponent(currentModel.value)}`
    );
    let partialMessage = '';

    eventSource.onmessage = (event) => {
        const data = JSON.parse(event.data);
        partialMessage += data.token;

        window.dispatchEvent(
            new CustomEvent('message-sent', {
                detail: {
                    id: botMessageId,
                    role: 'assistant',
                    content: partialMessage,
                },
            })
        );
    };

    eventSource.onerror = () => eventSource.close();
}

// -----------------------------
// Envoi message utilisateur
async function sendMessage() {
    if (!canSend.value) return;

    loading.value = true;
    const userText = message.value;

    try {
        // Envoie message user au backend avec le modèle choisi
        const res = await axios.post(
            `/api/chat/${props.conversation.id}/messages`,
            {
                role: 'user',
                content: userText,
                model: currentModel.value
            },
            axiosConfig
        );

        const { userMessage, botMessage, conversation: updatedConversation } = res.data;

        message.value = '';

        // Affiche message user
        window.dispatchEvent(new CustomEvent('message-sent', { detail: userMessage }));

        // Mise à jour conversation si besoin
        if (updatedConversation) {
            window.dispatchEvent(new CustomEvent('conversation-updated', { detail: updatedConversation }));
        }

        // Lance flux SSE assistant
        if (botMessage?.id) {
            streamAssistantMessage(props.conversation.id, botMessage.id, userText);
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
            class="px-4 text-white bg-blue-500 rounded-r hover:bg-blue-600 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="!canSend || loading"
        >
            {{ loading ? 'Envoi...' : 'Envoyer' }}
        </button>
    </form>
</template>
