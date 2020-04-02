<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Search\Setup\InstallConfigInterface;
use Magento\Setup\Model\SearchConfig;
use Magento\Setup\Model\SearchConfigOptionsList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SearchConfigTest extends TestCase
{
    /**
     * @var SearchConfig
     */
    private $searchConfig;

    /**
     * @var SearchConfigOptionsList
     */
    private $searchConfigOptionsList;

    /**
     * @var InstallConfigInterface|MockObject
     */
    private $installConfigMock;

    protected function setUp()
    {
        $this->installConfigMock = $this->getMockBuilder(InstallConfigInterface::class)
            ->getMockForAbstractClass();

        $objectManager = new ObjectManager($this);
        $this->searchConfigOptionsList = $objectManager->getObject(SearchConfigOptionsList::class);
        $this->searchConfig = $objectManager->getObject(
            SearchConfig::class,
            [
                'searchConfigOptionsList' => $this->searchConfigOptionsList,
                'installConfig' => $this->installConfigMock
            ]
        );
    }

    /**
     * @param array $installInput
     * @param array $searchInput
     * @dataProvider installInputDataProvider
     */
    public function testSaveConfiguration(array $installInput, array $searchInput)
    {
        $this->installConfigMock->expects($this->once())->method('configure')->with($searchInput);

        $this->searchConfig->saveConfiguration($installInput);
    }

    /**
     * @param array $installInput
     * @param array $searchInput
     * @dataProvider installInputDataProvider
     * @expectedException \Magento\Setup\Exception
     * @expectedExceptionMessage Search engine 'other-engine' is not an available search engine.
     */
    public function testSaveConfigurationInvalidSearchEngine(array $installInput, array $searchInput)
    {
        $installInput['search-engine'] = 'other-engine';
        $searchInput['search-engine'] = 'other-engine';
        $this->installConfigMock->expects($this->never())->method('configure');

        $this->searchConfig->saveConfiguration($installInput);
    }

    public function installInputDataProvider()
    {
        return [
            [
                'all' => [
                    'amqp-host' => '',
                    'amqp-port' => '5672',
                    'amqp-user' => '',
                    'amqp-password' => '',
                    'amqp-virtualhost' => '/',
                    'amqp-ssl' => '',
                    'amqp-ssl-options' => '',
                    'db-host' => 'localhost',
                    'db-name' => 'magento',
                    'db-user' => 'root',
                    'db-engine' => null,
                    'db-password' => 'root',
                    'db-prefix' => null,
                    'db-model' => null,
                    'db-init-statements' => null,
                    'skip-db-validation' => false,
                    'http-cache-hosts' => null,
                    'base-url' => 'http://magento.dev',
                    'language' => 'en_US',
                    'timezone' => 'America/Chicago',
                    'currency' => 'USD',
                    'use-rewrites' => '1',
                    'use-secure' => null,
                    'base-url-secure' => null,
                    'use-secure-admin' => null,
                    'admin-use-security-key' => null,
                    'search-engine' => 'elasticsearch7',
                    'elasticsearch-host' => 'localhost',
                    'elasticsearch-port' => '9200',
                    'elasticsearch-enable-auth' => false,
                    'elasticsearch-index-prefix' => 'magento2',
                    'elasticsearch-timeout' => 15,
                    'skip-elasticsearch-validation' => false,
                    'no-interaction' => false,
                ],
                'search' => [
                    'search-engine' => 'elasticsearch7',
                    'elasticsearch-host' => 'localhost',
                    'elasticsearch-port' => '9200',
                    'elasticsearch-enable-auth' => false,
                    'elasticsearch-index-prefix' => 'magento2',
                    'elasticsearch-timeout' => 15,
                    'skip-elasticsearch-validation' => false,
                ]
            ]
        ];
    }
}
