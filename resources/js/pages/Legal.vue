<script setup lang="ts">
import HeaderFooterLayout from '@/layouts/HeaderFooterLayout.vue';
</script>

<template>
<HeaderFooterLayout>

  <!-- HERO -->
  <section class="relative px-6 py-24 overflow-hidden text-center">
    <div class="absolute inset-0 bg-gradient-to-b from-[#0F4F8F]/40 to-[#0F0F2F]"></div>
    <div class="relative z-10 max-w-3xl mx-auto">
      <h1 class="mb-4 text-4xl md:text-5xl font-bold text-[#52c5ff] drop-shadow-lg">
        Mentions légales & Politique de confidentialité
      </h1>
      <p class="mb-6 text-lg md:text-xl text-[#E0E6F0]">
        Toutes les informations ci-dessous sont fictives mais conformes aux exigences RGPD et AI Act.
      </p>
    </div>
  </section>

  <!-- SEPARATEUR -->
  <div class="h-2 bg-gradient-to-r from-[#0F0F2F] via-[#0F4F8F] to-[#0F0F2F]"></div>

  <!-- CONTENT -->
  <main class="max-w-5xl px-6 py-12 mx-auto space-y-12 text-left">

    <!-- Mentions légales -->
    <section>
      <h2 class="mb-4 text-3xl font-bold text-[#52c5ff]">1. Mentions légales</h2>
      <ul class="pl-6 space-y-2 list-disc text-[#E0E6F0]">
        <li>Éditeur : GPTClone Inc. (fictif)</li>
        <li>Hébergeur : Localhost / Railway (fictif)</li>
        <li>Directeur de publication : John Doe</li>
        <li>Contact : <a href="mailto:contact@gptclone.test" class="text-[#C96BFF] hover:underline">contact@gptclone.test</a></li>
      </ul>
    </section>

    <!-- Politique de confidentialité -->
    <section>
      <h2 class="mb-4 text-3xl font-bold text-[#52c5ff]">2. Politique de confidentialité</h2>
      <ul class="pl-6 space-y-2 list-disc text-[#E0E6F0]">
        <li>Types de données collectées : conversations, préférences utilisateur, cookies anonymes.</li>
        <li>Finalités du traitement : amélioration du service, personnalisation des réponses, statistiques anonymisées.</li>
        <li>Base légale : consentement explicite de l’utilisateur.</li>
        <li>Durée de conservation : 12 mois maximum pour les conversations, cookies 6 mois.</li>
        <li>Droits des utilisateurs : accès, rectification, suppression, portabilité via la page profil (fictif).</li>
        <li>Transferts hors UE : aucun pour ce projet.</li>
        <li>Cookies : bandeau de consentement (consentement explicite, possibilité de refuser).</li>
      </ul>
    </section>

    <!-- AI Act / Transparence IA -->
    <section>
      <h2 class="mb-4 text-3xl font-bold text-[#52c5ff]">3. Transparence AI Act</h2>
      <ul class="pl-6 space-y-2 list-disc text-[#E0E6F0]">
        <li>L'utilisateur interagit avec une IA (GPTClone).</li>
        <li>Modèles utilisés : GPT-5-mini (ou équivalent fictif).</li>
        <li>Disclaimer : les réponses peuvent contenir des erreurs, hallucinations possibles. Ne remplace pas un professionnel.</li>
        <li>Les conversations peuvent être utilisées pour améliorer le service (anonymisées).</li>
      </ul>
    </section>

  </main>

</HeaderFooterLayout>
</template>
