<?php

namespace MagentoEse\SaasDataManagement\Model;

use MagentoEse\SaasDataManagement\Api\DataSpaceRepositoryInterface;
use MagentoEse\SaasDataManagement\Api\Data\DataSpaceInterfaceFactory;
use MagentoEse\SaasDataManagement\Api\Data\DataSpaceInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Type\Config as CacheConfig;
use Magento\Framework\Encryption\EncryptorInterface;

class DataSpaceRepository implements DataSpaceRepositoryInterface
{
    /** Config path values for Services */
    protected const CONFIG_PATH_PROJECT_ID =
        'services_connector/services_id/project_id';
    protected const CONFIG_PATH_PROJECT_NAME =
        'services_connector/services_id/project_name';
    protected const CONFIG_PATH_ENVIRONMENT_ID =
        'services_connector/services_id/environment_id';
    protected const CONFIG_PATH_ENVIRONMENT_NAME =
        'services_connector/services_id/environment_name';
    protected const CONFIG_PATH_SANDBOX_API_KEY =
        'services_connector/services_connector_integration/sandbox_api_key';
    protected const CONFIG_PATH_PRODUCTION_API_KEY =
        'services_connector/services_connector_integration/production_api_key';
    protected const CONFIG_PATH_SANDBOX_PRIVATE_KEY =
        'services_connector/services_connector_integration/sandbox_private_key';
    protected const CONFIG_PATH_PRODUCTION_PRIVATE_KEY =
        'services_connector/services_connector_integration/production_private_key';
    protected const CONFIG_PATH_IMS_ORG_ID =
        'services_connector/services_id/ims_organization_id';
    
    /** @var ScopeConfigInterface */
    protected $config;

    /** @var DataSpaceInterfaceFactory */
    protected $dataSpaceInterface;

    /** @var WriterInterface */
    protected $configWriter;

    /** @var TypeListInterface */
    protected $cacheTypeList;

    /** @var EncryptorInterface */
    protected $keyEncryptor;
    
    /**
     *
     * @param ScopeConfigInterface $config
     * @param DataSpaceInterfaceFactory $dataSpaceInterface
     * @param WriterInterface $configWriter
     * @param TypeListInterface $cacheTypeList
     * @param EncryptorInterface $keyEncryptor
     * @return void
     */
    public function __construct(
        ScopeConfigInterface $config,
        DataSpaceInterfaceFactory $dataSpaceInterface,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        EncryptorInterface $keyEncryptor
    ) {
        $this->config = $config;
        $this->dataSpaceInterface = $dataSpaceInterface;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->keyEncryptor = $keyEncryptor;
    }

    /**
     * Save DataSpace Configuration
     *
     * @param DataSpaceInterface $dataSpace
     * @return mixed
     */
    public function save(DataSpaceInterface $dataSpace)
    {
        $this->configWriter->save(
            self::CONFIG_PATH_PROJECT_ID,
            $dataSpace->getProjectId()
        );
        $this->configWriter->save(
            self::CONFIG_PATH_PROJECT_NAME,
            $dataSpace->getProjectName()
        );
        $this->configWriter->save(
            self::CONFIG_PATH_ENVIRONMENT_ID,
            $dataSpace->getEnvironmentId()
        );
        $this->configWriter->save(
            self::CONFIG_PATH_ENVIRONMENT_NAME,
            $dataSpace->getEnvironmentName()
        );
        $this->configWriter->save(
            self::CONFIG_PATH_SANDBOX_API_KEY,
            $this->keyEncryptor->encrypt($dataSpace->getSandboxPublicKey())
        );
        $this->configWriter->save(
            self::CONFIG_PATH_PRODUCTION_API_KEY,
            $this->keyEncryptor->encrypt($dataSpace->getProductionPublicKey())
        );
        $this->configWriter->save(
            self::CONFIG_PATH_SANDBOX_PRIVATE_KEY,
            $this->keyEncryptor->encrypt($dataSpace->getSandboxPrivateKey())
        );
        $this->configWriter->save(
            self::CONFIG_PATH_PRODUCTION_PRIVATE_KEY,
            $this->keyEncryptor->encrypt($dataSpace->getProductionPrivateKey())
        );
        $this->configWriter->save(
            self::CONFIG_PATH_IMS_ORG_ID,
            $dataSpace->getImsOrgId()
        );
        $this->cacheTypeList->cleanType(CacheConfig::TYPE_IDENTIFIER);
    }
    
    /**
     * Get DataSpace Configuration
     *
     * @return DataSpaceInterface
     */
    public function get()
    {
        $dataSpace = $this->dataSpaceInterface->create();
        $dataSpace->setEnvironmentId($this->config->getValue(self::CONFIG_PATH_ENVIRONMENT_ID));
        $dataSpace->setEnvironmentName($this->config->getValue(self::CONFIG_PATH_ENVIRONMENT_NAME));
        $dataSpace->setProjectId($this->config->getValue(self::CONFIG_PATH_PROJECT_ID));
        $dataSpace->setProjectName($this->config->getValue(self::CONFIG_PATH_PROJECT_NAME));
        $dataSpace->setAccountGroupId($this->config->getValue(self::CONFIG_PATH_PROJECT_ID));
        $dataSpace->setSandboxPublicKey($this->config->getValue(self::CONFIG_PATH_SANDBOX_API_KEY));
        $dataSpace->setProductionPublicKey($this->config->getValue(self::CONFIG_PATH_PRODUCTION_API_KEY));
        $dataSpace->setSandboxPrivateKey($this->config->getValue(self::CONFIG_PATH_SANDBOX_PRIVATE_KEY));
        $dataSpace->setProductionPrivateKey($this->config->getValue(self::CONFIG_PATH_PRODUCTION_PRIVATE_KEY));
        $dataSpace->setImsOrgId($this->config->getValue(self::CONFIG_PATH_IMS_ORG_ID));
        return $dataSpace;
    }
}
