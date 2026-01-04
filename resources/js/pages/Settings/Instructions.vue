<script setup>
import { useForm } from '@inertiajs/vue3'
import HeaderFooterLayout from '@/layouts/HeaderFooterLayout.vue'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
  instructions: String
})

const form = useForm({
  instructions: props.instructions ?? '',
  tone: '',
  style: '',
  specialties: '',
  language: ''
})

const submit = () => {
  // Combine tous les champs pour créer l'instruction finale
  const combinedInstructions = `
Ton de l'assistant : ${form.tone}
Style de réponses : ${form.style}
Domaines de spécialité : ${form.specialties}
Langue préférée : ${form.language}
Instructions libres : ${form.instructions}
`
  form.post('/settings/instructions', { instructions: combinedInstructions })
}

const goBackToChat = () => {
  Inertia.visit('/chat')
}
</script>

<template>
  <HeaderFooterLayout>
    <div class="h-20"></div>

    <!-- Bouton retour -->
    <div class="flex justify-start max-w-5xl mx-auto mb-6">
      <button
        @click="goBackToChat"
        class="px-6 py-2 rounded-xl bg-[#C96BFF] text-black font-semibold
               hover:shadow-[0_0_15px_rgba(201,107,255,0.6)] transition"
      >
        ← Retour au chat
      </button>
    </div>

    <!-- Contenu principal -->
    <div class="max-w-5xl mx-auto space-y-6">

      <h1 class="mb-4 text-2xl font-bold text-[#52c5ff] text-center">
        Personnalisation de l’assistant
      </h1>
      <p class="mb-6 text-[#A8B4C8] text-center">
        Complète les cartes ci-dessous pour que l’IA réponde exactement comme tu le souhaites.
      </p>

      <!-- Grille principale : 4 cartes + instructions libres -->
      <div class="grid gap-6 md:grid-cols-4">

        <!-- Carte Ton -->
        <div class="flex flex-col p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F4F8F]
                    transition hover:border-[#C96BFF] hover:shadow-[0_0_20px_rgba(201,107,255,0.45)]">
          <h3 class="mb-2 font-semibold text-[#52c5ff]">Ton de l’assistant</h3>
          <p class="mb-3 text-sm text-[#A8B4C8]">Choisis le ton général de l’IA (ex: amical, formel, humoristique).</p>
          <input
            v-model="form.tone"
            type="text"
            placeholder="Ex: amical"
            class="w-full p-3 rounded-xl border border-[#0F4F8F] bg-[#0F0F2F] text-[#E0E6F0]"
          />
        </div>

        <!-- Carte Style -->
        <div class="flex flex-col p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F4F8F]
                    transition hover:border-[#C96BFF] hover:shadow-[0_0_20px_rgba(201,107,255,0.45)]">
          <h3 class="mb-2 font-semibold text-[#52c5ff]">Style des réponses</h3>
          <p class="mb-3 text-sm text-[#A8B4C8]">Indique si les réponses doivent être synthétiques, détaillées ou explicatives.</p>
          <input
            v-model="form.style"
            type="text"
            placeholder="Ex: synthétique"
            class="w-full p-3 rounded-xl border border-[#0F4F8F] bg-[#0F0F2F] text-[#E0E6F0]"
          />
        </div>

        <!-- Carte Domaines de spécialité -->
        <div class="flex flex-col p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F4F8F]
                    transition hover:border-[#C96BFF] hover:shadow-[0_0_20px_rgba(201,107,255,0.45)]">
          <h3 class="mb-2 font-semibold text-[#52c5ff]">Domaines de spécialité</h3>
          <p class="mb-3 text-sm text-[#A8B4C8]">Indique les sujets où l’IA doit exceller (ex: pédagogie, organisation, codage).</p>
          <input
            v-model="form.specialties"
            type="text"
            placeholder="Ex: pédagogie, organisation"
            class="w-full p-3 rounded-xl border border-[#0F4F8F] bg-[#0F0F2F] text-[#E0E6F0]"
          />
        </div>

        <!-- Carte Langue -->
        <div class="flex flex-col p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F4F8F]
                    transition hover:border-[#C96BFF] hover:shadow-[0_0_20px_rgba(201,107,255,0.45)]">
          <h3 class="mb-2 font-semibold text-[#52c5ff]">Langue préférée</h3>
          <p class="mb-3 text-sm text-[#A8B4C8]">Choisis la langue principale des réponses (ex: français, anglais).</p>
          <input
            v-model="form.language"
            type="text"
            placeholder="Ex: français"
            class="w-full p-3 rounded-xl border border-[#0F4F8F] bg-[#0F0F2F] text-[#E0E6F0]"
          />
        </div>
      </div>

      <!-- Instructions libres (100% largeur) -->
      <div class="p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F4F8F]
                  transition hover:border-[#C96BFF] hover:shadow-[0_0_20px_rgba(201,107,255,0.45)]">
        <h3 class="mb-2 font-semibold text-[#52c5ff]">Instructions libres</h3>
        <p class="mb-3 text-sm text-[#A8B4C8]">Complète avec toute information supplémentaire que l’IA doit connaître.</p>
        <textarea
          v-model="form.instructions"
          class="w-full h-32 p-4 rounded-xl border border-[#0F4F8F] bg-[#0F0F2F] text-[#E0E6F0]"
          placeholder="Ex: Tu es un assistant pédagogique, réponses courtes, ton professionnel..."
        />
      </div>

      <!-- Bouton Sauvegarder -->
      <div class="flex justify-center">
        <button
          @click="submit"
          class="px-6 py-2 mt-4 rounded-xl bg-[#52c5ff] text-black font-semibold hover:shadow-[0_0_15px_rgba(82,197,255,0.6)] transition"
          :disabled="form.processing"
        >
          Sauvegarder
        </button>
      </div>

      <p v-if="form.recentlySuccessful" class="mt-2 text-center text-green-500">
        Instructions sauvegardées avec succès !
      </p>
    </div>

    <div class="h-20"></div>
  </HeaderFooterLayout>
</template>
