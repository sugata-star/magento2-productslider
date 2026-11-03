<?php
namespace Citytech\ProductSlider\Controller\Adminhtml\Notice;

use Citytech\ProductSlider\Model\GridFactory;
use Citytech\ProductSlider\Model\ResourceModel\Grid as GridResource;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Delete extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Citytech_ProductSlider::notice_list';

    /** @var GridFactory */
    private $gridFactory;
    /** @var GridResource */
    private $gridResource;

    public function __construct(Context $context, GridFactory $gridFactory, GridResource $gridResource)
    {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        $this->gridResource = $gridResource;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can not find a notice to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $model = $this->gridFactory->create();
            $this->gridResource->load($model, $id);
            if (!$model->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('This notice no longer exists.'));
            }
            $this->gridResource->delete($model);
            $this->messageManager->addSuccessMessage(__('The notice has been deleted.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
