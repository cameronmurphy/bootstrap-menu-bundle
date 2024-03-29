<?php

declare(strict_types=1);

$config = new PhpCsFixer\Config();

return $config
    ->setRules([
        '@PHP81Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@PHPUnit84Migration:risky' => true,
        'concat_space' => ['spacing' => 'one'],
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/DependencyInjection')
            ->in(__DIR__ . '/Twig')
            // Disable this file until PHP-CS-Fixer can be useful for the builder pattern or indentation formatting can be disabled
            // for a block
            ->notName('Configuration.php')
    )
;
