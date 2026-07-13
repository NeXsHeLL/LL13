import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import process from 'node:process';
import { defineConfig } from 'vite';

const devServerOrigin = process.env.VITE_DEV_SERVER_ORIGIN;
const devServerUrl = devServerOrigin ? new URL(devServerOrigin) : null;
const devClientOrigin = process.env.VITE_DEV_CLIENT_ORIGIN;

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.jsx',
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    esbuild: {
        jsx: 'automatic',
    },
    server: devServerUrl
        ? {
              host: process.env.VITE_DEV_SERVER_HOST ?? '0.0.0.0',
              port: Number(process.env.VITE_DEV_SERVER_PORT ?? devServerUrl.port),
              strictPort: true,
              origin: devServerUrl.origin,
              cors: devClientOrigin ? { origin: devClientOrigin } : undefined,
              hmr: {
                  host: devServerUrl.hostname,
                  clientPort: Number(devServerUrl.port),
                  protocol: devServerUrl.protocol.replace(':', ''),
              },
          }
        : undefined,
});
