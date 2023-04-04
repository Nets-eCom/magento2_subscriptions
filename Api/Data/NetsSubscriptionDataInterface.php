<?php

namespace Dibs\EasyCheckout\Api\Data;

interface NetsSubscriptionDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const SUBSCRIPTION_DATA_ID               = 'id_subscription_data';
    const ORDER_ID                           = 'order_id';
    const NETS_PAYMENT_ID                    = 'nets_payment_id';
    const NETS_SUBSCRIPTION_ID               = 'nets_subscription_id';
    const NETS_SUBSCRIBER_NAME               = 'nets_subscriber_name';
    const NETS_SUBSCRIPTION_INTERVAL         = 'nets_subscription_interval';
    const NETS_SUBSCRIPTION_EXPIRES          = 'nets_subscription_specific_interval';
    const NETS_SUBSCRIPTION_END              = 'nets_subscription_enddate';
    const STATUS                             = 'status';
    const INTERVAL_COUNT                     = 'intervalCount';
    const CREATED_DATE                       = 'created_date';
    const ORDER_ENTITY_ID                    = 'order_entity_id';
    const NETS_ORDER_AMOUNT                  = 'nets_order_amount';
    const NETS_ORDER_CURRENCY                = 'nets_order_currency';
    const CHARGE_DATE                        = 'chargeDate';
    const CUSTOMER_ID                        = 'customer_id';
    /**#@-*/


    /**
     * Get Order Id
     *
     * @return string|null
     */
    public function getOrderId();
    
    /**
     * Get Nets Payment Id
     *
     * @return string|null
     */
    public function getNetsPaymentId();

    /**
     * Get Nets Subscription Id
     *
     * @return string|null
     */
    public function getNetsSubscriptionId();

    /**
     * Get Nets Subscription Name
     *
     * @return string|null
     */
    public function getNetsSubscriberName();
    
    /**
     * Get Nets Subscription Interval
     *
     * @return string|null
     */
    public function getNetsSubscriptionInterval();
    
    /**
     * Get Nets Subscription Specific Interval
     *
     * @return string|null
     */
    public function getNetsSubscriptionExpires();

    /**
     * Get Nets Subscription End Date
     *
     * @return string|null
     */
    public function getNetsSubscriptionEnd();

    /**
     * Get Status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Get Interval Count	
     *
     * @return string|null
     */
    public function getIntervalCount();
    
    /**
     * Get Created Date
     *
     * @return string|null
     */
    public function getCreatedDate();

    /**
     * Get Order Entity Id
     *
     * @return string|null
     */
    public function getOrderEntityId();
    
    /**
     * Get Order Amount
     *
     * @return string|null
     */
    public function getNetsOrderAmount();
    
    /**
     * Get Order Currency
     *
     * @return string|null
     */
    public function getNetsOrderCurrency();
    
    /**
     * Get Charge Date
     *
     * @return string|null
     */
    public function getChargeDate();
    
    
    /**
     * Get Customer Id
     *
     * @return string|null
     */
    public function getCustomerId();
    
    
    /**
     * Get Id Subscription Data
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Order Id
     *
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId);
    
    /**
     * Set Nets Payment Id
     *
     * @param string $netsPaymentId
     * @return $this
     */
    public function setNetsPaymentId($netsPaymentId);

    /**
     * Set Nets Subscription Id
     *
     * @param string $netsSubscriptionId
     * @return $this
     */
    public function setNetsSubscriptionId($netsSubscriptionId);
    
    /**
     * Set Nets Subscription Name
     *
     * @param string $netsSubscriberName
     * @return $this
     */
    public function setNetsSubscriberName($netsSubscriberName);

    /**
     * Set Nets Subscription Interval
     *
     * @param string $netsSubscriptionIdInterval
     * @return $this
     */
    public function setNetsSubscriptionInterval($netsSubscriptionIdInterval);

    /**
     * Set Nets Subscription Specific Interval
     *
     * @param string $netsSubscriptionSpecificInterval
     * @return $this
     */
    public function setNetsSubscriptionExpires($netsSubscriptionSpecificInterval);
    
    /**
     * Set Nets Subscription End Date
     *
     * @param string $netsSubscriptionEnd
     * @return $this
     */
    public function setNetsSubscriptionEnd($netsSubscriptionEnd);

    /**
     * Set Status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Set Interval Count
     *
     * @param string $intervalCount
     * @return $this
     */
    public function setIntervalCount($intervalCount);
    
    /**
     * Set Created Date
     *
     * @param string $createdDate
     * @return $this
     */
    public function setCreatedDate($createdDate);
    
    /**
     * Set Order Entity Id
     *
     * @param string $orderEntityId
     * @return $this
     */
    public function setOrderEntityId($orderEntityId);
    
    /**
     * Set Order Amount
     *
     * @param string $orderAmount
     * @return $this
     */
    public function setNetsOrderAmount($orderAmount);
    
    /**
     * Set Order Currency
     *
     * @param string $orderCurrency
     * @return $this
     */
    public function setNetsOrderCurrency($orderCurrency);
    
    /**
     * Set Charge Date
     *
     * @param string $chargeDate
     * @return $this
     */
    public function setChargeDate($chargeDate);
    
    /**
     * Set Customer Id
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Set Id Subscription Data
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);
}