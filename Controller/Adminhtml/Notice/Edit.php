<?php
namespace Citytech\ProductSlider\Controller\Adminhtml\Notice;

use Citytech\ProductSlider\Model\GridFactory;
use Citytech\ProductSlider\Model\ResourceModel\Grid as GridResource;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Citytech_ProductSlider::notice_list';

    /** @var PageFactory */
    private $resultPageFactory;
    /** @var GridFactory */
    private $gridFactory;
    /** @var GridResource */
    private $gridResource;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GridFactory $gridFactory,
        GridResource $gridResource
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->gridFactory = $gridFactory;
        $this->gridResource = $gridResource;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = $this->gridFactory->create();

        if ($id) {
            $this->gridResource->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This notice no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Citytech_ProductSlider::notice_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Special Notices'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ? __('Edit Notice #%1', $model->getId()) : __('New Notice')
        );
        return $resultPage;
    }
}
