<?php
namespace Citytech\ProductSlider\Model\Notice;

use Citytech\ProductSlider\Model\ResourceModel\Grid\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Data provider for the productslider_notice_form UI component (edit / add form).
 */
class FormDataProvider extends AbstractDataProvider
{
    /** @var DataPersistorInterface */
    private $dataPersistor;

    /** @var array|null */
    private $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        $this->loadedData = [];
        foreach ($this->collection->getItems() as $item) {
            $this->loadedData[$item->getId()] = $item->getData();
        }

        // restore what the user typed after a failed save
        $persisted = $this->dataPersistor->get('productslider_notice');
        if (!empty($persisted)) {
            $item = $this->collection->getNewEmptyItem();
            $item->setData($persisted);
            $this->loadedData[$item->getId()] = $item->getData();
            $this->dataPersistor->clear('productslider_notice');
        }

        return $this->loadedData;
    }
}
