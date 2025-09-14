import { defineConfig } from "eslint/config";
import globals from "globals";
import path from "node:path";
import { fileURLToPath } from "node:url";
import js from "@eslint/js";
import { FlatCompat } from "@eslint/eslintrc";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const compat = new FlatCompat({
    baseDirectory: __dirname,
    recommendedConfig: js.configs.recommended,
    allConfig: js.configs.all
});

export default defineConfig([{
    extends: compat.extends("eslint:recommended"),

    languageOptions: {
        globals: {
            ...globals.browser,
            Camdram: "writable",
            require: "readonly",
        },

        ecmaVersion: "latest",
        sourceType: "module",
    },

    rules: {
        indent: ["error", 4],
        "linebreak-style": ["error", "unix"],

        quotes: ["error", "double", {
            avoidEscape: true,
        }],

        semi: ["error", "always"],

        "no-unused-vars": ["error", {
            argsIgnorePattern: "^_",
        }],

        "guard-for-in": ["error"],
        "no-var": ["error"],
    },
}]);