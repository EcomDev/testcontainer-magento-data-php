<?php

namespace EcomDev\TestContainers\MagentoData;

/**
 * A class responsible for managing a registry of running containers,
 * identified by their image name and a unique identifier.
 */
final class ContainerRegistry
{
    /**
     * @var array<string, array<string, RunningContainer>>
     */
    private static array $containers = [];

    /**
     * Register container with specified id
     */
    public static function registerContainer(string $containerId, RunningContainer $container): RunningContainer
    {
        $imageName = $container->getImageName();
        self::$containers[$imageName][$containerId] = $container;
        return $container;
    }

    /**
     * Returns container by image and id if it was registered before
     */
    public static function findContainer(string $image, string $containerId): ?RunningContainer
    {
        return self::$containers[$image][$containerId] ?? null;
    }
}
