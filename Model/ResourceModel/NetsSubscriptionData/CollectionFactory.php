<?php

namespace Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData;

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
        $instanceName = \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData\Collection::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * {@inheritdoc}
     */
    public function create($customerId = null)
    {
        /** @var \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData\Collection $collection */
        $collection = $this->objectManager->create($this->instanceName);
        
        if ($customerId) {
            $collection->addFieldToFilter('customer_id', $customerId);
        }
        return $collection;
    }
}
