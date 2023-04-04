<?php

namespace Dibs\EasyCheckout\Cron;

use Dibs\EasyCheckout\Helper\CronCreateOrder as DibsCrontHelper;
use \Magento\Framework\App\ObjectManager;
use Dibs\EasyCheckout\Model\Client\Api\Payment;
use Dibs\EasyCheckout\Model\Client\ClientException;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\OrderItem;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\CreatePaymentOrder;
use Dibs\EasyCheckout\Model\Client\DTO\SubscriptionChargePayment;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\CreateSubscriptionCharge;
use Dibs\EasyCheckout\Model\Dibs\Items;
use Dibs\EasyCheckout\Model\NetsSubscriptionIntervalChargeFactory;

class StatusUpdate {

    protected $_logger;
    protected $dibsCrontHelper;

    /**
     * @var Items $items
     */
    protected $items;

    /**
     * @param DibsCrontHelper $dibsCrontHelper
     */
    protected $helper;
    protected $subscriptionIntervalData;

    public function __construct(
            \Dibs\EasyCheckout\Model\Client\Api\Payment $paymentApi,
            \Dibs\EasyCheckout\Model\NetsSubscriptionIntervalChargeFactory $subscriptionIntervalData,
            \Dibs\EasyCheckout\Helper\Data $helper,
            DibsCrontHelper $dibsCrontHelper,
            Items $itemsHandler
    ) {
        $this->_cronHelper = $dibsCrontHelper;
        $this->helper = $helper;
        $this->paymentApi = $paymentApi;
        $this->items = $itemsHandler;
        $this->subscriptionIntervalData = $subscriptionIntervalData;
        // $this->storeManager = $storeManager;
    }

    public function execute() {
        $subscriptionIntervalQuery = $this->subscriptionIntervalData->create()->getCollection()->addFieldToSelect('BulkId')->addFieldToFilter('SubStatus', 'Pending');
        $result = $subscriptionIntervalQuery->getData();
        $result = $subscriptionIntervalQuery->getData();

        $currentDate = date("Y-m-d H:i:s");

        foreach ($result as $values) {
            $response = $this->paymentApi->getChargeUnscheduledSubscription($values['BulkId']);
            foreach ($response->getUnscheduledSubscriptionItems() as $data) {
                
                $sub_id = $data['unscheduledSubscriptionId'];
                $payment_id = $data['paymentId'];
                $charge_id = $data['chargeId'];
                $status = $data['status'];
                $post = $this->subscriptionIntervalData->create()->getCollection()
                        ->addFieldToFilter('SubStatus', 'Pending')
                        ->addFieldToFilter('SubscriptinID', $sub_id)
                        ->addFieldToFilter('PaymentID', $payment_id);

                $postUpdate = $post->load();
                foreach ($postUpdate as $item) {
                    $item->setSubscriptionStatus($status);
                    $item->setUpdateDate($currentDate);
                    $item->save();
                }
            }
        }
    }

}
