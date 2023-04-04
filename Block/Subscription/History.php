<?php

namespace Dibs\EasyCheckout\Block\Subscription;

use \Magento\Framework\App\ObjectManager;
use \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData\CollectionFactoryInterface as CollectionFactoryInterface;

/**
 * Nets subscription order history block
 *
 */
class History extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Dibs_EasyCheckout::order/history.phtml';

    /**
     * @var \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData\CollectionFactory
     */
    protected $_subscriptionCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

 
    /**
     * @var \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData\Collection
     */
    protected $subscriptions;

    /**
     * @var \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData\CollectionFactoryInterface
     */
    //private $subscriptionCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData\CollectionFactory $subscriptionCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Dibs\EasyCheckout\Model\ResourceModel\NetsSubscriptionData\CollectionFactory $subscriptionCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_subscriptionCollectionFactory = $subscriptionCollectionFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Nets Subscription Orders'));
    }

    /**
     * Provide nets subscription order collection factory
     *
     * @return CollectionFactoryInterface
     */
    /*private function getOrderCollectionFactory()
    {
        if ($this->subscriptionCollectionFactory === null) {
            $this->subscriptionCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->subscriptionCollectionFactory;
    }*/

    /**
     * Get customer orders
     *
     */
    public function getOrders()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->subscriptions) {
            $this->subscriptions = $this->_subscriptionCollectionFactory->create($customerId)->addFieldToSelect(
                '*'
            )->setOrder(
                'created_date',
                'desc'
            );
        }
        return $this->subscriptions;
    }
    
    public function getStatus($status) {
        if ($status == 1) {
            $status = 'Active';
        } else if ($status == 2){
            $status = 'Closed';
        } else {
            $status = 'InActive';
        }
        return $status;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'sales.order.history.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            $this->getOrders()->load();
        }
        return $this;
    }

    /**
     * Get Pager child block output
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get order view URL
     *
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $order->getOrderEntityId()]);
    }

    /**
     * Get order track URL
     *
     * @param object $order
     * @return string
     * @deprecated 102.0.3 Action does not exist
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getTrackUrl($order)
    {
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        trigger_error('Method is deprecated', E_USER_DEPRECATED);
        return '';
    }

    /**
     * Get customer account URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    /**
     * Get message for no orders.
     *
     * @return \Magento\Framework\Phrase
     */
    public function getEmptyOrdersMessage()
    {
        return __('You have placed no subscription orders.');
    }
}
