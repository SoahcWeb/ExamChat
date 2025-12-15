import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { InertiaProgress } from '@inertiajs/progress';

// Barre de chargement Inertia
InertiaProgress.init({
  color: '#4B9CE2',
  showSpinner: true,
});

// Map Vite de toutes les pages
const pages = import.meta.glob('./Pages/**/*.vue');

createInertiaApp({
  resolve: (name) => {
    const page = pages[`./Pages/${name}.vue`];
    if (!page) {
      throw new Error(`Page introuvable: ./Pages/${name}.vue`);
    }
    return page();
  },

  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el);
  },
});
