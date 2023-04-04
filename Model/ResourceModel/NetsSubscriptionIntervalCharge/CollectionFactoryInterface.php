<?php

namespace Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionIntervalCharge;

/**
 * Class CollectionFactoryInterface
 */
interface CollectionFactoryInterface
{
    /**
     * Create class instance with specified parameters
     *
     * @param int $intervalId
     * @return \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionIntervalCharge\Collection
     */
    public function create($intervalId = null);
}
