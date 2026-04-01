import antfu from '@antfu/eslint-config';

export default antfu({
    ignores: ['resources/js/ziggy.js'],
    vue: true,
    rules: {
        'style/arrow-parens': ['error', 'always'],
        'style/indent': ['error', 4],
        'style/indent-binary-ops': 'off',
        'style/member-delimiter-style': [
            'error',
            {
                multiline: {
                    delimiter: 'semi',
                    requireLast: true,
                },
                multilineDetection: 'brackets',
                singleline: {
                    delimiter: 'semi',
                    requireLast: false,
                },
            },
        ],
        'style/operator-linebreak': 'off',
        'style/quote-props': 'off',
        'style/semi': ['error', 'always'],

        'unicorn/error-message': 'off',

        curly: ['error', 'all'],

        // 1TBS + запрет однострочных блоков { ... }
        '@stylistic/brace-style': ['error', '1tbs', { allowSingleLine: false }],

        '@stylistic/object-curly-spacing': ['error', 'always'],

        '@stylistic/object-curly-newline': [
            'error',
            {
                ObjectExpression: {
                    minProperties: 5,
                    multiline: true,
                    consistent: true,
                },
                ObjectPattern: {
                    minProperties: 5,
                    multiline: true,
                    consistent: true,
                },
                ImportDeclaration: {
                    minProperties: 5,
                    multiline: true,
                    consistent: true,
                },
                ExportDeclaration: {
                    minProperties: 5,
                    multiline: true,
                    consistent: true,
                },
            },
        ],

        // Если атрибутов (props) ≤ 2 — можно в одну строку.
        // Если > 2 — переносим на новые строки, по одному на строку.
        'vue/max-attributes-per-line': 'off',
        'vue/html-indent': 'off',
        'vue/operator-linebreak': 'off',

        'antfu/if-newline': 'off',
    },
});
