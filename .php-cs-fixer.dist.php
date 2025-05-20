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
        'control_structure_braces' => true,
        'trailing_comma_in_multiline' => true,
        'single_quote' => false,
        'space_after_semicolon' => true,
        'blank_lines_before_namespace' => [
            'min_line_breaks' => 1,
        ],
        'no_unused_imports' => true,
        'no_useless_else' => true,
    ])
    ->setFinder($finder);
