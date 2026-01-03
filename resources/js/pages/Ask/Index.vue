<script setup>
import { ref, watchEffect } from 'vue';
import { useForm } from '@inertiajs/vue3';

// Props Inertia
const props = defineProps({
    models: Array,
    selectedModel: String,
    message: String,
    response: String,
    error: String,
});

// Formulaire Inertia
const form = useForm({
    message: props.message ?? '',
    model:
        props.selectedModel ?? (props.models.length ? props.models[0].id : ''),
});

// Réponse affichée
const response = ref(props.response ?? '');

// Mise à jour si props.response change
watchEffect(() => {
    if (props.response) response.value = props.response;
});

// Méthode d'envoi
const submitForm = () => {
    form.post('/ask', {
        preserveState: true,
        onSuccess: (page) => {
            // Met à jour la réponse depuis le backend
            response.value = page.props.response;
            // Optionnel : reset du message
            form.reset('message');
        },
        onError: () => {
            // Ici tu peux gérer l'erreur globalement
            response.value = '';
        },
    });
};
</script>

<template>
    <div class="max-w-3xl p-8 mx-auto">
        <h1 class="mb-6 text-3xl font-bold">Mini ChatGPT</h1>

        <!-- Message d'erreur global -->
        <div v-if="props.error" class="mb-4 text-red-600">
            {{ props.error }}
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="submitForm">
            <!-- Choix du modèle -->
            <div class="mb-4">
                <label class="mb-1 font-semibold block">Modèle</label>
                <select
                    v-model="form.model"
                    class="p-2 rounded w-full border"
                    required
                >
                    <option
                        v-for="model in props.models"
                        :key="model.id"
                        :value="model.id"
                    >
                        {{ model.id }}
                    </option>
                </select>
            </div>

            <!-- Question -->
            <div class="mb-4">
                <label class="mb-1 font-semibold block">Question</label>
                <textarea
                    v-model="form.message"
                    class="p-2 rounded w-full border"
                    rows="4"
                    placeholder="Pose ta question ici..."
                    required
                ></textarea>
            </div>

            <!-- Bouton -->
            <button
                type="submit"
                class="px-6 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
                :disabled="form.processing"
            >
                Envoyer
            </button>
        </form>

        <!-- Réponse -->
        <div v-if="response" class="mt-8">
            <h2 class="mb-2 text-xl font-semibold">Réponse</h2>
            <div class="p-4 bg-gray-100 rounded whitespace-pre-wrap">
                {{ response }}
            </div>
        </div>
    </div>
</template>

<style scoped>
textarea {
    width: 100%;
    height: 100px;
    margin-bottom: 10px;
}
button {
    padding: 10px 20px;
    cursor: pointer;
    margin-bottom: 10px;
}
select {
    margin-bottom: 10px;
}
</style>
