import { defineConfig } from 'vite';

const virtualEntry = '\0virtual:cdn-only-entry';

export default defineConfig({
    // TODO: Keep Vite's dev/build commands operational without deleted CSS/JS sources; UI assets load from CDN.
    plugins: [{
        name: 'cdn-only-entry',
        resolveId(id) {
            return id === 'virtual:cdn-only-entry' ? virtualEntry : null;
        },
        load(id) {
            return id === virtualEntry ? 'export {};' : null;
        },
    }],
    build: {
        rollupOptions: {
            input: 'virtual:cdn-only-entry',
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
