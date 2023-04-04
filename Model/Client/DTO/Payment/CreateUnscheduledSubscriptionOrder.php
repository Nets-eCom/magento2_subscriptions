<?php
namespace Dibs\EasyCheckout\Model\Client\DTO\Payment;

use Dibs\EasyCheckout\Model\Client\DTO\AbstractRequest;

class CreateUnscheduledSubscriptionOrder extends AbstractRequest
{

    /**
     * Required
     * @var string $create
     */
    protected $create;

    /**
     * @var string $unscheduledSubscriptionId
     */
    protected $unscheduledSubscriptionId;

    /**
     * @return string
     */
    public function getCreate()
    {
        return $this->create;
    }
    
    /**
     * @param string $create
     * @return CreatePaymentOrder
     */
    public function setCreate($create)
    {
        $this->create = $create;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnscheduledSubscriptionId()
    {
        return $this->unscheduledSubscriptionId;
    }
    
    /**
     * @param string $unscheduledSubscriptionId
     * @return CreatePaymentOrder
     */
    public function setUnscheduledSubscriptionId($unscheduledSubscriptionId)
    {
        $this->unscheduledSubscriptionId = $unscheduledSubscriptionId;
        return $this;
    }
    
    public function toArray()
    {
        $data = [
            'create' => $this->getCreate()
        ];
        
        if($this->unscheduledSubscriptionId) {
            $data['unscheduledSubscriptionId'] = $this->getUnscheduledSubscriptionId();
        }
        
        return $data;
    }


}