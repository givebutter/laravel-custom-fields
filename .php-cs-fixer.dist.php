<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('storage')
    ->exclude('bootstrap')
    ->exclude('tests/Feature/__snapshots__')
    ->in(__DIR__);

return (new PhpCsFixer\Config)
    ->setRules([
        '@PSR2' => true,
        'method_argument_space' => ['on_multiline' => 'ignore'],
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],
        'braces' => [
            'allow_single_line_closure' => true,
        ],
        'trailing_comma_in_multiline' => true,
        'single_quote' => false,
        'space_after_semicolon' => true,
        'single_blank_line_before_namespace' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
    ])
    ->setFinder($finder);
