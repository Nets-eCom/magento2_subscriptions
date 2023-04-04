<?php
namespace Dibs\EasyCheckout\Model\Client\DTO;

use Dibs\EasyCheckout\Model\Client\DTO\Payment\UnscheduledSubscriptionBulkItems;

class GetUnscheduledSubscriptionChargeResponse
{
    
    /** @var UnscheduledSubscriptionBulkItems $page */
    protected $page;

    /**
     * @var float $amount
     */
    protected $more;
    
    /**
     * @var float $status
     */
    protected $status;

    /**
     * If response is not empty, we fill the values from the API.
     *
     * GetUnscheduledSubscriptionChargeResponse constructor.
     * @param $response string
     */
    public function __construct($response = "")
    {
        if ($response !== "") {
            $data = json_decode($response);
            
            if (!empty((array)$data->page)) {
              
                $page = [];
                foreach($data->page as $item) {
                    $bulkItems = new UnscheduledSubscriptionBulkItems($item);
                    $items['unscheduledSubscriptionId'] = $bulkItems->setUnscheduledSubscriptionId($item->unscheduledSubscriptionId);
                    $items['paymentId'] = $bulkItems->setPaymentId($item->paymentId);
                    if(isset($item->chargeId) && !empty($item->chargeId)) {
                       $items['chargeId'] = $bulkItems->setChargeId($item->chargeId);
                    } else {
                       $items['chargeId'] = "";
                    }
                    $items['status'] = $bulkItems->setStatus($item->status);
                    
                    $page[] = $items;
                }
            }
            $this->setUnscheduledSubscriptionItems($page);
            $this->setMore($data->more);
            $this->setStatus($data->status);
        }
    }  
    
    /**
     * @return UnscheduledSubscriptionBulkItems
     */
    public function getUnscheduledSubscriptionItems()
    {
        return $this->page;
    }

    /**
     * @param UnscheduledSubscriptionBulkItems $page
     * @return GetUnscheduledSubscriptionChargeResponse
     */
    public function setUnscheduledSubscriptionItems($page)
    {
        $this->page = $page;
        return $this;
    }
    
    /**
     * @return UnscheduledSubscriptionBulkItems[]
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param UnscheduledSubscriptionBulkItems[] $page
     * @return GetUnscheduledSubscriptionChargeResponse
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getMore()
    {
        return $this->more;
    }

    /**
     * @param string $more
     * @return GetUnscheduledSubscriptionChargeResponse
     */
    public function setMore($more)
    {
        $this->more = $more;
        return $this;
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
     * @return GetUnscheduledSubscriptionChargeResponse
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    
    protected function _get($obj, $key)
    {
        $arr = (array)$obj;
        if (isset($arr[$key])) {
            return $arr[$key];
        }

        return null;
    }

}
