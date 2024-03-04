<?php

$config = new PhpCsFixer\Config();

$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS'                     => true,
        '@PSR2'                       => true,
        '@PSR12'                      => true,
        '@PHP82Migration'             => true,
        'array_syntax'                => ['syntax' => 'short'],
        'binary_operator_spaces'      => [
            'operators' => [
                '=>' => 'align',
                '='  => 'align',
            ],
        ],
        'ordered_imports'             => false,
        'blank_line_before_statement' => [
            'statements' => [
                'continue',
                'declare',
                'exit',
                'include',
                'include_once',
                'phpdoc',
                'require',
                'require_once',
                'return',
                'throw',
                'try',
                'yield',
                'yield_from',
            ],
        ],
        //'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => false],
    ])
    ->setLineEnding("\n")
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
            ->append([__DIR__ . '/.php-cs-fixer.dist.php'])
    )
;

return $config;
