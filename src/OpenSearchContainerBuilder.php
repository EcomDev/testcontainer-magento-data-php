<?php

namespace EcomDev\TestContainers\MagentoData;

use ReturnTypeWillChange;

final class OpenSearchContainerBuilder implements ContainerBuilder
{
    use ContainerBuilderConfiguration;

    public static function new(): self
    {
        return new self();
    }

    #[ReturnTypeWillChange]
    public function build(): OpenSearchContainer
    {
        return OpenSearchContainer::fromImage($this->getImageName());
    }

    #[ReturnTypeWillChange]
    public function shared(string $containerId): OpenSearchContainer
    {
        /** @var OpenSearchContainer $container */
        $container = ContainerRegistry::findContainer($this->getImageName(), $containerId)
            ?? ContainerRegistry::registerContainer($containerId, $this->build());
        return $container;
    }

    public function getImageName(): string
    {
        return sprintf(
            "%s:%s",
            ContainerMetadata::getImageNameWithDistribution('opensearch', $this->distribution),
            $this->generateImageTag()
        );
    }
}
