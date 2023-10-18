<?php

namespace MagentoEse\SaasDataManagement\Api\Data;

interface DataSpaceInterface
{
    /**
     * Return the Magento ID of the DataSpace
     *
     * @return string
     */
    public function getMageId() :string;

    /**
     * Set the Magento ID of the DataSpace
     *
     * @param string $mageId
     * @return void
     */
    public function setMageId($mageId) :void;

    /**
     * Set the Environment ID
     *
     * @param string $environmentId
     * @return void
     */
    public function setEnvironmentId($environmentId) :void;

    /**
     * Return the Environment ID
     *
     * @return string
     */
    public function getEnvironmentId() :string;

    /**
     * Set the Environment Name
     *
     * @param string $environmentName
     * @return void
     */
    public function setEnvironmentName($environmentName) :void;

    /**
     * Return the Environment Name
     *
     * @return string
     */
    public function getEnvironmentName() :string;

    /**
     * Set the Environment Type
     *
     * @param string $environmentType
     * @return void
     */
    public function setEnvironmentType($environmentType) :void;

    /**
     * Return the Environment Type
     *
     * @return string
     */
    public function getEnvironmentType() :string;

    /**
     * Set the Organization ID
     *
     * @param string $organizationId
     * @return void
     */
    public function setOrganizationId($organizationId) :void;

    /**
     * Return the Organization ID
     *
     * @return string
     */
    public function getOrganizationId() :string;

    /**
     * Set the Project Id
     *
     * @param mixed $projectId
     * @return mixed
     */
    public function setProjectId($projectId) :void;

    /**
     * Return the Project ID
     *
     * @return string
     */
    public function getProjectId() :string;

    /**
     * Set the Project Name
     *
     * @param string $projectName
     * @return void
     */
    public function setProjectName($projectName) :void;

    /**
     * Return the Project Name
     *
     * @return string
     */
    public function getProjectName() :string;

    /**
     * Set the Sandbox Public Key
     *
     * @return string
     */
    public function getSandboxPublicKey() :string;

    /**
     * Set the Sandbox Public Key
     *
     * @param string $sandboxPublicKey
     * @return void
     */
    public function setSandboxPublicKey($sandboxPublicKey) :void;

    /**
     * Get the Sandbox Private Key
     *
     * @return string
     */
    public function getSandboxPrivateKey() :string;

    /**
     * Set the Sandbox Private Key
     *
     * @param string $sandboxPrivateKey
     * @return void
     */
    public function setSandboxPrivateKey($sandboxPrivateKey) :void;

    /**
     * Get the Production Public Key
     *
     * @return string
     */
    public function getProductionPublicKey() :string;

    /**
     * Set the Production Public Key
     *
     * @param string $productionPublicKey
     * @return void
     */
    public function setProductionPublicKey($productionPublicKey) :void;

    /**
     * Get the Production Private Key
     *
     * @return string
     */
    public function getProductionPrivateKey() :string;

    /**
     * Set the Production Private Key
     *
     * @param string $productionPrivateKey
     * @return void
     */
    public function setProductionPrivateKey($productionPrivateKey) :void;

    /**
     * Set the Account Group Id
     *
     * @param string $accountGroupId
     * @return void
     */
    public function setAccountGroupId($accountGroupId) :void;

    /**
     * Return the Account Group Id
     *
     * @return string
     */
    public function getAccountGroupId() :string;

    /**
     * Set the Feature Set
     *
     * @param array $featureSet
     * @return void
     */
    public function setFeatureSet($featureSet) :void;

    /**
     * Return the Feature Set
     *
     * @return array
     */
    public function getFeatureSet() :array;

    /**
     * Gets ImsOrgig assigned to the instance
     *
     * @return mixed
     */
    public function getImsOrgId() :mixed;

    /**
     * Sets ImsOrgId assigned to the instance
     *
     * @param string $imsOrgId
     * @return void
     */
    public function setImsOrgId($imsOrgId) :void;

    /**
     * Clear Catalog Data
     *
     * @param DataSpaceInterface $dataSpace
     * @return string
     */
    public function clearCatalog($dataSpace) :string;
}
