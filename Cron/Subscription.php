<?php

namespace Dibs\EasyCheckout\Cron;

use Dibs\EasyCheckout\Helper\CronCreateOrder as DibsCrontHelper;
use Dibs\EasyCheckout\Model\Client\Api\Payment;
use Dibs\EasyCheckout\Model\Client\ClientException;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\OrderItem;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\CreatePaymentOrder;
use Dibs\EasyCheckout\Model\Client\DTO\CreateUnscheduledSubscriptionCharge;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\UnscheduledSubscriptionChargeOrder;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\CreatePaymentWebhook;
use Dibs\EasyCheckout\Model\Client\DTO\UpdatePaymentReference;
use Dibs\EasyCheckout\Model\Dibs\Items;
use Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory;
use Dibs\EasyCheckout\Model\NetsSubscriptionIntervalChargeFactory;

class Subscription {

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
    protected $subscriptionData;
    protected $subscriptionIntervalData;

    public function __construct(
            \Dibs\EasyCheckout\Model\Client\Api\Payment $paymentApi,
            \Dibs\EasyCheckout\Helper\Data $helper,
            \Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory $subscriptionData,
            \Dibs\EasyCheckout\Model\NetsSubscriptionIntervalChargeFactory $subscriptionIntervalData,
            DibsCrontHelper $dibsCrontHelper,
            Items $itemsHandler
    ) {
        $this->_cronHelper = $dibsCrontHelper;
        $this->helper = $helper;
        $this->subscriptionData = $subscriptionData;
        $this->subscriptionIntervalData = $subscriptionIntervalData;
        $this->paymentApi = $paymentApi;
        $this->items = $itemsHandler;
    }

    public function execute() {
        $netsSubscriptionValue = date('Y-m-d');
        $subscriptionDataQuery = $this->subscriptionData->create()->getCollection()->addFieldToSelect('*')
                ->addFieldToFilter('status', '1')
                ->addFieldToFilter('nets_subscription_enddate', array('gteq' => $netsSubscriptionValue))
                ->setOrder(
                'nets_subscription_enddate',
                'asc'
        );
        $result = $subscriptionDataQuery->load();

        $i = 1;
        foreach ($result as $subDetails) {

            $subsInterval = str_replace(' ', '_', $subDetails['nets_subscription_interval']);
            $specifcInterval = str_replace(' ', '_', $subDetails['nets_subscription_specific_interval']);
            $interval = $subsInterval . '_' . $specifcInterval;

            switch ($interval) {
                case "Every_Day_2":
                case "Every_Day_3":
                case "Every_Day_4":
                case "Every_Day_5":
                case "Every_Day_6":
                case "Every_Day_12":
                case "Every_Day_15":

                    $intervalCnt = $subDetails['intervalCount'];
                    $SpecificCnt = explode('_', $interval);

                    if ($SpecificCnt['2'] >= $intervalCnt) {
                        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info('condition1:- ' . $SpecificCnt['2'] . "--" . $intervalCnt);
                        $this->chargeSubscription($subDetails['order_entity_id']);
                    }

                    break;
                case "Every_Day_all_time":
                    $this->chargeSubscription($subDetails['order_entity_id']);
                    break;

                case "Every_2nd_Day_2":
                case "Every_2nd_Day_3":
                case "Every_2nd_Day_4":
                case "Every_2nd_Day_5":
                case "Every_2nd_Day_6":
                case "Every_2nd_Day_12":
                case "Every_2nd_Day_15":

                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 2;
                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);

                    break;

                case "Every_2nd_Day_all_time":

                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 2;
                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);

                    break;

                case "Every_3rd_Day_2":
                case "Every_3rd_Day_3":
                case "Every_3rd_Day_4":
                case "Every_3rd_Day_5":
                case "Every_3rd_Day_6":
                case "Every_3rd_Day_12":
                case "Every_3rd_Day_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 3;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_3nd_Day_all_time":

                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 3;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_4th_Day_2":
                case "Every_4th_Day_3":
                case "Every_4th_Day_4":
                case "Every_4th_Day_5":
                case "Every_4th_Day_6":
                case "Every_4th_Day_12":
                case "Every_4th_Day_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 4;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];
                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_4th_Day_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 4;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_5th_Day_2":
                case "Every_5th_Day_3":
                case "Every_5th_Day_4":
                case "Every_5th_Day_5":
                case "Every_5th_Day_6":
                case "Every_5th_Day_12":
                case "Every_5th_Day_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 5;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_5th_Day_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 5;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_6th_Day_2":
                case "Every_6th_Day_3":
                case "Every_6th_Day_4":
                case "Every_6th_Day_5":
                case "Every_6th_Day_6":
                case "Every_6th_Day_12":
                case "Every_6th_Day_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 6;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_6nd_Day_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 6;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                /*
                  FOR WEEKLY CONDITION
                 */

                case "Every_week_2":
                case "Every_week_3":
                case "Every_week_4":
                case "Every_week_5":
                case "Every_week_6":
                case "Every_week_12":
                case "Every_week_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 7;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_week_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 7;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_2nd_week_2":
                case "Every_2nd_week_3":
                case "Every_2nd_week_4":
                case "Every_2nd_week_5":
                case "Every_2nd_week_6":
                case "Every_2nd_week_12":
                case "Every_2nd_week_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 14;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_2nd_week_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 14;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_3rd_week_2":
                case "Every_3rd_week_3":
                case "Every_3rd_week_4":
                case "Every_3rd_week_5":
                case "Every_3rd_week_6":
                case "Every_3rd_week_12":
                case "Every_3rd_week_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 21;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_3rd_week_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 21;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_4th_week_2":
                case "Every_4th_week_3":
                case "Every_4th_week_4":
                case "Every_4th_week_5":
                case "Every_4th_week_6":
                case "Every_4th_week_12":
                case "Every_4th_week_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 28;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);

                    break;
                case "Every_4th_week_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 28;
                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_5th_week_2":
                case "Every_5th_week_3":
                case "Every_5th_week_4":
                case "Every_5th_week_5":
                case "Every_5th_week_6":
                case "Every_5th_week_12":
                case "Every_5th_week_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 35;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_5th_week_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 35;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_6th_week_2":
                case "Every_6th_week_3":
                case "Every_6th_week_4":
                case "Every_6th_week_5":
                case "Every_6th_week_6":
                case "Every_6th_week_12":
                case "Every_6th_week_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 42;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_6th_Day_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 42;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                /*
                  FOR MONTHLY CONDITION
                 */

                case "Every_Monthly_2":
                case "Every_Monthly_3":
                case "Every_Monthly_4":
                case "Every_Monthly_5":
                case "Every_Monthly_6":
                case "Every_Monthly_12":
                case "Every_Monthly_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 30;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['2'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_monthly_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 30;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['2'];
                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_2nd_Monthly_2":
                case "Every_2nd_Monthly_3":
                case "Every_2nd_Monthly_4":
                case "Every_2nd_Monthly_5":
                case "Every_2nd_Monthly_6":
                case "Every_2nd_Monthly_12":
                case "Every_2nd_Monthly_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 60;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_2nd_Monthly_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 60;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];
                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_3rd_Monthly_2":
                case "Every_3rd_Monthly_3":
                case "Every_3rd_Monthly_4":
                case "Every_3rd_Monthly_5":
                case "Every_3rd_Monthly_6":
                case "Every_3rd_Monthly_12":
                case "Every_3rd_Monthly_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 90;
                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_3rd_Monthly_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 90;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_4th_Monthly_2":
                case "Every_4th_Monthly_3":
                case "Every_4th_Monthly_4":
                case "Every_4th_Monthly_5":
                case "Every_4th_Monthly_6":
                case "Every_4th_Monthly_12":
                case "Every_4th_Monthly_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 120;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];
                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_4th_Monthly_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 120;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];
                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_5th_Monthly_2":
                case "Every_5th_Monthly_3":
                case "Every_5th_Monthly_4":
                case "Every_5th_Monthly_5":
                case "Every_5th_Monthly_6":
                case "Every_5th_Monthly_12":
                case "Every_5th_Monthly_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 150;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];
                    $this->frequencyCharge($interval, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_5th_Monthly_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 150;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_6th_Monthly_2":
                case "Every_6th_Monthly_3":
                case "Every_6th_Monthly_4":
                case "Every_6th_Monthly_5":
                case "Every_6th_Monthly_6":
                case "Every_6th_Monthly_12":
                case "Every_6th_Monthly_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 180;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_6th_Monthly_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 180;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                /*
                  FOR QUARTER CONDITION
                 */
                case "Every_quarter_2":
                case "Every_quarter_3":
                case "Every_quarter_4":
                case "Every_quarter_5":
                case "Every_quarter_6":
                case "Every_quarter_12":
                case "Every_quarter_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 90;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['2'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_quarter_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 90;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['2'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_2nd_quarter_2":
                case "Every_2nd_quarter_3":
                case "Every_2nd_quarter_4":
                case "Every_2nd_quarter_5":
                case "Every_2nd_quarter_6":
                case "Every_2nd_quarter_12":
                case "Every_2nd_quarter_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 180;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_2nd_quarter_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 180;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_3rd_quarter_2":
                case "Every_3rd_quarter_3":
                case "Every_3rd_quarter_4":
                case "Every_3rd_quarter_5":
                case "Every_3rd_quarter_6":
                case "Every_3rd_quarter_12":
                case "Every_3rd_quarter_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 270;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_3rd_quarter_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 270;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);

                    break;

                case "Every_4th_quarter_2":
                case "Every_4th_quarter_3":
                case "Every_4th_quarter_4":
                case "Every_4th_quarter_5":
                case "Every_4th_quarter_6":
                case "Every_4th_quarter_12":
                case "Every_4th_quarter_15":

                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 360;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);

                    break;
                case "Every_4th_Monthly_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 360;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_5th_quarter_2":
                case "Every_5th_quarter_3":
                case "Every_5th_quarter_4":
                case "Every_5th_quarter_5":
                case "Every_5th_quarter_6":
                case "Every_5th_quarter_12":
                case "Every_5th_quarter_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 450;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);

                    break;
                case "Every_5th_quarter_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 450;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_6th_quarter_2":
                case "Every_6th_quarter_3":
                case "Every_6th_quarter_4":
                case "Every_6th_quarter_5":
                case "Every_6th_quarter_6":
                case "Every_6th_quarter_12":
                case "Every_6th_quarter_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 540;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);

                    break;
                case "Every_6th_quarter_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 540;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];
                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                /*
                  FOR YEAR CONDITION
                 */

                case "Every_year_2":
                case "Every_year_3":
                case "Every_year_4":
                case "Every_year_5":
                case "Every_year_6":
                case "Every_year_12":
                case "Every_year_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 365;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['2'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_year_all_Time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 365;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['2'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_2nd_year_2":
                case "Every_2nd_year_3":
                case "Every_2nd_year_4":
                case "Every_2nd_year_5":
                case "Every_2nd_year_6":
                case "Every_2nd_year_12":
                case "Every_2nd_year_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 730;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_2nd_year_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 730;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_3rd_year_2":
                case "Every_3rd_year_3":
                case "Every_3rd_year_4":
                case "Every_3rd_year_5":
                case "Every_3rd_year_6":
                case "Every_3rd_year_12":
                case "Every_3rd_year_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 1095;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_3rd_year_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 1095;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);

                    break;

                case "Every_4th_year_2":
                case "Every_4th_year_3":
                case "Every_4th_year_4":
                case "Every_4th_year_5":
                case "Every_4th_year_6":
                case "Every_4th_year_12":
                case "Every_4th_year_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 1460;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);

                    break;
                case "Every_4th_year_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 1460;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_5th_year_2":
                case "Every_5th_year_3":
                case "Every_5th_year_4":
                case "Every_5th_year_5":
                case "Every_5th_year_6":
                case "Every_5th_year_12":
                case "Every_5th_year_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 1825;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_5th_year_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 1825;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;

                case "Every_6th_year_2":
                case "Every_6th_year_3":
                case "Every_6th_year_4":
                case "Every_6th_year_5":
                case "Every_6th_year_6":
                case "Every_6th_year_12":
                case "Every_6th_year_15":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 2190;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 0);
                    break;
                case "Every_6th_year_all_time":
                    $intValCnt = $subDetails['intervalCount'];
                    $subCreateDate = $subDetails['created_date'];
                    $orderId = $subDetails['order_entity_id'];
                    $frquency = 2190;

                    $SpecificCnt = explode('_', $interval);
                    $intervalSpe = $SpecificCnt['3'];

                    $this->frequencyCharge($intervalSpe, $intValCnt, $subCreateDate, $orderId, $frquency, $allTime = 1);
                    break;
            }
            $i++;
        }
    }

    public function frequencyCharge($interval, $intervalCnt, $subcriptionCreateDate, $orderEntityId, $frqCnt, $allTime) {

        $subcriptionCreateDate = date('Y-m-d', strtotime($subcriptionCreateDate));
        if ($interval >= $intervalCnt && $allTime == 0) {

            if ($intervalCnt == '0') {
                $intervalCnt = 1;
            } else if ($intervalCnt == '1') {
                $intervalCnt = 2;
            }
            $days = $intervalCnt * $frqCnt;
            $addDates = date('Y-m-d', strtotime($subcriptionCreateDate . ' +' . $days . 'days'));

            if (date("Y-m-d") == $addDates) {
                $this->chargeSubscription($orderEntityId);
            }
        } else {

            if ($intervalCnt == '0') {
                $intervalCnt = 1;
            } else if ($intervalCnt == '1') {
                $intervalCnt = 2;
            }
            $days = $intervalCnt * $frqCnt;
            $addDates = date('Y-m-d', strtotime($subcriptionCreateDate . ' +' . $days . 'days'));

            if (date("Y-m-d") == $addDates) {
                $this->chargeSubscription($orderEntityId);
            }
        }
    }

    public function chargeSubscription($orderId) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('Magento\Sales\Api\Data\OrderInterface')->load($orderId);
        $orders = $objectManager->create('\Magento\Sales\Model\OrderRepository')->get($orderId);
        $items = $this->items->fromOrder($orders);
        $getNetsAmt = $order->getBaseGrandTotal();

        if ($order->getNetsSubscriptionSignupFee() > 0) {
            $getNetsAmt = $order->getBaseGrandTotal() - $order->getNetsSubscriptionSignupFee();
        }

        $paymentOrder = new CreatePaymentOrder();
        $paymentOrder->setCurrency($order->getOrderCurrencyCode());
        $paymentOrder->setReference($order->getIncrementId()); // change increment id from sales_order table
        $paymentOrder->setAmount((int) round($getNetsAmt * 100, 0)); // amount total from sales_order
        $paymentOrder->setItems($items); //set items from sales_order

        $webhookReservationCreated = new CreatePaymentWebhook();
        $webhookReservationCreated->setEventName(CreatePaymentWebhook::EVENT_PAYMENT_RESERVATION_CREATED);
        $webHookUrl = $this->helper->getWebHookCallbackUrl($webhookReservationCreated->getControllerName());
        $webhookReservationCreated->setUrl($webHookUrl);
        $webhooks = [$webhookReservationCreated];

        $webhookCheckoutCompleted = new CreatePaymentWebhook();
        $webhookCheckoutCompleted->setEventName(CreatePaymentWebhook::EVENT_PAYMENT_CHECKOUT_COMPLETED);
        $webHookCheckoutUrl = $this->helper->getWebHookCallbackUrl($webhookCheckoutCompleted->getControllerName());
        $webhookCheckoutCompleted->setUrl($webHookCheckoutUrl);
        $webhooks[] = $webhookCheckoutCompleted;

        //EVENT_PAYMENT_CREATED
        $webhookCheckoutCreated = new CreatePaymentWebhook();
        $webhookCheckoutCreated->setEventName(CreatePaymentWebhook::EVENT_PAYMENT_CREATED);
        $webHookCreatedUrl = $this->helper->getWebHookCallbackUrl($webhookCheckoutCreated->getControllerName());
        $webhookCheckoutCreated->setUrl($webHookCreatedUrl);
        $webhooks[] = $webhookCheckoutCreated;

        //EVENT_PAYMENT_CHARGE_CREATED
        $webhookChargeCreated = new CreatePaymentWebhook();
        $webhookChargeCreated->setEventName(CreatePaymentWebhook::EVENT_PAYMENT_CHARGE_CREATED);
        $webHookChargeUrl = $this->helper->getWebHookCallbackUrl($webhookChargeCreated->getControllerName());
        $webhookChargeCreated->setUrl($webHookChargeUrl);
        $webhooks[] = $webhookChargeCreated;

        //EVENT_PAYMENT_REFUND_INITIATED
        $webhookRefundInit = new CreatePaymentWebhook();
        $webhookRefundInit->setEventName(CreatePaymentWebhook::EVENT_PAYMENT_REFUND_INITIATED);
        $webHookRefundInitUrl = $this->helper->getWebHookCallbackUrl($webhookRefundInit->getControllerName());
        $webhookRefundInit->setUrl($webHookRefundInitUrl);
        $webhooks[] = $webhookRefundInit;

        //EVENT_PAYMENT_REFUND_COMPLETED
        $webhookRefundCompleted = new CreatePaymentWebhook();
        $webhookRefundCompleted->setEventName(CreatePaymentWebhook::EVENT_PAYMENT_REFUND_COMPLETED);
        $webHookRefundUrl = $this->helper->getWebHookCallbackUrl($webhookRefundCompleted->getControllerName());
        $webhookRefundCompleted->setUrl($webHookRefundUrl);
        $webhooks[] = $webhookRefundCompleted;

        //EVENT_PAYMENT_CANCEL_CREATED
        $webhookCancelCreated = new CreatePaymentWebhook();
        $webhookCancelCreated->setEventName(CreatePaymentWebhook::EVENT_PAYMENT_CANCEL_CREATED);
        $webHookCancelUrl = $this->helper->getWebHookCallbackUrl($webhookCancelCreated->getControllerName());
        $webhookCancelCreated->setUrl($webHookCancelUrl);
        $webhooks[] = $webhookCancelCreated;

        foreach ($webhooks as $webhook) {
            $webhook->setAuthorization($this->helper->getWebhookSecret());
        }
        if (!empty($order->getNetsSubscriptionId())) {

            $subscriptionChargeData = [];
            $subscriptionCharge = new UnscheduledSubscriptionChargeOrder();
            $subscriptionCharge->setUnscheduledSubscriptionId($order->getNetsSubscriptionId()); //set subscription id from sales_ordr
            $subscriptionCharge->setOrder($paymentOrder);
            $subscriptionChargeData[] = $subscriptionCharge;

            $createSubPaymentRequest = new CreateUnscheduledSubscriptionCharge();
            $createSubPaymentRequest->setExternalBulkChargeId(rand(10000, 99999));
            $createSubPaymentRequest->setUnscheduledSubscriptionIds($subscriptionChargeData);
            $createSubPaymentRequest->setWebHooks($webhooks);
            $chargeResponse = $this->paymentApi->chargeUnscheduledSubscription($createSubPaymentRequest);

            if (!empty($chargeResponse) && isset($chargeResponse)) {

                $bulkId = $chargeResponse->getBulkId();
                $response = $this->paymentApi->getChargeUnscheduledSubscription($bulkId);

                foreach ($response->getUnscheduledSubscriptionItems() as $data) {
                    $sub_id = $data['unscheduledSubscriptionId'];
                    $payment_id = $data['paymentId'];
                    $charge_id = $data['chargeId'];
                    $status = $data['status'];

                    if ($status == 'Succeeded' || $status == 'Pending') {

                        $subscriptionQuery = $this->subscriptionData->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter('nets_subscription_id', $sub_id);
                        $rowResult = $subscriptionQuery->getData();
                        
                        $currentDate = date("Y-m-d");
                        $updateCnt = $rowResult[0]['intervalCount'] + 1;
                        $orderID = $rowResult[0]['order_entity_id'];
                        $payID = $payment_id;

                        //Update subscription charge interval count here
                        $postSubscription = $this->subscriptionData->create()->getCollection()
                                ->addFieldToFilter('nets_subscription_id', $sub_id);

                        $postSubscriptionUpdate = $postSubscription->load();
                        foreach ($postSubscriptionUpdate as $itemUpdate) {
                            $itemUpdate->setIntervalCount($updateCnt);
                            $itemUpdate->setChargeDate($currentDate);
                            $itemUpdate->save();
                        }

                        //Create magento child order here post charge transaction
                        $childOrderID = $this->_cronHelper->createMageOrder($orderID, $payID);
                    }

                    $subscriptionIntervalModel = $this->subscriptionIntervalData->create();
                    $subscriptionIntervalModel->addData([
                        "OrderID" => $order->getIncrementId(),
                        "SubscriptinID" => $sub_id,
                        "PaymentID" => $payment_id,
                        "ChargeID" => $charge_id,
                        "SubStatus" => $status,
                        "BulkId" => $bulkId,
                        "ChildOrderId" => $childOrderID
                    ]);
                    $subscriptionIntervalModel->save();
                }
            }
        }
    }

}
