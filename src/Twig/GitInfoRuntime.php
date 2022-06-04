<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\RuntimeExtensionInterface;

class GitInfoRuntime implements RuntimeExtensionInterface
{
    private const INIT_COMMIT = 'aebcdda2c9550386ace42d3a04df55aa0cf56850';

    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {}

    public function getGitInfo(): string
    {
        $filePath = $this->parameterBag->get('kernel.project_dir') . '/git-info.txt';

        if (file_exists($filePath)) {
            if (false === $currentCommit = file_get_contents($filePath)) {
                return self::INIT_COMMIT;
            }

            return trim($currentCommit);
        }

        return self::INIT_COMMIT;
    }
}
