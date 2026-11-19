<?php

/**
 * Copyright © EcomDev B.V. All rights reserved.
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace EcomDev\TestContainers\MagentoData;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ContainerMetadataTest extends TestCase
{
    protected function tearDown(): void
    {
        ContainerMetadata::reset();
    }

    #[Test]
    public function defaultsToTestMagentoDataContainers()
    {
        $this->assertEquals(
            'ghcr.io/ecomdev/testcontainer-magento-data/opensearch',
            ContainerMetadata::getImageNameWithDistribution(
                'opensearch',
                'magento',
            ),
        );
    }

    #[Test]
    public function allowsOverridingCustomMetadata()
    {
        ContainerMetadata::setCustomSource('ecomdev/{{distribution}}/{{type}}');
        $this->assertEquals(
            'ecomdev/magento/opensearch',
            ContainerMetadata::getImageNameWithDistribution(
                'opensearch',
                'magento',
            )
        );
    }
}
