<?php
namespace Citytech\ProductSlider\Block;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class ProductSlider extends Template
{
    const XML_PATH = 'productslider_config/slider/';

    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var Visibility */
    private $productVisibility;
    /** @var ImageHelper */
    private $imageHelper;
    /** @var PricingHelper */
    private $pricingHelper;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        Visibility $productVisibility,
        ImageHelper $imageHelper,
        PricingHelper $pricingHelper,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->productVisibility = $productVisibility;
        $this->imageHelper = $imageHelper;
        $this->pricingHelper = $pricingHelper;
        parent::__construct($context, $data);
    }

    private function getConfig(string $field)
    {
        return $this->_scopeConfig->getValue(self::XML_PATH . $field, ScopeInterface::SCOPE_STORE);
    }

    public function isEnabled(): bool
    {
        return (bool)$this->getConfig('enable');
    }

    public function getSliderTitle(): string
    {
        return (string)($this->getConfig('title') ?: __('Featured Products'));
    }

    /**
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getProducts(): array
    {
        if (!$this->isEnabled()) {
            return [];
        }

        $limit = max(1, (int)($this->getConfig('product_count') ?: 10));
        $categoryId = (int)$this->getConfig('category_id');

        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect([
            'name', 'small_image', 'url_key', 'price', 'special_price',
            'special_from_date', 'special_to_date', 'tax_class_id'
        ])
            ->addStoreFilter()
            ->addAttributeToFilter('status', Status::STATUS_ENABLED)
            ->addAttributeToFilter('visibility', ['in' => $this->productVisibility->getVisibleInCatalogIds()])
            ->addUrlRewrite()
            ->setOrder('created_at', 'DESC')
            ->setPageSize($limit);

        if ($categoryId) {
            $collection->addCategoriesFilter(['in' => [$categoryId]]);
        }

        return $collection->getItems();
    }

    public function getImageUrl($product): string
    {
        return $this->imageHelper->init($product, 'category_page_grid')->getUrl();
    }

    public function getFormattedPrice($product): string
    {
        try {
            $amount = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        } catch (\Throwable $e) {
            $amount = $product->getPrice();
        }
        return $this->pricingHelper->currency((float)$amount, true, false);
    }
}
