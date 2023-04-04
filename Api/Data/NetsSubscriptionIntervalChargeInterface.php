<?php

namespace Dibs\EasyCheckout\Api\Data;

interface NetsSubscriptionIntervalChargeInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const INTERVAL_ID                        = 'intervalID';
    const ORDER_ID                           = 'OrderID';
    const SUBSCRIPTION_ID                    = 'SubscriptinID';
    const PAYMENT_ID                         = 'PaymentID';
    const CHARGE_ID                          = 'ChargeID';
    const SUBSCRIPTION_STATUS                = 'SubStatus';
    const CREATED_DATE                       = 'created_date';
    const UPDATED_DATE                       = 'updated_at';
    const BULK_ID                            = 'BulkId';
    const CHILD_ORDER_ID                     = 'ChildOrderId';

    /**#@-*/


    /**
     * Get Order Id
     *
     * @return string|null
     */
    public function getOrderId();

    
    /**
     * Get Nets Subscription Id
     *
     * @return string|null
     */
    public function getSubscriptionId();

    
    /**
     * Get Nets Payment Id
     *
     * @return string|null
     */
    public function getPaymentId();

    /**
     * Get Nets charge Id
     *
     * @return string|null
     */
    public function getChargeId();

    /**
     * Get Nets Subscription status
     *
     * @return string|null
     */
    public function getSubscriptionStatus();
    
    /**
     * Get Created Date
     *
     * @return string|null
     */
    public function getCreatedDate();

    /**
     * Get updated Date
     *
     * @return string|null
     */
    public function getupdateDate();

    /**
     * Get Bulk Id
     *
     * @return string|null
     */
    public function getBulkID();

    /**
     * Get Nets Child order ID
     *
     * @return string|null
     */
    public function getChildOrderId();
}