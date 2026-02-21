import js from '@eslint/js';
import pluginVue from 'eslint-plugin-vue';
import tseslint from 'typescript-eslint';
import configPrettier from '@vue/eslint-config-prettier';
import globals from 'globals';
import vueParser from 'vue-eslint-parser'; // Импортируем парсер явно

export default tseslint.config(
  js.configs.recommended,
  ...tseslint.configs.recommended,
  ...pluginVue.configs['flat/recommended'],
  configPrettier,
  {
    // Указываем, какие файлы обрабатывать этим блоком
    files: ['**/*.vue', '**/*.ts'],
    languageOptions: {
      parser: vueParser, // Главный парсер для Vue файлов
      parserOptions: {
        parser: tseslint.parser, // Парсер для TS внутри <script>
        sourceType: 'module',
        ecmaVersion: 'latest',
        extraFileExtensions: ['.vue']
      },
      globals: {
        ...globals.browser,
        ...globals.node
      }
    },
    rules: {
      'vue/multi-word-component-names': 'off',
      'vue/require-default-prop': 'off',
      '@typescript-eslint/no-unused-vars': 'warn',
      'no-console': 'off'
    }
  }
);
