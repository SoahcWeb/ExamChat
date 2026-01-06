<template>
  <HeaderFooterLayout>
    <section
  id="top"
  class="relative px-6 py-16 md:py-16 overflow-hidden text-center scroll-mt-32 bg-cover bg-center"
  :style="{ backgroundImage: `url(${heroImage})` }"
>
  <!-- Overlay -->
  <div class="absolute inset-0 bg-gradient-to-b from-[#0F4F8F]/40 to-[#0F0F2F]"></div>

  <div class="relative z-10 flex flex-col items-center justify-center">
    <!-- Logo Nethra -->
    <img
      :src="nethraLogo"
      alt="Nethra Logo"
      class="w-32 h-32 md:w-40 md:h-40 mb-2 -mt-4"
    />

    <!-- Titre -->
    <h1 class="mb-1 text-4xl md:text-5xl font-bold text-[#52c5ff] drop-shadow-lg">
      Nethra.IA
    </h1>

    <!-- Description -->
    <p class="max-w-3xl mx-auto mb-6 text-lg md:text-xl text-[#E0E6F0]">
      Améliore ton organisation, booste ton focus et prends des décisions avec clarté, grâce à ton assistant personnel Nethra.
    </p>

    <!-- Bouton avec images corrigé -->
    <a
      href="/chat/models"
      class="inline-block w-48 h-14 md:w-56 md:h-16"
      @mouseenter="isHovered = true"
      @mouseleave="isHovered = false"
      :style="{
        backgroundImage: `url(${isHovered ? boutonActif : boutonInactif})`,
        backgroundSize: 'contain',      // <-- correction ici
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat'
      }"
    ></a>
  </div>
</section>

    <!-- SEPARATEUR -->
    <div class="h-2 bg-gradient-to-r from-[#0F0F2F] via-[#0F4F8F] to-[#0F0F2F]"></div>

    <!-- FONCTIONNALITÉS -->
    <section
      id="features"
      class="px-6 py-20 text-center scroll-mt-24"
    >
      <h2 class="mb-4 text-3xl font-bold text-[#52c5ff]">Fonctionnalités</h2>
      <p class="mb-12 text-[#A8B4C8] max-w-2xl mx-auto">
        Nethra.IA simplifie ton quotidien en centralisant tes outils,
        mémorisant tes préférences et te guidant pour rester productif.
      </p>

      <div class="grid max-w-6xl grid-cols-1 gap-8 mx-auto md:grid-cols-4 sm:grid-cols-2">
        <div
          v-for="(item, i) in features"
          :key="i"
          class="p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F0F2F]/50
                 transition-all duration-300
                 hover:border-[#C96BFF]
                 hover:shadow-[0_0_20px_rgba(201,107,255,0.45)]"
        >
          <div class="mb-3">
            <img :src="item.icon" alt="" class="object-contain w-32 h-32 mx-auto" />
          </div>
          <h3 class="mb-2 font-semibold text-[#52c5ff]">{{ item.title }}</h3>
          <p class="text-sm text-[#E0E6F0]">{{ item.text }}</p>
        </div>
      </div>
    </section>

    <!-- SEPARATEUR -->
    <div class="h-2 bg-gradient-to-r from-[#0F0F2F] via-[#0F2F4F] to-[#0F0F2F]"></div>

    <!-- PRICING -->
    <section
      id="pricing"
      class="px-6 py-20 text-center scroll-mt-24"
    >
      <h2 class="mb-4 text-3xl font-bold text-[#52c5ff]">Nos Offres</h2>
      <p class="mb-12 text-[#A8B4C8] max-w-2xl mx-auto">
        Choisissez l’offre qui correspond à votre rythme et vos besoins ! Profitez de Nethra.IA pour mieux organiser vos journées, gagner du temps et atteindre vos objectifs avec clarté et efficacité.
      </p>

      <div class="grid max-w-5xl grid-cols-1 gap-8 mx-auto md:grid-cols-3 sm:grid-cols-2">
        <div
          v-for="(plan, i) in plans"
          :key="i"
          class="flex flex-col justify-between p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F0F2F]/50
                 transition-all duration-300 transform hover:scale-105
                 hover:border-[#C96BFF]
                 hover:shadow-[0_10px_30px_rgba(201,107,255,0.4)]"
        >
          <div>
            <img
              :src="plan.image"
              alt=""
              class="object-contain w-40 h-40 mx-auto mb-4 shadow-lg rounded-xl"
            />

            <h3 class="mb-4 text-xl font-semibold text-[#52c5ff]">{{ plan.name }}</h3>
            <p class="mb-4 text-sm text-[#E0E6F0]">{{ plan.desc }}</p>

            <ul class="mb-4 text-sm text-[#A8B4C8] list-disc list-inside space-y-1">
              <li v-if="plan.name === 'Free'">Accès aux fonctionnalités de base de Nethra.IA pour découvrir l’assistant.</li>
              <li v-if="plan.name === 'Free'">Organisation simple de tes journées et priorités.</li>
              <li v-if="plan.name === 'Pro'">Historique complet de toutes tes interactions pour un suivi personnalisé.</li>
              <li v-if="plan.name === 'Pro'">Outils avancés pour planifier tes tâches et projets en profondeur.</li>
              <li v-if="plan.name === 'Pro'">Accès prioritaire aux nouveautés et mises à jour.</li>
              <li v-if="plan.name === 'Enterprise'">Support dédié et assistance personnalisée pour ton équipe.</li>
              <li v-if="plan.name === 'Enterprise'">Fonctionnalités collaboratives pour gérer plusieurs utilisateurs.</li>
              <li v-if="plan.name === 'Enterprise'">Intégrations avancées avec tes outils professionnels existants.</li>
            </ul>
          </div>

          <div>
            <span class="block mb-6 text-2xl font-bold text-[#52c5ff]">{{ plan.price }}</span>
            <button class="px-6 py-2 rounded-xl bg-[#C96BFF] text-black font-semibold hover:shadow-[0_0_25px_rgba(201,107,255,0.6)] transition w-full">
              Choisir
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- SEPARATEUR -->
    <div class="h-2 bg-gradient-to-r from-[#0F0F2F] via-[#0F4F8F] to-[#0F0F2F]"></div>

    <!-- TÉMOIGNAGES -->
    <section
      id="testimonials"
      class="px-6 py-20 text-center scroll-mt-24"
    >
      <h2 class="mb-12 text-3xl font-bold text-[#52c5ff]">Témoignages</h2>
      <p class="mb-12 text-[#A8B4C8] max-w-2xl mx-auto">
        Ce que nos utilisateurs pensent de Nethra.IA. Des retours authentiques pour te montrer l’impact réel de ton assistant personnel.
      </p>

      <div class="grid max-w-5xl grid-cols-1 gap-8 mx-auto md:grid-cols-3 sm:grid-cols-2">
        <div
          v-for="(t, i) in testimonials"
          :key="i"
          class="group flex flex-col items-center p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F0F2F]/50
                 hover:border-[#C96BFF]
                 hover:shadow-[0_0_20px_rgba(201,107,255,0.45)] transition"
        >
          <img
            :src="t.image"
            alt=""
            class="w-16 h-16 rounded-full mb-4 object-cover shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:shadow-[0_0_15px_rgba(201,107,255,0.6)]"
          />
          <p class="italic text-center">"{{ t.text }}"</p>
          <span class="block mt-4 font-semibold text-[#52c5ff]">— {{ t.name }}</span>
        </div>
      </div>
    </section>

    <!-- SEPARATEUR -->
    <div class="h-2 bg-gradient-to-r from-[#0F0F2F] via-[#0F2F4F] to-[#0F0F2F]"></div>

    <!-- FAQ -->
    <section
      id="faq"
      class="px-6 py-20 text-center scroll-mt-24"
    >
      <h2 class="mb-12 text-3xl font-bold text-[#52c5ff]">FAQ</h2>
      <p class="mb-12 text-[#A8B4C8] max-w-2xl mx-auto">
        Toutes les réponses aux questions que tu peux te poser sur Nethra.IA et son utilisation.
      </p>

      <div class="max-w-3xl mx-auto space-y-4 text-left">
        <div
          v-for="(faq, i) in faqs"
          :key="i"
          class="border border-[#0F4F8F] rounded-xl overflow-hidden bg-[#0F0F2F]/80"
        >
          <button
            @click="faq.open = !faq.open"
            class="w-full px-6 py-4 text-left text-[#52c5ff] font-semibold flex justify-between items-center hover:bg-[#0F4F8F]/20 transition-colors duration-300"
          >
            <span class="flex items-center gap-3">
              <img :src="faq.icon" alt="" class="object-contain w-12 h-12" />
              {{ faq.question }}
            </span>
            <span class="text-xl">{{ faq.open ? '−' : '+' }}</span>
          </button>

          <transition name="faq-slide">
            <div
              v-show="faq.open"
              class="px-6 py-4 text-[#E0E6F0] bg-[#0F0F2F]/70"
            >
              {{ faq.answer }}
            </div>
          </transition>
        </div>
      </div>
    </section>
  </HeaderFooterLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import HeaderFooterLayout from '@/layouts/HeaderFooterLayout.vue'

const heroImage = new URL('@/assets/images/hero/banner.jpg', import.meta.url).href
const nethraLogo = new URL('@/assets/icons/nethra.png', import.meta.url).href

// Bouton
const isHovered = ref(false)
const boutonActif = new URL('@/assets/images/buttons/bouton01.png', import.meta.url).href
const boutonInactif = new URL('@/assets/images/buttons/bouton02.png', import.meta.url).href

// Features
const featureImages = {
  calendar: new URL('@/assets/images/features/calendar.png', import.meta.url).href,
  brain: new URL('@/assets/images/features/brain.png', import.meta.url).href,
  lightning: new URL('@/assets/images/features/lightning.png', import.meta.url).href,
  target: new URL('@/assets/images/features/target.png', import.meta.url).href
}

const features = [
  { icon: featureImages.calendar, title:'Organisation du planning', text:'Structure tes journées et priorités pour rester concentré sur l’essentiel.' },
  { icon: featureImages.brain, title:'Contexte intelligent', text:'Nethra.IA se souvient de tes échanges pour des réponses cohérentes et personnalisées.' },
  { icon: featureImages.lightning, title:'Réponses en temps réel', text:'Reçois les réponses de ton assistant instantanément, comme si tu discutais avec un humain.' },
  { icon: featureImages.target, title:'Personnalisation avancée', text:'Ajuste ton assistant selon ton style et tes besoins pour qu’il devienne vraiment personnel.' }
]

// Plans
const plans = [
  { name:'Free', desc:'Découvrir Nethra.IA et ses fonctionnalités de base.', price:'0€/mois', image: new URL('@/assets/images/pricing/free.png', import.meta.url).href },
  { name:'Pro', desc:'Organisation avancée et historique complet.', price:'9,99€/mois', image: new URL('@/assets/images/pricing/pro.png', import.meta.url).href },
  { name:'Enterprise', desc:'Usage professionnel avec support prioritaire.', price:'29,99€/mois', image: new URL('@/assets/images/pricing/enterprise.png', import.meta.url).href }
]

// Témoignages
const testimonials = [
  { text:'Nethra.IA m’aide à organiser mes journées et à rester concentré.', name:'Alice', image: new URL('@/assets/images/testimonials/alice.png', import.meta.url).href },
  { text:'Un assistant clair et simple qui améliore vraiment mon quotidien.', name:'Bob', image: new URL('@/assets/images/testimonials/bob.png', import.meta.url).href },
  { text:'La personnalisation de Nethra.IA fait toute la différence.', name:'Clara', image: new URL('@/assets/images/testimonials/clara.png', import.meta.url).href }
]

// FAQ
const faqs = reactive([
  { icon: new URL('@/assets/icons/faq/question.png', import.meta.url).href, question:'Qu’est-ce que Nethra.IA ?', answer:'Nethra.IA est ton assistant personnel intelligent pour gérer ton organisation et tes priorités quotidiennes.', open:false },
  { icon: new URL('@/assets/icons/faq/settings.png', import.meta.url).href, question:'Comment personnaliser l’assistant ?', answer:'Tu peux ajuster le ton, le style et le comportement directement dans l’interface de Nethra.IA.', open:false },
  { icon: new URL('@/assets/icons/faq/money.png', import.meta.url).href, question:'Y a-t-il une version gratuite ?', answer:'Oui, la formule Free permet de découvrir toutes les fonctionnalités de base sans limite de temps.', open:false },
  { icon: new URL('@/assets/icons/faq/calendar.png', import.meta.url).href, question:'Puis-je synchroniser Nethra.IA avec mon calendrier ?', answer:'Oui, Nethra.IA peut s’intégrer avec les principaux calendriers pour organiser tes journées.', open:false },
  { icon: new URL('@/assets/icons/faq/lock.png', import.meta.url).href, question:'Mes données sont-elles sécurisées ?', answer:'Toutes tes données sont protégées et utilisées uniquement pour améliorer ton expérience avec l’assistant.', open:false }
])
</script>

<style scoped>
.faq-slide-enter-active,
.faq-slide-leave-active {
  transition: all 0.3s ease;
}
.faq-slide-enter-from,
.faq-slide-leave-to {
  max-height: 0;
  opacity: 0;
  padding-top: 0;
  padding-bottom: 0;
}
.faq-slide-enter-to,
.faq-slide-leave-from {
  max-height: 500px;
  opacity: 1;
  padding-top: 1rem;
  padding-bottom: 1rem;
}
</style>
