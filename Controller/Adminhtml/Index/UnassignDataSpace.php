<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MagentoEse\SaasDataManagement\Controller\Adminhtml\Index;

use Magento\Framework\App\ActionInterface as AbstractAction;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\Cache\TypeListInterface;

/**
 * resets value of data space in core_config_data
 */
class UnassignDataSpace implements AbstractAction
{
    /** @var ResourceConfig  */
    protected $resourceConfig;

    /** @var JsonFactory  */
    protected $resultJasonFactory;

    /** @var TypeListInterface  */
    protected $typeList;

    /**
     * @param ResourceConfig $resourceConfig
     * @param JsonFactory $resultJasonFactory
     * @param TypeListInterface $typeList
     */

    public function __construct(
        ResourceConfig $resourceConfig,
        JsonFactory $resultJasonFactory,
        TypeListInterface $typeList
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->resultJasonFactory = $resultJasonFactory;
        $this->typeList = $typeList;
    }
      
    /**
     * Clear Data Space settings
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        $jsonResult = $this->resultJasonFactory->create();
        $this->resourceConfig->saveConfig("services_connector/services_id/environment", "", "default", 0)
        ->saveConfig("services_connector/services_id/environment_name", "", "default", 0)
        ->saveConfig("services_connector/services_id/environment_id", "", "default", 0);
        //clear config cache
        $this->typeList->cleanType('config');
        
        return $jsonResult->setData('');
    }
}
