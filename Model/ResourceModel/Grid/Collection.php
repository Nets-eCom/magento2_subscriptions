<?php

/**
 * Grid Grid Collection.
 * @category    Webkul
 * @author      Webkul Software Private Limited
 */
namespace Dibs\EasyCheckout\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id_subscription_data';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init('Dibs\EasyCheckout\Model\Grid', 'Dibs\EasyCheckout\Model\ResourceModel\Grid');
    }
}