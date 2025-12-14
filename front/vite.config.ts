/* eslint-env node */
import { reactRouter } from "@react-router/dev/vite";
import tailwindcss from "@tailwindcss/vite";
import { defineConfig } from "vitest/config";
import tsconfigPaths from "vite-tsconfig-paths";

export default defineConfig({
  plugins: [
    tailwindcss(),
    tsconfigPaths(),
    !process.env.VITEST && reactRouter(),
  ],
  test: {
    globals: true,
    environment: "happy-dom",
    setupFiles: "./app/test/setup.ts",
    css: true,
  },
});
