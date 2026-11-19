<?php

namespace EcomDev\TestContainers\MagentoData;

use ReturnTypeWillChange;

/**
 * A builder class for creating and managing database container instances,
 * with configurations for different database types, Magento versions, and variations.
 */
final class DbContainerBuilder implements ContainerBuilder
{
    use ContainerBuilderConfiguration;

    /**
     * Constructor method for initializing the class with specified parameters
     */
    private function __construct(
        private readonly string $type
    ) {
    }

    public static function mysql(): self
    {
        return self::for('mysql');
    }

    public static function mariadb(): self
    {
        return self::for('mariadb');
    }

    public static function for(string $type): self
    {
        return new self($type);
    }

    #[ReturnTypeWillChange]
    public function shared(string $containerId): DbContainer
    {
        /** @var DbContainer $container */
        $container = ContainerRegistry::findContainer($this->getImageName(), $containerId)
            ?? ContainerRegistry::registerContainer($containerId, $this->build());
        return $container;
    }

    #[ReturnTypeWillChange]
    public function build(): DbContainer
    {
        return DbContainer::fromImage($this->getImageName());
    }

    public function getImageName(): string
    {
        return sprintf(
            '%s:%s',
            ContainerMetadata::getImageNameWithDistribution($this->type, $this->distribution),
            $this->generateImageTag()
        );
    }
}
