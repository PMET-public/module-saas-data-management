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
use MagentoEse\SaasDataManagement\Model\ServicesConfigInterface;

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
    private const ENVIRONMENT_URL = 'sassdatamanagement/index/clearenvironment';

    /**
     * @var ServicesConfigInterface
     */
    private $servicesConfig;

    /**
     * @param Context $context
     * @param ServicesConfigInterface $servicesConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        ServicesConfigInterface $servicesConfig,
        array $data = []
    ) {
        $this->servicesConfig = $servicesConfig;
        parent::__construct($context, $data);
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
     * Return ajax url for the environment route
     *
     * @return string
     */
    public function getEnvironmentUrl() : string
    {
        return $this->getUrl(self::ENVIRONMENT_URL);
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

        // $html .= $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData(
        //     [
        //         'id' => 'cancel_environment_button',
        //         'label' => __('Cancel')
        //     ]
        // )->toHtml();

        return $html;
    }
}
