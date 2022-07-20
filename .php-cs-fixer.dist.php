<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())->setRules([
    '@PSR12' => true,
    'strict_param' => true,
    'declare_strict_types' => true,
    'concat_space' => ['spacing' => 'one'],
])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
;
