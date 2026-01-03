<script setup lang="ts">
// Aucun script nécessaire pour cette page statique
</script>

<template>
  <div class="flex flex-col min-h-screen font-sans text-gray-900">

    <!-- HEADER -->
    <header class="px-6 py-8 text-center text-white bg-purple-600">
      <h1 class="mb-2 text-4xl font-bold">Mentions légales & Politique de confidentialité</h1>
      <p class="text-lg">Toutes les informations ci-dessous sont fictives mais conformes aux exigences RGPD et AI Act.</p>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 max-w-5xl px-6 py-12 mx-auto space-y-12">

      <!-- Mentions légales -->
      <section>
        <h2 class="mb-4 text-2xl font-semibold">1. Mentions légales</h2>
        <ul class="pl-6 space-y-2 list-disc">
          <li>Éditeur : GPTClone Inc. (fictif)</li>
          <li>Hébergeur : Localhost / Railway (fictif)</li>
          <li>Directeur de publication : John Doe</li>
          <li>Contact : <a href="mailto:contact@gptclone.test" class="text-purple-600 hover:underline">contact@gptclone.test</a></li>
        </ul>
      </section>

      <!-- Politique de confidentialité -->
      <section>
        <h2 class="mb-4 text-2xl font-semibold">2. Politique de confidentialité</h2>
        <ul class="pl-6 space-y-2 list-disc">
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
        <h2 class="mb-4 text-2xl font-semibold">3. Transparence AI Act</h2>
        <ul class="pl-6 space-y-2 list-disc">
          <li>L'utilisateur interagit avec une IA (GPTClone).</li>
          <li>Modèles utilisés : GPT-5-mini (ou équivalent fictif).</li>
          <li>Disclaimer : les réponses peuvent contenir des erreurs, hallucinations possibles. Ne remplace pas un professionnel.</li>
          <li>Les conversations peuvent être utilisées pour améliorer le service (anonymisées).</li>
        </ul>
      </section>

    </main>

    <!-- FOOTER -->
    <footer class="px-6 py-8 text-center text-white bg-gray-900">
      <p>&copy; 2026 GPTClone. Tous droits réservés.</p>
      <div class="mt-2 space-x-4">
        <a href="/" class="hover:underline">Accueil</a>
        <a href="/legal" class="hover:underline">Mentions légales</a>
        <a href="#" class="hover:underline">Contact</a>
      </div>
    </footer>

  </div>
</template>
