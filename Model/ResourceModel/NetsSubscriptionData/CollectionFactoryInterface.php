<?php

namespace Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData;

/**
 * Class CollectionFactoryInterface
 */
interface CollectionFactoryInterface
{
    /**
     * Create class instance with specified parameters
     *
     * @param int $customerId
     * @return \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData\Collection
     */
    public function create($customerId = null);
}
