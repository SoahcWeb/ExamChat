<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';

const consent = ref<string | null>(null);
const visible = ref(false);

let firstFocusable: HTMLElement | null = null;
let lastFocusable: HTMLElement | null = null;

onMounted(() => {
  const stored = localStorage.getItem('cookie_consent');
  consent.value = stored;
  if (!stored) {
    // Affichage du banner après un petit délai
    setTimeout(() => {
      visible.value = true;
      nextTick(setFocusTrap);
    }, 500);
  }
});

const acceptCookies = () => {
  localStorage.setItem('cookie_consent', 'accepted');
  consent.value = 'accepted';
  visible.value = false;
};

const refuseCookies = () => {
  localStorage.setItem('cookie_consent', 'refused');
  consent.value = 'refused';
  visible.value = false;
};

// Gestion du focus trap
const setFocusTrap = () => {
  const banner = document.getElementById('cookie-banner');
  if (!banner) return;

  const focusableElements = banner.querySelectorAll<HTMLElement>(
    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
  );
  if (focusableElements.length === 0) return;

  firstFocusable = focusableElements[0];
  lastFocusable = focusableElements[focusableElements.length - 1];

  firstFocusable.focus();

  banner.addEventListener('keydown', handleTabKey);
};

const handleTabKey = (e: KeyboardEvent) => {
  if (e.key !== 'Tab') return;
  if (!firstFocusable || !lastFocusable) return;

  if (e.shiftKey) {
    // Shift + Tab
    if (document.activeElement === firstFocusable) {
      e.preventDefault();
      lastFocusable.focus();
    }
  } else {
    // Tab
    if (document.activeElement === lastFocusable) {
      e.preventDefault();
      firstFocusable.focus();
    }
  }
};
</script>

<template>
  <transition
    name="cookie-banner"
    enter-active-class="transition duration-500 ease-out"
    leave-active-class="transition duration-300 ease-in"
    enter-from-class="translate-y-6 opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="translate-y-6 opacity-0"
  >
    <div
      v-if="!consent && visible"
      id="cookie-banner"
      class="fixed bottom-0 left-0 z-50 w-full bg-[#0F0F2F] border-t border-[#0F4F8F] px-4 py-4 md:px-6 md:py-4"
      role="dialog"
      aria-live="polite"
      aria-label="Bandeau de consentement aux cookies"
    >
      <div class="flex flex-col max-w-5xl gap-3 mx-auto md:flex-row md:items-center md:justify-between">
        <p class="text-sm md:text-base text-[#E0E6F0] leading-relaxed">
          Nous utilisons des cookies à des fins de fonctionnement et d’amélioration du service.
          Vous pouvez accepter ou refuser leur utilisation.
          <a
            href="/legal"
            class="underline text-[#C96BFF] hover:text-[#52c5ff] focus:outline-none focus:ring-4 focus:ring-[#C96BFF]/50 rounded"
          >
            En savoir plus
          </a>.
        </p>

        <div class="flex flex-col gap-2 mt-2 sm:flex-row sm:gap-3 md:mt-0">
          <button
            @click="acceptCookies"
            class="w-full sm:w-auto px-5 py-3 rounded-lg bg-[#52c5ff] text-[#0F0F2F] font-semibold hover:opacity-90 focus:outline-none focus:ring-4 focus:ring-[#52c5ff] focus:ring-offset-2"
          >
            Accepter
          </button>

          <button
            @click="refuseCookies"
            class="w-full sm:w-auto px-5 py-3 rounded-lg border-2 border-[#52c5ff] text-[#52c5ff] hover:bg-[#52c5ff]/10 font-semibold focus:outline-none focus:ring-4 focus:ring-[#52c5ff] focus:ring-offset-2"
          >
            Refuser
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>
