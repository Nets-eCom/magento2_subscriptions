<?php
namespace Dibs\EasyCheckout\Model\Client\DTO;

use Dibs\EasyCheckout\Model\Client\DTO\Payment\UnscheduledSubscriptionChargeOrder;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\CreatePaymentWebhook;

class CreateUnscheduledSubscriptionCharge extends AbstractRequest
{

    /**
     * Required
     * @var string $externalBulkChargeId
     */
    protected $externalBulkChargeId;

    /**
     * Required
     * @var unscheduledSubscriptions UnscheduledSubscriptionChargeOrder[]
     */
    protected $unscheduledSubscriptions;
    
    /** @var CreatePaymentWebhook[] */
    protected $webHooks;
    
    /**
     * @return string
     */
    public function getExternalBulkChargeId()
    {
        return $this->externalBulkChargeId;
    }

    /**
     * @param string $externalBulkChargeId
     * @return CreateUnscheduledSubscriptionCharge
     */
    public function setExternalBulkChargeId($externalBulkChargeId)
    {
        $this->externalBulkChargeId = $externalBulkChargeId;
        return $this;
    }


    /**
     * @return UnscheduledSubscriptionChargeOrder[]
     */
    public function getUnscheduledSubscriptionIds()
    {
        return $this->unscheduledSubscriptions;
    }

    /**
     * @param UnscheduledSubscriptionChargeOrder[] $unscheduledSubscriptions
     * @return CreateUnscheduledSubscriptionCharge
     */
    public function setUnscheduledSubscriptionIds($unscheduledSubscriptions)
    {
        $this->unscheduledSubscriptions = $unscheduledSubscriptions;
        return $this;
    }
    
    /**
     * @return CreatePaymentWebhook[]
     */
    public function getWebHooks() {
        return $this->webHooks;
    }

    /**
     * @param CreatePaymentWebhook[] $webHooks
     * @return CreatePayment
     */
    public function setWebHooks($webHooks) {
        $this->webHooks = $webHooks;
        return $this;
    }
    
    public function toJSON()
    {
        return json_encode(
            $this->utf8ize($this->toArray())
        );
    }

    public function toArray()
    {
        
        $unscheduledSubscriptions = [];
        if (!empty($this->getUnscheduledSubscriptionIds())) {
            foreach ($this->getUnscheduledSubscriptionIds() as $sub) {
                $unscheduledSubscriptions[] = $sub->toArray();
            }
        }
                
        $data = [
            'externalBulkChargeId' => $this->getExternalBulkChargeId(),
            'unscheduledSubscriptions' => $unscheduledSubscriptions
        ];
        
        if ($this->webHooks) {
            $webhooks = [];
            foreach ($this->webHooks as $w) {
                $webhooks[] = $w->toArray();
            }
            $data['notifications']['webhooks'] = $webhooks;
        }

        return $data;
    }


}