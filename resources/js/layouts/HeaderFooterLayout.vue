<template>
  <div class="flex flex-col min-h-screen font-sans bg-[#0F0F2F] text-[#E0E6F0]">

    <!-- HEADER FIXE -->
    <header class="fixed top-0 z-50 w-full bg-[#0F0F2F]/90 backdrop-blur border-b border-[#0F4F8F]">
      <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center space-x-4">
          <img :src="logo" alt="Nethra" class="w-10 h-10" />
          <span class="text-2xl font-bold text-[#52c5ff]">Nethra.IA</span>
        </div>
        <nav class="space-x-4">
          <a href="/" class="hover:text-[#52c5ff] transition">Accueil</a>
          <a href="/chat" class="hover:text-[#52c5ff] transition">Chat</a>
          <a href="/legal" class="hover:text-[#52c5ff] transition">Mentions légales</a>
        </nav>
      </div>
    </header>

    <!-- ESPACE HEADER -->
    <div class="h-20"></div>

    <!-- CONTENU PRINCIPAL -->
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
const logo = new URL('/images/icons/nethra.png', import.meta.url).href;
</script>

<style scoped>
</style>
