<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MagentoEse\SaasDataManagement\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\AbstractAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\Serializer\Json;
use MagentoEse\SaasDataManagement\Model\ServicesClientInterface;
use MagentoEse\SaasDataManagement\Model\ServicesConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\FlagManager;
use Magento\Framework\Indexer\IndexerRegistry;

/**
 * Controller responsible for communicating with the Magento SaaS Registry service
 */
class ClearEnvironment extends AbstractAction
{
    public const ADMIN_RESOURCE = 'Magento_ServicesId::configuration';

    private const FEED_TYPE = [
        ['type'=>'categories','flagName'=>'categories-feed-version','indexerName'=>'catalog_data_exporter_categories',
        'registryTableName'=>'catalog_category_data_submitted_hash'],
        ['type'=>'productoverrides','flagName'=>'product-overrides-feed-version',
        'indexerName'=>'catalog_data_exporter_product_overrides',
        'registryTableName'=>'catalog_product_override_data_submitted_hash'],
        ['type'=>'variants','flagName'=>'variants-feed-version',
        'indexerName'=>'catalog_data_exporter_product_variants',
        'registryTableName'=>'catalog_product_variants_submitted_hash'],
        ['type'=>'products','flagName'=>'products-feed-version','indexerName'=>'catalog_data_exporter_products',
        'registryTableName'=>'catalog_product_data_submitted_hash'],
        ['type'=>'productattributes','flagName'=>'product-attributes-feed-version',
        'indexerName'=>'catalog_data_exporter_product_attributes',
        'registryTableName'=>'catalog_product_attribute_data_submitted_hash']
    ];
    /**
     * @var ServicesConfigInterface
     */
    private $servicesConfig;

    /**
     * @var ServicesClientInterface
     */
    private $servicesClient;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var FlagManager
     */
    private $flagManager;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @param Context $context
     * @param ServicesConfigInterface $servicesConfig
     * @param ServicesClientInterface $servicesClient
     * @param Json $serializer
     * @param JsonFactory $resultJsonFactory
     * @param LoggerInterface $logger
     * @param FlagManager $flagManager
     * @param IndexerRegistry $indexerRegistry
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        ServicesConfigInterface $servicesConfig,
        ServicesClientInterface $servicesClient,
        Json $serializer,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger,
        FlagManager $flagManager,
        IndexerRegistry $indexerRegistry,
        ResourceConnection $resourceConnection
    ) {
        $this->servicesConfig = $servicesConfig;
        $this->servicesClient = $servicesClient;
        $this->serializer = $serializer;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->flagManager = $flagManager;
        $this->indexerRegistry = $indexerRegistry;
        parent::__construct($context);
    }
      
    /**
     * Execute middleware call
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        $jsonResult = $this->resultJsonFactory->create();

        $apiVersion = $this->servicesConfig->getRegistryApiVersion();
        $method = $this->getRequest()->getParam('method', 'GET');
        $uri = $this->getRequest()->getParam('uri', '');
        //$uri = 'registry/MAG005476355/environments/ae113b59-b765-4d23-b4dd-fbd2424684d4/cleanup';
        $environmentName = $this->getRequest()->getParam('environmentName');
        $payload = ['environmentName' => $environmentName];
        $url = $this->servicesClient->getUrl($apiVersion, $uri);
        $result = $this->servicesClient->request($method, $url, $this->serializer->serialize($payload));
        
        if (isset($result['environmentId'])) {
            foreach (self::FEED_TYPE as $feedType) {
                try {
                    $this->resetIndexedData($feedType['indexerName']);
                    $this->resetSubmittedData($feedType['flagName'], $feedType['registryTableName']);
                } catch (Exception $e) {
                    //ignore errors. some tables may not exist based on modules included
                    $r = null;
                }
            }
        } else {
            $result = 'An error occurred clearing the data space. See logs for details';
                //phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                $this->logger->error(print_r($result));
        }
        return $jsonResult->setData($result);
    }

    /**
     * Reset SaaS indexed feed data in order to re-generate
     *
     * @param string $indexerName
     *
     * @throws \Zend_Db_Statement_Exception
     */
    public function resetIndexedData($indexerName): void
    {
        $indexer = $this->indexerRegistry->get($indexerName);
        $indexer->invalidate();
    }

    /**
     * Reset SaaS submitted feed data in order to re-send
     *
     * @param string $flagName
     * @param string $registryTableName
     * @throws \Zend_Db_Statement_Exception
     */
    public function resetSubmittedData($flagName, $registryTableName): void
    {
        $connection = $this->resourceConnection->getConnection();
        $registryTable = $this->resourceConnection->getTableName($registryTableName);
        $connection->truncateTable($registryTable);
        $this->flagManager->deleteFlag($flagName);
    }
}
