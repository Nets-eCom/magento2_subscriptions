<?php

namespace Dibs\EasyCheckout\Model;

use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;
use \Dibs\EasyCheckout\Api\Data\NetsSubscriptionIntervalChargeInterface;

/**
 * Class File
 * @package Dibs\EasyCheckout\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NetsSubscriptionIntervalCharge extends AbstractModel implements NetsSubscriptionIntervalChargeInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'nets_subscription_interval_charge';

    /**
     * Post Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionIntervalCharge');
    }

    /**
     * Get Order Id
     *
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    
    /**
     * Get Nets Subscription Id
     *
     * @return string|null
     */
    public function getSubscriptionId()
    {
        return $this->getData(self::SUBSCRIPTION_ID);
    }

    /**
     * Get Nets Payment Id
     *
     * @return string|null
     */
    public function getPaymentId()
    {
        return $this->getData(self::PAYMENT_ID);
    }

    /**
     * Get Nets Subscription Charge Id
     *
     * @return string|null
     */
    public function getChargeId()
    {
        return $this->getData(self::CHARGE_ID);
    }

    /**
     * Get Nets Subscription Status
     *
     * @return string|null
     */
    public function getSubscriptionStatus()
    {
        return $this->getData(self::SUBSCRIPTION_STATUS);
    }
    
    /**
     * Get Created Date
     *
     * @return string|null
     */
    public function getCreatedDate()
    {
        return $this->getData(self::CREATED_DATE);
    }
    
    /**
     * Get Updated Date
     *
     * @return string|null
     */
    public function getupdateDate()
    {
        return $this->getData(self::UPDATED_DATE);
    }

    /**
     * Get Bulk Id
     *
     * @return string|null
     */
    public function getBulkID()
    {
        return $this->getData(self::BULK_ID);
    }

    /**
     * Get Child Order Id
     *
     * @return string|null
    */
    public function getChildOrderId()
    {
        return $this->getData(self::CHILD_ORDER_ID);
    }

    /**
     * Set Order Id
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * Set Nets Subscription Id
     *
     * @param string $subscriptionId
     * @return $this
     */
    public function setSubscriptionId($subscriptionId)
    {
        return $this->setData(self::SUBSCRIPTION_ID, $subscriptionId);
    }

    /**
     * Set Payment Id
     *
     * @param string $PaymentId
     * @return $this
     */
    public function setPaymentId($PaymentId)
    {
        return $this->setData(self::PAYMENT_ID, $PaymentId);
    }

    
    /**
     * Set Charge ID
     *
     * @param string $chargeID
     * @return $this
     */
    public function setChargeId($chargeID)
    {
        return $this->setData(self::CHARGE_ID, $chargeID);
    }
    
    /**
     * Set Subscription Status
     *
     * @param string $SubscriptionStatus
     * @return $this
     */
    public function setSubscriptionStatus($SubscriptionStatus)
    {
        return $this->setData(self::SUBSCRIPTION_STATUS, $SubscriptionStatus);
    }
    
    /**
     * Set Created Date
     *
     * @param string $CreatedDate
     * @return $this
     */
    public function setCreatedDate($CreatedDate)
    {
        return $this->setData(self::CREATED_DATE, $CreatedDate);
    }

    /**
     * Set Status
     *
     * @param string $updateDate
     * @return $this
     */
    public function setUpdateDate($updateDate)
    {
        return $this->setData(self::UPDATED_DATE, $updateDate);
    }
    
    /**
     * Set Bulk ID
     *
     * @param string $Bulk Id
     * @return $this
     */
    public function setBulkId($BulkID)
    {
        return $this->setData(self::BulkId, $BulkID);
    }
    
    /**
     * Set Child Order ID
     *
     * @param string $childOrderId
     * @return $this
     */
    public function setChildOrderId($childOrderId)
    {
        return $this->setData(self::CHILD_ORDER_ID, $childOrderId);
    }
   
}