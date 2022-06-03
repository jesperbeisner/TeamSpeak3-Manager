<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\RuntimeExtensionInterface;

class GitInfoRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {}

    public function getGitInfo(): string
    {
        $filePath = $this->parameterBag->get('kernel.project_dir') . '/git-info.txt';

        if (file_exists($filePath)) {
            return file_get_contents($filePath);
        }

        return "Das ist ein Test";
    }
}
