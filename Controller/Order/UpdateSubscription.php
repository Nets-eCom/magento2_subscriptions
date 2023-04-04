<?php

namespace Dibs\EasyCheckout\Controller\Order;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;

class UpdateSubscription extends AbstractAccount {

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    protected $_orderRepository;
    protected $subscriptionData;

    public function __construct(
            Context $context,
            \Magento\Sales\Model\Order $orderRepository,
            \Dibs\EasyCheckout\Model\NetsSubscriptionDataFactory $subscriptionData,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultJsonFactory;
        $this->subscriptionData = $subscriptionData;
        $this->_orderRepository = $orderRepository;
    }

    public function execute() {
        $data = $this->getRequest()->getParams();

        $subscriptionID = $data['SubscriptionID'];
        $status = $data['Status'];
        $resStatus = "Active";

        $subscriptionDataQuery = $this->subscriptionData->create()->getCollection()->addFieldToSelect('*')->addFieldToFilter('nets_subscription_id', $subscriptionID);
        $fetchRow = $subscriptionDataQuery->getData();
        $setValue = array('0', '1', '2');
        if (in_array($status, $setValue)) {
            if ($status == '2') {
                $orderId = $fetchRow[0]['order_entity_id'];
                $_order = $this->_orderRepository->load($orderId); // it order is not order increment id
                $_order->setState('closed');
                $_order->setStatus('closed');
                $_order->save();
            }

            $post = $this->subscriptionData->create()->getCollection()
                    ->addFieldToFilter('nets_subscription_id', $subscriptionID);

            $postUpdate = $post->load();
            foreach ($postUpdate as $item) {
                $item->setStatus($status);
                $item->save();
            }
            $resStatus = "Success";
        }

        $resultJson = $this->resultFactory->create();
        return $resultJson->setData([
                    'status' => $resStatus,
                    'error' => false,
                    'SubID' => $subscriptionID,
        ]);
    }

}

?>