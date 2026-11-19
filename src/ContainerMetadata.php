<?php

namespace EcomDev\TestContainers\MagentoData;

final class ContainerMetadata
{
    private const CONTAINER_NAME = 'ghcr.io/ecomdev/testcontainer-%s-data/%s';

    private static string $customSource = '';

    public static function getImageNameWithDistribution(string $type, string $distribution): string
    {
        if (self::$customSource !== '') {
            return str_replace(['{{type}}', '{{distribution}}'], [$type, $distribution], self::$customSource);
        }

        return sprintf(self::CONTAINER_NAME, $distribution, $type);
    }

    /**
     * Allows to set custom source for container image name
     *
     * @param string $source
     * @return void
     */
    public static function setCustomSource(string $source): void
    {
        if (!str_contains($source, '{{type}}')) {
            throw new \InvalidArgumentException('Custom source must contain at least {{type}} placeholder');
        }

        self::$customSource = $source;
    }

    public static function reset(): void
    {
        self::$customSource = '';
    }
}
