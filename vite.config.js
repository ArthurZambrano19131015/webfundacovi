import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins:[
        laravel({
            input:[
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/db.js' 
            ],
            refresh: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            outDir: 'public',
            buildBase: '/',
            scope: '/',
            injectRegister: 'script',
            manifest: {
                name: 'Pro-Productos Apícolas FUNDACOVI',
                short_name: 'Apicultura App',
                description: 'Control de calidad y producción apícola Offline-First',
                theme_color: '#FBBF24', 
                background_color: '#FFFFFF',
                display: 'standalone',
                icons:[
                    {
                        src: '/icons/icon-192x192.png',
                        sizes: '192x192',
                        type: 'image/png'
                    },
                    {
                        src: '/icons/icon-512x512.png',
                        sizes: '512x512',
                        type: 'image/png'
                    }
                ]
            },
            workbox: {
                globPatterns:['**/*.{js,css,html,ico,png,svg,woff2}'],
                navigateFallback: '/offline', 
            }
        })
    ],
});