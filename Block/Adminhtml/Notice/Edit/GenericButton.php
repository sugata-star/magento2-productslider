<?php
namespace Citytech\ProductSlider\Block\Adminhtml\Notice\Edit;

use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    /** @var Context */
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getId()
    {
        return (int)$this->context->getRequest()->getParam('id') ?: null;
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
