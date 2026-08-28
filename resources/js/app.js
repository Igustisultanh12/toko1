import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'SIBALOG POS';

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.component('Head', Head);
        app.component('Link', Link);
        
        // Helper global format rupiah
        app.config.globalProperties.$formatRupiah = (val) => {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val || 0);
        };

        app.mount(el);
    },
    progress: {
        color: '#00AA13',
        showSpinner: true,
    },
});
