<?php

namespace Dibs\EasyCheckout\Model;

use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;
use \Dibs\EasyCheckout\Api\Data\NetsSubscriptionDataInterface;

/**
 * Class File
 * @package Dibs\EasyCheckout\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NetsSubscriptionData extends AbstractModel implements NetsSubscriptionDataInterface, IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'nets_subscription_data';

    /**
     * Post Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData');
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
     * Get Nets Payment Id
     *
     * @return string|null
     */
    public function getNetsPaymentId()
    {
        return $this->getData(self::NETS_PAYMENT_ID);
    }

    /**
     * Get Nets Subscription Id
     *
     * @return string|null
     */
    public function getNetsSubscriptionId()
    {
        return $this->getData(self::NETS_SUBSCRIPTION_ID);
    }
    
    /**
     * Get Nets Subscription Name
     *
     * @return string|null
     */
    public function getNetsSubscriberName()
    {
        return $this->getData(self::NETS_SUBSCRIBER_NAME);
    }
    /**
     * Get Nets Subscription Interval
     *
     * @return string|null
     */
    public function getNetsSubscriptionInterval()
    {
        return $this->getData(self::NETS_SUBSCRIPTION_INTERVAL);
    }

    /**
     * Get Nets Subscription Specific Interval
     *
     * @return string|null
     */
    public function getNetsSubscriptionExpires()
    {
        return $this->getData(self::NETS_SUBSCRIPTION_EXPIRES);
    }
    
    /**
     * Get Nets Subscription End Date
     *
     * @return string|null
     */
    public function getNetsSubscriptionEnd()
    {
        return $this->getData(self::NETS_SUBSCRIPTION_END);
    }
    
    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }
    
    /**
     * Get Interval Count
     *
     * @return string|null
     */
    public function getIntervalCount()
    {
        return $this->getData(self::INTERVAL_COUNT);
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
     * Get Order Entity Id
     *
     * @return string|null
     */
    public function getOrderEntityId()
    {
        return $this->getData(self::ORDER_ENTITY_ID);
    }
    
    /**
     * Get Order Amount
     *
     * @return string|null
     */
    public function getNetsOrderAmount()
    {
        return $this->getData(self::NETS_ORDER_AMOUNT);
    }
    
    /**
     * Get Order Currency
     *
     * @return string|null
     */
    public function getNetsOrderCurrency()
    {
        return $this->getData(self::NETS_ORDER_CURRENCY);
    }
    
    /**
     * Get Charge Date
     *
     * @return string|null
     */
    public function getChargeDate()
    {
        return $this->getData(self::CHARGE_DATE);
    }
    
    /**
     * Get Customer Id
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }
    
    /**
     * Get Id Subscription Data
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::SUBSCRIPTION_DATA_ID);
    }

    /**
     * Return identities
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
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
     * Set Nets Payment Id
     *
     * @param string $netsPaymentId
     * @return $this
     */
    public function setNetsPaymentId($netsPaymentId)
    {
        return $this->setData(self::NETS_PAYMENT_ID, $netsPaymentId);
    }

    /**
     * Set Nets Subscription Name
     *
     * @param string $netsSubscriberName
     * @return $this
     */
    public function setNetsSubscriberName($netsSubscriberName)
    {
        return $this->setData(self::NETS_SUBSCRIBER_NAME, $netsSubscriberName);
    }

    /**
     * Set Nets Subscription Id
     *
     * @param string $netsSubscriptionId
     * @return $this
     */
    public function setNetsSubscriptionId($netsSubscriptionId)
    {
        return $this->setData(self::NETS_SUBSCRIPTION_ID, $netsSubscriptionId);
    }
    
    /**
     * Set Nets Subscription Interval
     *
     * @param string $netsSubscriptionIdInterval
     * @return $this
     */
    public function setNetsSubscriptionInterval($netsSubscriptionIdInterval)
    {
        return $this->setData(self::NETS_SUBSCRIPTION_INTERVAL, $netsSubscriptionIdInterval);
    }
    
    /**
     * Set Nets Subscription Specific Interval
     *
     * @param string $netsSubscriptionSpecificInterval
     * @return $this
     */
    public function setNetsSubscriptionExpires($netsSubscriptionSpecificInterval)
    {
        return $this->setData(self::NETS_SUBSCRIPTION_EXPIRES, $netsSubscriptionSpecificInterval);
    }
    
    /**
     * Set Nets Subscription End Date
     *
     * @param string $netsSubscriptionEnd
     * @return $this
     */
    public function setNetsSubscriptionEnd($netsSubscriptionEnd)
    {
        return $this->setData(self::NETS_SUBSCRIPTION_END, $netsSubscriptionEnd);
    }

    /**
     * Set Status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set Interval Count
     *
     * @param string $intervalCount
     * @return $this
     */
    public function setIntervalCount($intervalCount)
    {
        return $this->setData(self::INTERVAL_COUNT, $intervalCount);
    }
    
    /**
     * Set Created Date
     *
     * @param string $createdDate
     * @return $this
     */
    public function setCreatedDate($createdDate)
    {
        return $this->setData(self::CREATED_DATE, $createdDate);
    }
    
    /**
     * Set Order Entity Id
     *
     * @param string $orderEntityId
     * @return $this
     */
    public function setOrderEntityId($orderEntityId)
    {
        return $this->setData(self::ORDER_ENTITY_ID, $orderEntityId);
    }
    
    /**
     * Set Order Amount
     *
     * @param string $orderAmount
     * @return $this
     */
    public function setNetsOrderAmount($orderAmount)
    {
        return $this->setData(self::NETS_ORDER_AMOUNT, $orderAmount);
    }
    
    /**
     * Set Order Currency
     *
     * @param string $orderCurrency
     * @return $this
     */
    public function setNetsOrderCurrency($orderCurrency)
    {
        return $this->setData(self::NETS_ORDER_CURRENCY, $orderCurrency);
    }
    
    /**
     * Set Charge Date
     *
     * @param string $chargeDate
     * @return $this
     */
    public function setChargeDate($chargeDate)
    {
        return $this->setData(self::CHARGE_DATE, $chargeDate);
    }
    
    /**
     * Set Customer Id
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::SUBSCRIPTION_DATA_ID, $id);
    }
}