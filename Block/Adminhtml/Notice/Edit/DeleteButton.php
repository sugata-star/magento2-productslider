<?php
namespace Citytech\ProductSlider\Block\Adminhtml\Notice\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        if (!$this->getId()) {
            return [];
        }

        $message = __('Are you sure you want to delete this notice?');
        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => sprintf(
                "deleteConfirm('%s', '%s', {data: {}})",
                $message,
                $this->getUrl('*/*/delete', ['id' => $this->getId()])
            ),
            'sort_order' => 20,
        ];
    }
}
