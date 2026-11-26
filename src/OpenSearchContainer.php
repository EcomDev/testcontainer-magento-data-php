<?php

namespace EcomDev\TestContainers\MagentoData;

use Testcontainers\Container\GenericContainer;
use Testcontainers\Wait\WaitForLog;

final class OpenSearchContainer implements RunningContainer
{
    private function __construct(private readonly GenericStartedContainer $container)
    {
    }

    public static function fromImage(string $imageName): self
    {
        return new self(GenericStartedContainer::create(
            (new GenericContainer($imageName))
                ->withWait(
                    (new WaitForLog('Cluster health status changed from [RED]', false, 60000))
                        ->withPollInterval(50)
                )
                ->withEnvironment([
                    'discovery.type' => 'single-node',
                    'DISABLE_SECURITY_PLUGIN' => 'true'
                ]),
            $imageName
        ));
    }

    public function getBaseUrl(): string
    {
        return sprintf('http://%s:9200/', $this->getAddress());
    }

    public function getAddress(): string
    {
        return $this->container->getResolvedAddress();
    }

    public function getId(): string
    {
        return $this->container->getId();
    }

    public function getImageName(): string
    {
        return $this->container->getImageName();
    }

    public function __destruct()
    {
        $this->container->stop();
    }
}
