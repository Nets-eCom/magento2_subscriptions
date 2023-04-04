<?php

namespace Dibs\EasyCheckout\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class NetsSubscriptionData extends AbstractDb
{
    /**
     * Post Abstract Resource Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_init('nets_subscription_data', 'id_subscription_data');
    }
}