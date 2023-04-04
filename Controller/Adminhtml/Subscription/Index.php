<?php

namespace Dibs\EasyCheckout\Controller\Adminhtml\Subscription;

use Dibs\EasyCheckout\Helper\Email as DibsEmailHelper;
use Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory;

class Index extends \Magento\Backend\App\Action {

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    protected $DibsEmailHelper;

    /**
     * @param DibsEmailtHelper $dibsEmailHelper
     */
    protected $_orderRepository;
    protected $subscriptionData;

    public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Sales\Model\Order $orderRepository,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
            \Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory $subscriptionData,
            DibsEmailHelper $dibsEmailHelper
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultJsonFactory;
        $this->_orderRepository = $orderRepository;
        $this->subscriptionData = $subscriptionData;
        $this->_emailHelper = $dibsEmailHelper;
    }

    public function execute() {

        $params = $this->getRequest()->getParams();

        $post = $this->subscriptionData->create()->getCollection()
                ->addFieldToFilter('nets_subscription_id', $params['subscription_id']);

        $postUpdate = $post->load();
        foreach ($postUpdate as $item) {
            $item->setStatus($params['subscription_status']);
            $item->save();
        }

        $subscriptionDataQuery = $this->subscriptionData->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter('nets_subscription_id', $params['subscription_id']);
        $fetchRow = $subscriptionDataQuery->getData();

        if ($fetchRow[0]['status'] == '0') {

            // Check if new order email notification enabled
            $PauseTemp = 'notification_subscription/pause/';
            $isEnableEmail = $this->_emailHelper->isEnabledNewOrder($PauseTemp);

            if ($isEnableEmail) {

                // Set current order details
                $orderId = $fetchRow[0]['order_entity_id'];
                $_order = $this->_orderRepository->load($orderId);

                // Set email config options
                // $store = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
                $from = $this->_emailHelper->fromEmailOrder($PauseTemp);
                $to = $_order->getCustomerEmail();
                $templateId = $this->_emailHelper->templeteIdOrder($PauseTemp);
                $subject = $this->_emailHelper->subjectOrder($PauseTemp);

                // Set email template variables
                $vars = [
                    'admin_subject' => $subject,
                    'cur_subscription_id' => "#" . $params['subscription_id'],
                    'sub_status' => 'Inactive subscription',
                    'Order_id' => "#" . $fetchRow[0]['order_id'],
                    'customer_name' => $_order->getCustomerName()
                ];

                $this->_emailHelper->sendEmail($from, $to, $templateId, $vars);
            }
        }

        if ($fetchRow[0]['status'] == '2') {

            $orderId = $fetchRow[0]['order_entity_id'];
            $_order = $this->_orderRepository->load($orderId); // it order is not order increment id
            $_order->setState('closed');
            $_order->setStatus('closed');
            $_order->save();

            // Check if new order email notification enabled
            $tempID = 'notification_subscription/cancel/';
            $isEnable = $this->_emailHelper->isEnabledNewOrder($tempID);

            if ($isEnable) {

                // Set email config options
                // $store = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
                $from = $this->_emailHelper->fromEmailOrder($tempID);
                $to = $_order->getCustomerEmail();
                $templateId = $this->_emailHelper->templeteIdOrder($tempID);
                $subject = $this->_emailHelper->subjectOrder($tempID);

                // Set email template variables
                $vars = [
                    'admin_subject' => $subject,
                    'subscription_id' => "#" . $params['subscription_id'],
                    'sub_status' => 'Deactivated subscription',
                    'Order_id' => "#" . $fetchRow[0]['order_id'],
                    'customer_name' => $_order->getCustomerName()
                ];

                $this->_emailHelper->sendEmail($from, $to, $templateId, $vars);
            }
        }


        try {
            $resultJson = $this->resultFactory->create();
            return $resultJson->setData([
                        'messages' => 'Successfully.',
                        'status' => '2',
                        'error' => false
            ]);
        } catch (ClientException $e) {
            // handle?
            throw $e;
        }
    }

}
