<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;

abstract class AbstractFixtures extends Fixture
{
    private const NUMBER_CHARACTERS = '1234567890';
    private const LOWERCASE_CHARACTERS = 'abcdefghijklmnopqrstuvwxyz';
    private const UPPERCASE_CHARACTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    protected function createRandomString(int $length): string
    {
        $randomString = '';

        $characters = self::NUMBER_CHARACTERS . self::LOWERCASE_CHARACTERS . self::UPPERCASE_CHARACTERS;

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}
