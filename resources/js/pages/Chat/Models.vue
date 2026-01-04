<script setup lang="ts">
import HeaderFooterLayout from '@/layouts/HeaderFooterLayout.vue'

// Import images depuis assets
import imgCreativite from '../../assets/images/models/nethra-creativite.png'
import imgPhilosophe from '../../assets/images/models/nethra-philosophe.png'
import imgStratege from '../../assets/images/models/nethra-stratege.png'
import imgPersonnalise from '../../assets/images/models/nethra-personnalise.png'

const presetModels = [
  {
    id: 'CoachCréativité',
    title: 'Nethra Créativité',
    desc: 'Stimule tes idées et structure tes projets créatifs.',
    img: imgCreativite,
  },
  {
    id: 'PhilosopheModerne',
    title: 'Nethra Philosophe',
    desc: 'Clarifie tes valeurs et tes choix.',
    img: imgPhilosophe,
  },
  {
    id: 'StratègeDeVie',
    title: 'Nethra Stratège',
    desc: 'Organise ta vie et atteins tes objectifs.',
    img: imgStratege,
  },
  {
    id: 'custom',
    title: 'Nethra Personnalisé',
    desc: 'Crée ton assistant sur mesure.',
    img: imgPersonnalise,
  },
]

function selectModel(modelId: string) {
  localStorage.setItem('selectedModel', modelId)
}
</script>

<template>
  <HeaderFooterLayout>
    <section class="px-6 py-20 text-center">
      <!-- TITRE ET TEXTE INTRO -->
      <h1 class="mb-4 text-3xl md:text-4xl font-bold text-[#52c5ff] drop-shadow-lg">
        Trouvez votre Nethra idéal
      </h1>
      <p class="mb-12 text-[#A8B4C8] max-w-2xl mx-auto text-lg">
        Choisissez l’assistant qui vous correspond et commencez à organiser vos journées, prendre des décisions plus claires et booster vos projets. Chaque Nethra est prêt à vous accompagner dans votre expérience.
      </p>

      <!-- CARTES ASSISTANTS -->
      <div class="grid max-w-5xl grid-cols-1 gap-8 mx-auto md:grid-cols-4">
        <div
          v-for="model in presetModels"
          :key="model.id"
          @click="selectModel(model.id)"
          class="group flex flex-col items-center p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F0F2F]/50
                 cursor-pointer transition-all duration-300
                 hover:border-[#C96BFF]
                 hover:shadow-[0_0_20px_rgba(201,107,255,0.45)]"
        >
          <!-- Image avec effet zoom -->
          <div class="w-32 h-32 mb-4 rounded-full overflow-hidden border-2 border-[#C96BFF]">
            <img
              :src="model.img"
              :alt="model.title"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110 group-hover:shadow-[0_0_15px_rgba(201,107,255,0.6)]"
            />
          </div>

          <h3 class="mb-2 font-bold text-lg text-[#52c5ff] text-center">{{ model.title }}</h3>
          <p class="text-sm text-[#E0E6F0] text-center">{{ model.desc }}</p>
        </div>
      </div>

      <a
        href="/chat"
        class="inline-block px-10 py-4 mt-14 font-semibold text-black bg-[#C96BFF] rounded-xl hover:shadow-lg transition"
      >
        Commencer la discussion
      </a>
    </section>
  </HeaderFooterLayout>
</template>
