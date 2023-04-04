<?php
namespace Dibs\EasyCheckout\Model\Client\DTO\Payment;

use Dibs\EasyCheckout\Model\Client\DTO\AbstractRequest;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\CreatePaymentOrder;

class UnscheduledSubscriptionChargeOrder extends AbstractRequest
{

    /** @var CreatePaymentOrder */
    protected $order;

    /** @var $unscheduledSubscriptionId string */
    protected $unscheduledSubscriptionId;


    /**
     * @return string
     */
    public function getUnscheduledSubscriptionId()
    {
        return $this->unscheduledSubscriptionId;
    }

    /**
     * @param string $unscheduledSubscriptionId
     * @return UnscheduledSubscriptionChargeOrder
     */
    public function setUnscheduledSubscriptionId($unscheduledSubscriptionId)
    {
        $this->unscheduledSubscriptionId = $unscheduledSubscriptionId;
        return $this;
    }
    
    /**
     * @return CreatePaymentOrder
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param CreatePaymentOrder $order
     * @return CreatePayment
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }
       
    public function toArray()
    {
        return [
            'unscheduledSubscriptionId' => $this->getUnscheduledSubscriptionId(),
            'order' => $this->order->toArray()
        ];
    }

}
