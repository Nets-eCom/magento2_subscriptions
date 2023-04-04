<?php
namespace Dibs\EasyCheckout\Controller\SubscriptionUpdate;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;


class SubscriptionUpdate extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{

    // protected $_mcfFactory;
    private $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context
        // McfFactory $mcfFactory
    ) {
        // $this->_mcfFactory = $mcfFactory;
        parent::__construct($context);
    }
    
    public function execute(){
            
        $post_data = $this->getRequest()->getPostValue();
         return  $post_data ;

    }
}