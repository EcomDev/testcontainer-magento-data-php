<?php

namespace EcomDev\TestContainers\MagentoData;

use Testcontainers\Container\GenericContainer;
use Testcontainers\Wait\WaitForLog;

final class DbContainer implements RunningContainer
{
    private function __construct(private readonly GenericStartedContainer $container)
    {
    }

    public static function fromImage(string $imageName): self
    {
        return new self(GenericStartedContainer::create(
            (new GenericContainer($imageName))
                ->withWait((new WaitForLog('ready for connections'))->withPollInterval(50)),
            $imageName
        ));
    }

    public function getConnectionSettings(): DbConnectionSettings
    {
        return DbConnectionSettings::fromEnvironment($this->container->getEnvironmentVariables(), $this->getAddress());
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
