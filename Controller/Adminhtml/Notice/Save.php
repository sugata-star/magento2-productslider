<?php
namespace Citytech\ProductSlider\Controller\Adminhtml\Notice;

use Citytech\ProductSlider\Model\GridFactory;
use Citytech\ProductSlider\Model\ResourceModel\Grid as GridResource;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Citytech_ProductSlider::notice_list';
    const PERSISTOR_KEY = 'productslider_notice';

    /** @var GridFactory */
    private $gridFactory;
    /** @var GridResource */
    private $gridResource;
    /** @var DataPersistorInterface */
    private $dataPersistor;

    public function __construct(
        Context $context,
        GridFactory $gridFactory,
        GridResource $gridResource,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
        $this->gridResource = $gridResource;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $id = (int)($data['id'] ?? 0);
        $model = $this->gridFactory->create();

        if ($id) {
            $this->gridResource->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This notice no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        }

        try {
            $title = trim((string)($data['title'] ?? ''));
            $content = trim((string)($data['content'] ?? ''));
            if ($title === '' || $content === '') {
                throw new LocalizedException(__('Title and content are required.'));
            }

            $model->setTitle($title);
            $model->setContent($content);
            $model->setData('is_active', (int)($data['is_active'] ?? 1));
            // let the database manage these two timestamps
            $model->unsetData('created_at');
            $model->unsetData('update_time');

            $this->gridResource->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the notice.'));
            $this->dataPersistor->clear(self::PERSISTOR_KEY);

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the notice.'));
        }

        $this->dataPersistor->set(self::PERSISTOR_KEY, $data);
        return $resultRedirect->setPath('*/*/edit', $id ? ['id' => $id] : []);
    }
}
