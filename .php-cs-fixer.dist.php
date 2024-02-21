<?php

$config = new PhpCsFixer\Config();

$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS'                             => true,
        '@PSR2'                               => true,
        '@PSR12'                              => true,
        '@PHP82Migration'                     => true,
        'array_syntax'                        => ['syntax' => 'short'],
        'php_unit_internal_class'             => ['types' => ['normal', 'final']],
        'php_unit_namespaced'                 => true,
        'php_unit_expectation'                => true,
        'php_unit_strict'                     => ['assertions' => ['assertAttributeEquals', 'assertAttributeNotEquals', 'assertEquals', 'assertNotEquals']],
        'align_multiline_comment'             => true,
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => true],
        'binary_operator_spaces'              => [
            'operators' => [
                '=>' => 'align',
                '='  => 'align',
            ],
        ],
        'ordered_imports'         => false,
        //'global_namespace_import' => ['import_classes' => true, 'import_constants' => true, 'import_functions' => true],
    ])
    ->setLineEnding("\n")
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
            ->append([__DIR__ . '/rector.php', __DIR__ . '/.php-cs-fixer.dist.php'])
    )
;

return $config;
