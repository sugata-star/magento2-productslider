<?php
namespace Citytech\ProductSlider\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;

class CustomData extends Template
{
    protected $resourceConnection;

    public function __construct(
        Template\Context $context,
        ResourceConnection $resourceConnection,
        ?array $data = []
    ) {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $data ?? []);
    }

    public function getCustomTableData()
    {
        $isEnabled = $this->_scopeConfig->getValue(
            'productslider_config/general/enable',
            ScopeInterface::SCOPE_STORE
        );

        if (!$isEnabled) {
            return [];
        }

        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('custom_notice');

        $select = $connection->select()->from($tableName, ['id', 'title', 'content', 'is_active', 'created_at', 'update_time'])
            ->where('is_active = ?', 1);

        return $connection->fetchAll($select);
    }
}

