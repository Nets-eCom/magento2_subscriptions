<?php
namespace Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Remittance File Collection Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Dibs\EasyCheckout\Model\NetsSubscriptionData::class, \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData::class);
    }
}