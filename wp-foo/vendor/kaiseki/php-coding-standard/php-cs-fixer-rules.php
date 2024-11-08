<?php

declare(strict_types=1);

// https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/rules/index.rst

return [
    '@PSR12' => true,

    /*
     * Array Notation
     */
    'array_syntax' => ['syntax' => 'short'],
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_trailing_comma_in_singleline' => true,
    'trim_array_spaces' => true,
    'whitespace_after_comma_in_array' => [
        'ensure_single_space' => true,
    ],

    /*
     * Cast Notation
     */
    'cast_spaces' => [
        'space' => 'none',
    ],

    /*
     * Class Notation
     */
    'class_attributes_separation' => [
        'elements' => [
            'method' => 'one',
        ],
    ],
    'single_trait_insert_per_statement' => true,

    /*
     * Comment
     */
    'no_empty_comment' => true,

    /*
     * Constant Notation
     */
    'native_constant_invocation' => [
        'scope' => 'namespaced',
        'strict' => true,
    ],

    /*
     * Control Structure
     */
    'no_superfluous_elseif' => true,
    'no_useless_else' => true,
    'trailing_comma_in_multiline' => [
        'elements' => [
            'arrays',
        ],
    ],

    /*
     * Function Notation
     */
    'function_declaration' => [
        'closure_fn_spacing' => 'none',
    ],
    'method_argument_space' => [
        'on_multiline' => 'ensure_fully_multiline',
        'keep_multiple_spaces_after_comma' => true,
    ],
    'native_function_invocation' => [
        'include' => ['@all'],
        'scope' => 'namespaced',
        'strict' => true,
    ],
    'nullable_type_declaration_for_default_null_value' => true,

    /*
     * Import
     */
    'fully_qualified_strict_types' => true,
    'global_namespace_import' => [
        'import_classes' => true,
        'import_constants' => true,
        'import_functions' => true,
    ],
    'no_unused_imports' => true,
    'ordered_imports' => [
        'imports_order' => ['class', 'function', 'const'],
        'sort_algorithm' => 'alpha',
    ],

    /*
     * Language Construct
     */
    'single_space_around_construct' => true,

    /*
     * Operator
     */
    'binary_operator_spaces' => true,
    'new_with_parentheses' => [
        'named_class' => true,
        'anonymous_class' => false,
    ],
    'unary_operator_spaces' => true,

    /*
     * PHPDoc
     */
    'no_blank_lines_after_phpdoc' => true,
    'no_empty_phpdoc' => true,
    'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
    'phpdoc_align' => true,
    'phpdoc_annotation_without_dot' => true,
    'phpdoc_indent' => true,
    'phpdoc_line_span' => ['property' => 'single', 'const' => 'single', 'method' => 'multi'],
    'phpdoc_no_empty_return' => true,
    'phpdoc_order' => true,
    'phpdoc_param_order' => true,
    'phpdoc_scalar' => true,
    'phpdoc_separation' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_tag_casing' => true,
    'phpdoc_to_comment' => false,
    'phpdoc_trim' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_types' => true,
    'phpdoc_types_order' => ['sort_algorithm' => 'alpha', 'null_adjustment' => 'always_last'],
    'phpdoc_var_annotation_correct_order' => true,
    'phpdoc_var_without_name' => true,

    /*
     * Strict
     */
    'declare_strict_types' => true,
    'strict_comparison' => true,

    /*
     * Whitespace
     */
    'array_indentation' => true,
    'blank_line_before_statement' => [
        'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
    ],
    'no_extra_blank_lines' => [
        'tokens' => [
            'continue',
            'extra',
            'parenthesis_brace_block',
            'return',
            'square_brace_block',
            'throw',
            'use',
        ],
    ],
    'type_declaration_spaces' => true,
    'types_spaces' => [
        'space' => 'none',
    ],
];
