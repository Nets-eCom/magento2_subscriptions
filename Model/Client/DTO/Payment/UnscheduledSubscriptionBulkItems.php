<?php
namespace Dibs\EasyCheckout\Model\Client\DTO\Payment;

class UnscheduledSubscriptionBulkItems
{

    /**
     * @var string $unscheduledSubscriptionId
     */
    protected $unscheduledSubscriptionId;

    /**
     * @var string $paymentId
     */
    protected $paymentId;

    /**
     * @var string $chargeId
     */
    protected $chargeId;

    /**
     * @var string $status
     */
    protected $status;

    /**
     * @return string
     */
    public function getUnscheduledSubscriptionId()
    {
        return $this->unscheduledSubscriptionId;
    }

    /**
     * @param string $unscheduledSubscriptionId
     * @return UnscheduledSubscriptionBulkItems
     */
    public function setUnscheduledSubscriptionId($unscheduledSubscriptionId)
    {
        $this->unscheduledSubscriptionId = $unscheduledSubscriptionId;
        return $this->unscheduledSubscriptionId;
    }

    /**
     * @return string
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * @param string $paymentId
     * @return UnscheduledSubscriptionBulkItems
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
        return $this->paymentId;
    }

    /**
     * @return string
     */
    public function getChargeId()
    {
        return $this->chargeId;
    }

    /**
     * @param float $chargeId
     * @return UnscheduledSubscriptionBulkItems
     */
    public function setChargeId($chargeId)
    {
        $this->chargeId = $chargeId;
        return $this->chargeId;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return UnscheduledSubscriptionBulkItems
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this->status;
    }

}