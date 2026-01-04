<template>
  <div class="flex flex-col min-h-screen font-sans bg-[#0F0F2F] text-[#E0E6F0]">

    <!-- HEADER FIXE -->
    <header class="fixed top-0 z-50 w-full bg-[#0F0F2F]/90 backdrop-blur border-b border-[#0F4F8F]">
      <div class="flex items-center justify-between px-6 py-4">

        <!-- LOGO -->
        <div class="flex items-center space-x-4">
          <img
            :src="logo"
            alt="Nethra"
            class="w-10 h-10 sm:w-12 sm:h-12 md:w-[3.75rem] md:h-[3.75rem] lg:w-16 lg:h-16 object-contain"
          />
          <span class="text-2xl font-bold text-[#52c5ff]">Nethra.IA</span>
        </div>

        <!-- NAV DESKTOP -->
        <nav class="relative items-center hidden space-x-6 md:flex">

          <!-- ACCUEIL + DROPDOWN -->
          <div
            class="relative group"
          >
            <a
              href="/#top"
              class="hover:text-[#52c5ff] transition"
            >
              Accueil
            </a>

            <!-- MENU DÉROULANT -->
            <div
              class="absolute left-0 mt-3 w-48 rounded-xl bg-[#0F0F2F] border border-[#0F4F8F]
                     opacity-0 invisible group-hover:opacity-100 group-hover:visible
                     transition-all duration-300 shadow-lg"
            >
              <a href="/#features" class="block px-4 py-3 hover:bg-[#0F4F8F]/30 transition">Fonctionnalités</a>
              <a href="/#pricing" class="block px-4 py-3 hover:bg-[#0F4F8F]/30 transition">Offres</a>
              <a href="/#testimonials" class="block px-4 py-3 hover:bg-[#0F4F8F]/30 transition">Témoignages</a>
              <a href="/#faq" class="block px-4 py-3 hover:bg-[#0F4F8F]/30 transition">FAQ</a>
            </div>
          </div>

          <a href="/chat" class="hover:text-[#52c5ff] transition">Chat</a>
          <a href="/legal" class="hover:text-[#52c5ff] transition">Mentions légales</a>
        </nav>

        <!-- BOUTON MOBILE -->
        <button
          @click="mobileOpen = !mobileOpen"
          class="text-2xl md:hidden"
        >
          ☰
        </button>
      </div>

      <!-- MENU MOBILE -->
      <div
        v-show="mobileOpen"
        class="md:hidden px-6 pb-6 space-y-2 border-t border-[#0F4F8F]"
      >
        <a href="/#top" class="block py-2" @click="closeMobile">Accueil</a>
        <a href="/#features" class="block py-2" @click="closeMobile">Fonctionnalités</a>
        <a href="/#pricing" class="block py-2" @click="closeMobile">Offres</a>
        <a href="/#testimonials" class="block py-2" @click="closeMobile">Témoignages</a>
        <a href="/#faq" class="block py-2" @click="closeMobile">FAQ</a>
        <a href="/chat" class="block py-2" @click="closeMobile">Chat</a>
        <a href="/legal" class="block py-2" @click="closeMobile">Mentions légales</a>
      </div>
    </header>

    <!-- ESPACE HEADER -->
    <div class="h-20"></div>

    <!-- CONTENU -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- FOOTER -->
    <footer class="px-6 py-6 text-center text-sm bg-[#0F0F2F] border-t border-[#0F4F8F] mt-auto">
      <p>&copy; 2026 Nethra.IA — Assistant personnel intelligent</p>
      <p class="mt-2 text-xs opacity-60">
        Nethra.IA est un système d’intelligence artificielle. Les réponses peuvent contenir
        des erreurs et ne remplacent pas un avis professionnel humain.
      </p>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const logo = new URL('@/assets/icons/nethra.png', import.meta.url).href;
const mobileOpen = ref(false);

const closeMobile = () => {
  mobileOpen.value = false;
};
</script>

<style scoped>
html {
  scroll-behavior: smooth;
}
</style>
