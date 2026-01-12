import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { createPinia } from 'pinia';
import { initializeTheme } from './composables/useAppearance';
import Vue3Toastify from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';

const appName = import.meta.env.VITE_APP_NAME || 'E-Commerce Platform';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const pinia = createPinia()
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue)
            .use(Vue3Toastify, {
                autoClose: 3000,
                position: "top-right",
            })
            .component('EasyDataTable', Vue3EasyDataTable)
            .mount(el);
    },
    progress: {
        color: '#007CC3',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
