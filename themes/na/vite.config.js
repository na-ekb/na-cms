import { defineConfig } from 'vite';
import { resolve, basename } from 'path';

const input = {
    main: resolve(__dirname, 'assets/js/main.js'),
    css: resolve(__dirname, 'assets/sass/app.scss'),
}

const themeName = 'na';

export default defineConfig({
    base: `/themes/${themeName}/assets/build/`,
    build: {
        rollupOptions: { input },
        manifest: true,
        emptyOutDir: true,
        outDir: resolve(__dirname, 'assets/build'),
    },
    server: {
        hmr: {
            protocol: 'ws',
        },
    }
})
