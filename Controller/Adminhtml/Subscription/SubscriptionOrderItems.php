<?php
namespace Dibs\EasyCheckout\Controller\Adminhtml\Subscription;

use Dibs\EasyCheckout\Model\Client\DTO\RefundPayment;
use Dibs\EasyCheckout\Model\Client\DTO\Payment\OrderItem;
use Dibs\EasyCheckout\Model\Dibs\Items;

class SubscriptionOrderItems extends \Magento\Backend\App\Action
{

    /** @var \Dibs\EasyCheckout\Model\Dibs\SubscriptionCharge $subscriptionHandler */
    protected $subscriptionHandler;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Dibs\EasyCheckout\Model\Dibs\SubscriptionCharge $subscriptionHandler,
        \Dibs\EasyCheckout\Model\Client\Api\Payment $payment,
        \Magento\Sales\Model\Spi\OrderResourceInterface $orderResource,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory,
        Items $items
    )
    {
        parent::__construct($context);
        $this->resultFactory = $resultJsonFactory;        
        $this->subscriptionHandler = $subscriptionHandler;
        $this->payment = $payment;
        $this->orderResource = $orderResource;
        $this->orderFactory = $orderFactory;
        $this->items = $items;
    }
    public function execute(){
        
        $params = $this->getRequest()->getParams();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $incrId = $params['id'];
        $collection = $objectManager->create('Magento\Sales\Model\Order'); 
        $orderInfo = $collection->loadByIncrementId($incrId);
        $storeId = $orderInfo->getStore()->getId();
        $result = $this->payment->getPayment($params['payment_id'], $storeId);
        //print_r($result->orderItem);
        //echo '-----------------------------------------------------------------------------------------------';
        //print_r($result->getOrderItem());
        //die;
        if(!empty($result->orderItem)){
          //print_r($result->getOrderItem());
          $rowCount = count($result->orderItem);
          $rowItem = array();
          foreach($result->orderItem as $item){
            if($item['reference'] == "shipping_fee"){
              $rowItem['shipping']['name'] = $item['name'];
              $rowItem['shipping']['total'] = $item['grossTotalAmount'];
            } else{
              $rowItem['orderItem']['type'] = 'Product';
              $rowItem['orderItem']['name'] = $item['name'];
              $rowItem['orderItem']['qty'] = $item['quantity'];
              $rowItem['orderItem']['total'] = $item['grossTotalAmount'];
            }
            //$rowItem[$item->getReference()] = $item->getReference();
            //print_r($rowItem);
            //die;
          }
          //die;
          $resultJson = $this->resultFactory->create();
          return $resultJson->setData([
              'status' => 'Success',
              'row' => $rowItem,
              'rowCount' => $rowCount
          ]);
        } else{
          $resultJson = $this->resultFactory->create();
          return $resultJson->setData([
              'status' => 'Failed.'
          ]);
        }
    }
}