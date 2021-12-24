<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->exclude('vendor')
    ->in(__DIR__);

$config = new Config();
return $config
    ->setRules(
        [
            '@PSR12' => true,
            '@PHP81Migration' => true,
            'declare_strict_types' => true, // risky
            'align_multiline_comment' => ['comment_type' => 'all_multiline'],
            'fully_qualified_strict_types' => true,
            'no_unused_imports' => true,
            'clean_namespace' => true,
            'function_declaration' => ['closure_function_spacing' => 'none'],
            'lambda_not_used_import' => true,
            'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => false],
            'trailing_comma_in_multiline' => false,
            'no_trailing_comma_in_singleline_array' => true,
            'no_trailing_comma_in_list_call' => true,
            'types_spaces' => true
        ]
    )
    ->setFinder($finder);
