<?php

declare(strict_types=1);

namespace App\Logger;

use DateTime;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordLogger extends AbstractLogger
{
    /* https://www.spycolor.com */
    public const COLOR_BLACK = 0;
    public const COLOR_WHITE = 16777215;
    public const COLOR_YELLOW = 16776960;
    public const COLOR_RED = 16711680;
    public const COLOR_BLUE = 255;
    public const COLOR_GREEN = 32768;
    public const COLOR_ORANGE = 16753920;
    public const COLOR_VIOLET = 15631086;
    public const COLOR_ICON_GREEN = 30794;

    private array $colors = [
        LogLevel::EMERGENCY => self::COLOR_BLACK,
        LogLevel::ALERT => self::COLOR_RED,
        LogLevel::CRITICAL => self::COLOR_RED,
        LogLevel::ERROR => self::COLOR_RED,
        LogLevel::NOTICE => self::COLOR_YELLOW,
        LogLevel::INFO => self::COLOR_ICON_GREEN,
        LogLevel::DEBUG => self::COLOR_BLUE,
    ];

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $appName,
        private readonly string $webhook,
    ) {
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->httpClient->request('POST', $this->webhook, [
            'json' => [
                'embeds' => [
                    [
                        'title' => $this->appName . ' - ' . (new DateTime())->format('Y-m-d - H:i:s'),
                        'description' => $message,
                        'color' => $this->colors[$level],
                        /*
                        'footer' => [
                            'text' => (new DateTime())->format('Y-m-d - H:i:s'),
                        ],
                        */
                    ],
                ],
            ],
        ]);
    }
}
