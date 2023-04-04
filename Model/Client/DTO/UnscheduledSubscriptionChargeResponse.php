<?php
namespace Dibs\EasyCheckout\Model\Client\DTO;

class UnscheduledSubscriptionChargeResponse
{

    /** @var string $bulkId */
    protected $bulkId;


    /**
     * UnscheduledSubscriptionChargeResponse constructor.
     * @param $response string
     */
    public function __construct($response = "")
    {
        if ($response !== "") {
            $data = json_decode($response);
            $this->setBulkId($data->bulkId);
        }
    }

    /**
     * @return string
     */
    public function getBulkId()
    {
        return $this->bulkId;
    }

    /**
     * @param string $bulkId
     */
    public function setBulkId($bulkId)
    {
        $this->bulkId = $bulkId;
        return $this;
    }

}
