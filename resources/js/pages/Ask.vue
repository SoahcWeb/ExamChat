<template>
    <div>
        <h1>Mini ChatGPT (Vue)</h1>

        <!-- Choix du modèle -->
        <div v-if="models.length">
            <label for="model">Choisir le modèle :</label>
            <select v-model="selectedModel" id="model">
                <option
                    v-for="model in models"
                    :key="model.id"
                    :value="model.id"
                >
                    {{ model.id }}
                </option>
            </select>
        </div>

        <!-- Question -->
        <textarea
            v-model="question"
            placeholder="Pose ta question ici..."
        ></textarea>
        <button @click="sendQuestion">Envoyer</button>

        <!-- Réponse -->
        <div v-if="response">
            <h2>Réponse :</h2>
            <pre>{{ response }}</pre>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            question: '',
            response: null,
            models: [],
            selectedModel: null,
        };
    },
    async created() {
        try {
            // Charger les modèles disponibles depuis le backend
            const res = await axios.get('/ask/models');
            this.models = res.data.models;

            // Sélectionner le premier modèle par défaut
            if (this.models.length) {
                this.selectedModel = this.models[0].id;
            }
        } catch (err) {
            console.error('Erreur lors du chargement des modèles :', err);
        }
    },
    methods: {
        async sendQuestion() {
            if (!this.question || !this.selectedModel) return;

            try {
                const res = await axios.post('/ask', {
                    question: this.question,
                    model: this.selectedModel,
                });
                // Récupérer uniquement le texte renvoyé par l'IA
                this.response = res.data.response;
            } catch (err) {
                console.error(err);
                alert('Erreur lors de l’envoi de la question.');
            }
        },
    },
};
</script>

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

pre {
    background: #f4f4f4;
    padding: 10px;
    border-radius: 5px;
    white-space: pre-wrap;
}
</style>
