import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// En dev (Docker), les routes servies par Symfony sont relayées vers le conteneur nginx :
// la SPA et l'API partagent ainsi la même origine (cookies de session, pas de CORS).
const backend = process.env.BACKEND_URL ?? 'http://nginx:80';
const routesSymfony = ['/api', '/admin', '/login', '/logout', '/uploads', '/images'];

export default defineConfig({
    plugins: [react()],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        // Les événements de fichiers ne traversent pas les volumes Windows/WSL : on scrute à intervalle régulier
        watch: { usePolling: true },
        proxy: Object.fromEntries(routesSymfony.map((route) => [route, { target: backend, changeOrigin: false }])),
    },
    build: {
        outDir: 'dist',
    },
});
