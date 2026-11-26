<?php

namespace EcomDev\TestContainers\MagentoData;

use Docker\API\Exception\ConflictException;
use Docker\API\Exception\NotFoundException;
use Override;
use Testcontainers\Container\GenericContainer;
use Testcontainers\Container\StartedGenericContainer;
use Testcontainers\Container\StoppedGenericContainer;
use Testcontainers\Container\StoppedTestContainer;

/**
 * Container extension for removing volume associated with test container
 *
 * Original implementation does not remove ephemeral volumes of the container
 *
 * @private
 */
final class GenericStartedContainer extends StartedGenericContainer
{
    /**
     * @var array<string, string>
     */
    private readonly array $environmentVariables;
    private readonly string $ipAddress;

    private function __construct(
        string $containerId,
        private readonly string $originalImageName
    ) {
        parent::__construct($containerId);

        $envVariables = [];
        foreach ($this->inspect()?->getConfig()?->getEnv() ?? [] as $env) {
            [$name, $value] = explode('=', $env, 2);
            $envVariables[$name] = $value;
        }
        $this->environmentVariables = $envVariables;

        $ipAddress = '';
        foreach ($this->inspect()?->getNetworkSettings()?->getNetworks() ?? [] as $network) {
            $ipAddress = $network->getIpAddress() ?? '';
            if ($ipAddress !== '') {
                break;
            }
        }

        $this->ipAddress = $ipAddress;
    }

    public static function create(
        GenericContainer $container,
        string $image
    ): self {
        $container = $container
            ->withAutoRemove(true);

        $started = $container->start();
        return new self($started->getId(), $image);
    }

    /**
     * Environment variables
     *
     * @return array<string, string>
     */
    public function getEnvironmentVariables(): array
    {
        return $this->environmentVariables;
    }

    /**
     * Returns container IP address
     *
     * @return string
     */
    public function getResolvedAddress(): string
    {
        return $this->ipAddress;
    }

    public function getImageName(): string
    {
        return $this->originalImageName;
    }

    #[Override]
    public function stop(): StoppedTestContainer
    {
        try {
            $this->dockerClient->containerDelete($this->id, ['force' => true, 'v' => true]);
        } catch (NotFoundException | ConflictException) {
            // The container is already gone or being auto-removed by Docker
        }

        return new StoppedGenericContainer($this->id);
    }
}
