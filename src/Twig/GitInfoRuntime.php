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
            if (false === $gitInfo = file_get_contents($filePath)) {
                return '';
            }

            $gitInfo = trim($gitInfo);

            return '<a href="https://github.com/jesperbeisner/TeamSpeak3-Server-Manager/tree/' . $gitInfo . '">' . $gitInfo . '</a>';
        }

        return '';
    }
}
