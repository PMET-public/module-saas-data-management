<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MagentoEse\SaasDataManagement\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\App\Config\ScopeConfigInterface;

class DeleteEnvironmentData extends Field
{
    /**
     * @var string
     */
    //phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore,Magento2.Commenting.ClassPropertyPHPDocFormatting.Missing
    protected $_template = 'MagentoEse_SaasDataManagement::system/config/clear-environment.phtml';

    /**
     * @var string
     */
    private const CLEARDATA_URL = 'sassdatamanagement/index/clearenvironment';

    /**
     * @var string
     */
    private const CLEARAPI_URL = 'sassdatamanagement/index/unassigndataspace';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfigInterface;

    /**
     * @param Context $context
     * @param array $data
     * @param ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfigInterface,
        array $data = []
    ) {
        $this->scopeConfigInterface = $scopeConfigInterface;
        parent::__construct($context,$data);
    }

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element) : string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
     //phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore,Magento2.Annotation.MethodArguments.NoCommentBlock
    protected function _getElementHtml(AbstractElement $element) : string
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for the clear data route
     *
     * @return string
     */
    public function getClearDataUrl() : string
    {
        return $this->getUrl(self::CLEARDATA_URL);
    }

    /**
     * Return ajax url for the clear data route
     *
     * @return string
     */
    public function getUnassignDataSpaceUrl() : string
    {
        return $this->getUrl(self::CLEARAPI_URL);
    }

    /**
     * Get environment name string for display in UI
     *
     * @return string
     */
    public function getEnvironmentName() : string
    {
        return $this->scopeConfigInterface->getValue('services_connector/services_id/environment_name',
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * Generate collect button html
     *
     * @return string
     * @throws LocalizedException
     */
    public function getButtonHtml() : string
    {
        //phpcs:ignore Magento2.PHP.LiteralNamespaces.LiteralClassUsage
        $html = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(
            [
                'id' => 'clear_environment_button',
                'label' => __('Clear Data Space Data')
            ]
        )->toHtml();

        //phpcs:ignore Magento2.PHP.LiteralNamespaces.LiteralClassUsage
        $html .= $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(
            [
                'id' => 'unassign_dataspace_button',
                'label' => __('Unassign Data Space')
            ]
        )->toHtml();

        return $html;
    }
}
