<?php

namespace Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionIntervalCharge;

/**
 * Class CollectionFactory
 *
 */
class CollectionFactory implements CollectionFactoryInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    private $instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionIntervalCharge\Collection::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * {@inheritdoc}
     */
    public function create($intervalId = null)
    {
        /** @var \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionIntervalCharge\Collection $collection */
        $collection = $this->objectManager->create($this->instanceName);
        
        if ($intervalId) {
            $collection->addFieldToFilter('intervalID', $intervalId);
        }
        return $collection;
    }
}
