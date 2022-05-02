module.exports = {
    'rules': {
        'eol-last': 'error',
        'linebreak-style': ['error', 'unix'],
        'object-curly-spacing': ['error', 'always'],
        'key-spacing': ['error', { 'beforeColon': false, 'afterColon': true }],
        'object-curly-newline': ['error', { 'consistent': true }],
        'no-multiple-empty-lines': ['error', { 'max': 1 }],
        'func-call-spacing': ['error', 'never'],
        'keyword-spacing': ['error', { 'before': true, 'after': true }],
        'comma-spacing': ['error', { 'before': false, 'after': true }],
        'indent': ['error', 4, {
            'SwitchCase': 1,
            "VariableDeclarator": 1,
            "outerIIFEBody": 1,
            "MemberExpression": 1,
            "FunctionDeclaration": { "parameters": 1, "body": 1 },
            "FunctionExpression": { "parameters": 1, "body": 1 },
            "CallExpression": { "arguments": 1 },
            "ArrayExpression": 1,
            "ObjectExpression": 1,
            "ImportDeclaration": 1,
            "flatTernaryExpressions": false,
            "ignoreComments": false,
        }],
        'operator-linebreak': ['error', 'after', { 'overrides': { '?': 'before', ':': 'before' } }],
        'space-infix-ops': 'error',
        'space-unary-ops': ['error', { 'words': true, 'nonwords': false }],
    }
};