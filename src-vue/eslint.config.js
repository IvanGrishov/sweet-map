import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import tseslint from 'typescript-eslint';
import configPrettier from '@vue/eslint-config-prettier';
import globals from 'globals';
import vueParser from 'vue-eslint-parser';

export default tseslint.config(
  // 1. Базовые конфиги
  js.configs.recommended,
  ...tseslint.configs.recommended,
  ...pluginVue.configs['flat/recommended'],
  configPrettier,
  // 2. Глобальные настройки
  {
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
        wpData: 'readonly' // Чтобы window.wpData не вызывал ошибок
      }
    }
  },
  {
    files: ['**/*.vue', '**/*.ts'],
    languageOptions: {
      parser: vueParser,
      parserOptions: {
        parser: tseslint.parser,
        sourceType: 'module',
        ecmaVersion: 'latest',
        extraFileExtensions: ['.vue']
      }
    },
    rules: {
      'vue/multi-word-component-names': 'off',
      'vue/require-default-prop': 'off',
      '@typescript-eslint/no-unused-vars': 'warn',
      'no-console': 'off',
      'no-undef': 'off'
    }
  }
);
