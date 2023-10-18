<?php

namespace MagentoEse\SaasDataManagement\Model;

use MagentoEse\SaasDataManagement\Api\Data\DataSpaceInterface;
use MagentoEse\SaasDataManagement\Model\ServicesClientInterface;
use MagentoEse\SaasDataManagement\Model\ServicesConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\FlagManager;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Serialize\Serializer\Json;

class DataSpace implements DataSpaceInterface
{
    /** list of DB data that should be cleared when resetting catalog or configuration */
    /** @var array */
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
    
    /** @var string */
    protected $mageId;

    /** @var string */
    protected $environmentId;

    /** @var string */
    protected $environmentName;

    /** @var string */
    protected $environmentType;

    /** @var string */
    protected $orginiationId;

    /** @var string */
    protected $projectId;

    /** @var string */
    protected $projectName;

    /** @var string */
    protected $sandboxPublicKey;

    /** @var string */
    protected $sandboxPrivateKey;

    /** @var string */
    protected $productionPublicKey;

    /** @var string */
    protected $productionPrivateKey;

    /** @var string */
    protected $accountGroupId;

    /** @var array */
    protected $featureSet;
    
    /** @var string */
    protected $imsOrgId;

    /** @var ServicesClientInterface */
    protected $servicesClient;

    /** @var ServicesConfigInterface */
    protected $servicesConfig;

    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var LoggerInterface */
    protected $logger;

    /** @var ResourceConnection */
    protected $resourceConnection;

    /** @var FlagManager */
    protected $flagManager;

    /** @var IndexerRegistry */
    protected $indexerRegistry;

    /** @var Json */
    protected $serializer;
    /**
     *
     * @param ServicesClientInterface $servicesClient
     * @param ServicesConfigInterface $servicesConfig
     * @param JsonFactory $resultJsonFactory
     * @param LoggerInterface $logger
     * @param ResourceConnection $resourceConnection
     * @param FlagManager $flagManager
     * @param IndexerRegistry $indexerRegistry
     * @param Json $serializer
     * @return void
     */
    public function __construct(
        ServicesClientInterface $servicesClient,
        ServicesConfigInterface $servicesConfig,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger,
        ResourceConnection $resourceConnection,
        FlagManager $flagManager,
        IndexerRegistry $indexerRegistry,
        Json $serializer
    ) {
        $this->servicesClient = $servicesClient;
        $this->servicesConfig = $servicesConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->flagManager = $flagManager;
        $this->indexerRegistry = $indexerRegistry;
        $this->serializer = $serializer;
    }
        
    /**
     * Get mage Id
     *
     * @return string
     */
    public function getMageId() :string
    {
        return $this->mageId;
    }

    /**
     * Set mage Id
     *
     * @param string $mageId
     * @return void
     */
    public function setMageId($mageId) :void
    {
        $this->mageId = $mageId;
    }

    /**
     * Set environment Id
     *
     * @param string $environmentId
     * @return void
     */
    public function setEnvironmentId($environmentId) :void
    {
        $this->environmentId = $environmentId;
    }

    /**
     * Get environment Id
     *
     * @return string
     */
    public function getEnvironmentId() :string
    {
        return $this->environmentId;
    }

    /**
     * Set environment name
     *
     * @param string $environmentName
     * @return void
     */
    public function setEnvironmentName($environmentName) :void
    {
        $this->environmentName = $environmentName;
    }

    /**
     * Get environment name
     *
     * @return string
     */
    public function getEnvironmentName() :string
    {
        return $this->environmentName;
    }

    /**
     * Set environment type
     *
     * @param string $environmentType
     * @return void
     */
    public function setEnvironmentType($environmentType) :void
    {
        $this->environmentType = $environmentType;
    }

    /**
     * Get environment type
     *
     * @return string
     */
    public function getEnvironmentType() :string
    {
        return $this->environmentType;
    }

    /**
     * Set organization Id
     *
     * @param string $orginiationId
     * @return void
     */
    public function setOrganizationId($orginiationId) :void
    {
        $this->orginiationId = $orginiationId;
    }

    /**
     * Get organization Id
     *
     * @return string
     */
    public function getOrganizationId() :string
    {
        return $this->orginiationId;
    }

    /**
     * Set project Id
     *
     * @param string $projectId
     * @return void
     */
    public function setProjectId($projectId) :void
    {
        $this->projectId = $projectId;
    }

    /**
     * Get project Id
     *
     * @return string
     */
    public function getProjectId() :string
    {
        return $this->projectId;
    }

    /**
     * Set project name
     *
     * @param string $projectName
     * @return void
     */
    public function setProjectName($projectName) :void
    {
        $this->projectName = $projectName;
    }

    /**
     * Get project name
     *
     * @return string
     */
    public function getProjectName() :string
    {
        return $this->projectName;
    }

    /**
     * Set sandbox Public key
     *
     * @return string
     */
    public function getSandboxPublicKey(): string
    {
        return $this->sandboxPublicKey;
    }

    /**
     * Set sandbox Public key
     *
     * @param string $sandboxPublicKey
     * @return void
     */
    public function setSandboxPublicKey($sandboxPublicKey): void
    {
        $this->sandboxPublicKey = $sandboxPublicKey;
    }

    /**
     * Get sandbox private key
     *
     * @return string
     */
    public function getSandboxPrivateKey(): string
    {
        return $this->sandboxPrivateKey;
    }

    /**
     * Set sandbox private key
     *
     * @param string $sandboxPrivateKey
     * @return void
     */
    public function setSandboxPrivateKey($sandboxPrivateKey): void
    {
        $this->sandboxPrivateKey = $sandboxPrivateKey;
    }

    /**
     * Set production Public key
     *
     * @param string $productionPublicKey
     * @return void
     */
    public function setProductionPublicKey($productionPublicKey): void
    {
        $this->productionPublicKey = $productionPublicKey;
    }

    /**
     * Get production Public key
     *
     * @return string
     */
    public function getProductionPublicKey(): string
    {
        return $this->productionPublicKey;
    }

    /**
     * Set production private key
     *
     * @param string $productionPrivateKey
     * @return void
     */
    public function setProductionPrivateKey($productionPrivateKey): void
    {
        $this->productionPrivateKey = $productionPrivateKey;
    }

    /**
     * Get production private key
     *
     * @return string
     */
    public function getProductionPrivateKey(): string
    {
        return $this->productionPrivateKey;
    }
    /**
     * Get account group Id
     *
     * @return string
     */
    public function getAccountGroupId() :string
    {
        return $this->accountGroupId;
    }

    /**
     * Set account group Id
     *
     * @param string $accountGroupId
     * @return void
     */
    public function setAccountGroupId($accountGroupId) :void
    {
        $this->accountGroupId = $accountGroupId;
    }

    /**
     * Set feature set
     *
     * @param array $featureSet
     * @return void
     */
    public function setFeatureSet($featureSet) :void
    {
        $this->featureSet = $featureSet;
    }

    /**
     * Get feature set
     *
     * @return array
     */
    public function getFeatureSet() :array
    {
        return $this->featureSet;
    }

    /**
     * Get IMS Org Id
     *
     * @return mixed
     */
    public function getImsOrgId(): mixed
    {
        return $this->imsOrgId;
    }

    /**
     * Set IMS Org Id
     *
     * @param string $imsOrgId
     * @return void
     */
    public function setImsOrgId($imsOrgId): void
    {
        $this->imsOrgId = $imsOrgId;
    }

    /**
     * Clear catalog data
     *
     * @param DataSpaceInterface $dataSpace
     * @return string
     */
    public function clearCatalog($dataSpace) :string
    {
       // $jsonResult = $this->resultJsonFactory->create();

        $PublicVersion = $this->servicesConfig->getRegistryApiVersion();
        $method = 'POST';
        $uri = 'registry/environments/'.$dataSpace->getEnvironmentId().'/cleanup';
        //$uri = 'registry/MAG005476355/environments/ae113b59-b765-4d23-b4dd-fbd2424684d4/cleanup';
        $environmentName = $dataSpace->getEnvironmentName();
        $payload = ['environmentName' => $environmentName];
        $url = $this->servicesClient->getUrl($PublicVersion, $uri);
        $result = $this->servicesClient->request($method, $url, $this->serializer->serialize($payload));
        
        if (isset($result['environmentId'])) {
            foreach (self::FEED_TYPE as $feedType) {
                try {
                    $this->resetIndexedData($feedType['indexerName']);
                    $this->resetSubmittedData($feedType['flagName'], $feedType['registryTableName']);
                } catch (\Exception $e) {
                    //ignore errors. some tables may not exist based on modules included
                    $r = null;
                }
            }
        } else {
            $result = 'An error occurred clearing the data space. See logs for details';
                //phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                $this->logger->error(print_r($result));
        }
        return 'Data Space Cleared';
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
