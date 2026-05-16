import js from '@eslint/js';
import ts from 'typescript-eslint';
import react from 'eslint-plugin-react';
import reactHooks from 'eslint-plugin-react-hooks';
import prettier from 'eslint-config-prettier';
import globals from 'globals';

export default ts.config(
    {
        ignores: [
            'node_modules/',
            'vendor/',
            'public/build/',
            'storage/',
            'bootstrap/cache/',
            'resources/js/types/generated.d.ts',
        ],
    },
    js.configs.recommended,
    ...ts.configs.recommended,
    {
        files: ['resources/js/**/*.{ts,tsx}'],
        languageOptions: {
            ecmaVersion: 'latest',
            sourceType: 'module',
            globals: { ...globals.browser },
            parserOptions: {
                ecmaFeatures: { jsx: true },
            },
        },
        plugins: {
            react,
            'react-hooks': reactHooks,
        },
        settings: {
            react: { version: 'detect' },
        },
        rules: {
            ...react.configs.recommended.rules,
            ...react.configs['jsx-runtime'].rules,
            ...reactHooks.configs.recommended.rules,
            'react/prop-types': 'off',
        },
    },
    // Disable ESLint rules that would conflict with Prettier formatting.
    // Must be last so it wins over the configs above.
    prettier,
);
