<template>
  <div class="flex flex-col min-h-screen font-sans bg-[#0F0F2F] text-[#E0E6F0]">

    <!-- HEADER FIXE -->
    <header class="fixed top-0 z-50 w-full bg-[#0F0F2F]/90 backdrop-blur border-b border-[#0F4F8F]">
      <div class="flex items-center px-6 py-4 space-x-4">
        <img :src="logo" alt="Nethra" class="w-10 h-10" />
        <span class="text-2xl font-bold text-[#52c5ff]">Nethra.IA</span>
      </div>
    </header>

    <!-- ESPACE HEADER -->
    <div class="h-20"></div>

    <!-- HERO -->
    <section class="relative px-6 py-24 overflow-hidden text-center">
      <div class="absolute inset-0 bg-gradient-to-b from-[#0F4F8F]/40 to-[#0F0F2F]"></div>
      <div class="relative z-10">
        <h1 class="mb-4 text-4xl font-bold md:text-5xl text-[#52c5ff] drop-shadow-lg">
          Nethra.IA 🟡
        </h1>
        <p class="max-w-3xl mx-auto mb-6 text-lg md:text-xl">
          Ton assistant personnel intelligent dédié à l’organisation,
          la gestion du temps et la clarté au quotidien.
        </p>
        <a
          href="/chat"
          class="inline-block px-8 py-4 font-semibold text-black transition bg-[#C96BFF] rounded-xl hover:shadow-[0_0_25px_rgba(201,107,255,0.6)]"
        >
          Accéder à l’assistant
        </a>
      </div>
    </section>

    <!-- SEPARATEUR -->
    <div class="h-2 bg-gradient-to-r from-[#0F0F2F] via-[#0F4F8F] to-[#0F0F2F]"></div>

    <!-- FONCTIONNALITÉS -->
    <section class="px-6 py-20 text-center">
      <h2 class="mb-4 text-3xl font-bold text-[#52c5ff]">Fonctionnalités</h2>
      <p class="mb-12 text-[#A8B4C8] max-w-2xl mx-auto">
        Nethra.IA centralise ton organisation personnelle et t’accompagne intelligemment au quotidien.
      </p>

      <div class="grid max-w-6xl gap-8 mx-auto md:grid-cols-4">
        <div
          v-for="(item, i) in [
            { icon:'🗓️', title:'Organisation du planning', text:'Structure tes journées, priorités et objectifs personnels ou professionnels.' },
            { icon:'🧠', title:'Contexte intelligent', text:'Nethra.IA mémorise le contexte de tes échanges pour des réponses cohérentes.' },
            { icon:'⚡', title:'Réponses en temps réel', text:'Les réponses s’affichent progressivement pour une expérience fluide et naturelle.' },
            { icon:'🎯', title:'Personnalisation avancée', text:'Ajuste le ton, le style et le comportement de l’assistant selon tes besoins.' }
          ]"
          :key="i"
          class="p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F0F2F]/50
                 transition-all duration-300
                 hover:border-[#C96BFF]
                 hover:shadow-[0_0_20px_rgba(201,107,255,0.45)]"
        >
          <div class="mb-3 text-4xl">{{ item.icon }}</div>
          <h3 class="mb-2 font-semibold text-[#52c5ff]">{{ item.title }}</h3>
          <p class="text-sm text-[#E0E6F0]">{{ item.text }}</p>
        </div>
      </div>
    </section>

    <!-- SEPARATEUR -->
    <div class="h-2 bg-gradient-to-r from-[#0F0F2F] via-[#0F2F4F] to-[#0F0F2F]"></div>

    <!-- PRICING -->
    <section class="px-6 py-20 text-center">
      <h2 class="mb-12 text-3xl font-bold text-[#52c5ff]">Formules</h2>
      <div class="grid max-w-5xl gap-8 mx-auto md:grid-cols-3">

        <div
          v-for="(plan, i) in [
            { name:'Free', desc:'Découvrir Nethra.IA et ses fonctionnalités de base.', price:'0€/mois' },
            { name:'Pro', desc:'Organisation avancée et historique complet.', price:'9,99€/mois' },
            { name:'Enterprise', desc:'Usage professionnel avec support prioritaire.', price:'29,99€/mois' }
          ]"
          :key="i"
          class="p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F0F2F]/50
                 transition-all duration-300
                 hover:border-[#C96BFF]
                 hover:shadow-[0_0_25px_rgba(201,107,255,0.45)]"
        >
          <h3 class="mb-4 text-xl font-semibold text-[#52c5ff]">{{ plan.name }}</h3>
          <p class="mb-4 text-sm">{{ plan.desc }}</p>
          <span class="block mb-6 text-2xl font-bold">{{ plan.price }}</span>
          <button class="px-6 py-2 rounded-xl bg-[#52c5ff] text-black font-semibold">
            Choisir
          </button>
        </div>

      </div>
    </section>

    <!-- SEPARATEUR -->
    <div class="h-2 bg-gradient-to-r from-[#0F0F2F] via-[#0F4F8F] to-[#0F0F2F]"></div>

    <!-- TÉMOIGNAGES -->
    <section class="px-6 py-20 text-center">
      <h2 class="mb-12 text-3xl font-bold text-[#52c5ff]">Témoignages</h2>
      <div class="grid max-w-5xl gap-8 mx-auto md:grid-cols-3">
        <div
          v-for="(t, i) in [
            { text:'Nethra.IA m’aide à structurer mes journées et à rester concentré sur l’essentiel.', name:'Alice' },
            { text:'Un assistant clair, simple et vraiment utile pour mon organisation personnelle.', name:'Bob' },
            { text:'La personnalisation de l’assistant fait toute la différence au quotidien.', name:'Clara' }
          ]"
          :key="i"
          class="p-6 rounded-xl bg-[#0F0F2F]/80 border border-[#0F0F2F]/50
                 hover:border-[#C96BFF]
                 hover:shadow-[0_0_20px_rgba(201,107,255,0.45)] transition"
        >
          <p class="italic">"{{ t.text }}"</p>
          <span class="block mt-4 font-semibold text-[#52c5ff]">— {{ t.name }}</span>
        </div>
      </div>
    </section>

    <!-- SEPARATEUR -->
    <div class="h-2 bg-gradient-to-r from-[#0F0F2F] via-[#0F2F4F] to-[#0F0F2F]"></div>

    <!-- FOOTER -->
    <footer class="px-6 py-10 text-center text-sm bg-[#0F0F2F] border-t border-[#0F4F8F]">
      <p>&copy; 2026 Nethra.IA — Assistant personnel intelligent</p>
      <div class="mt-3 space-x-4">
        <a href="/" class="hover:text-[#52c5ff]">Accueil</a>
        <a href="/legal" class="hover:text-[#52c5ff]">Mentions légales</a>
        <a href="#" class="hover:text-[#52c5ff]">Contact</a>
      </div>
      <p class="mt-4 text-xs opacity-60">
        Nethra.IA est un système d’intelligence artificielle. Les réponses peuvent contenir
        des erreurs et ne remplacent pas un avis professionnel humain.
      </p>
    </footer>

  </div>
</template>

<script setup lang="ts">
// Correction du logo pour Vite
const logo = new URL('/images/icons/nethra.png', import.meta.url).href;
</script>

<style scoped>
</style>
