<?php

namespace Dibs\EasyCheckout\Ui\Component\Listing\Grid\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class SubscriptionExpires extends \Magento\Ui\Component\Listing\Columns\Column {

    
     /** @var UrlInterface */
     protected $_urlBuilder;
 
    

      /**
     * @var string
     */
  

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory, 
        UrlInterface $urlBuilder,
        
        array $components = [],
        array $data = []
     
        
    ){
        $this->_urlBuilder = $urlBuilder;
      
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource) { 
        if (isset($dataSource['data']['items'])) {
            
            foreach ($dataSource['data']['items'] as & $items) {

                if($items['nets_subscription_specific_interval'] !== "all time") {
                     $items['nets_subscription_specific_interval'] = $items['nets_subscription_specific_interval'] . " period"; 
                }
            }

        }

        return $dataSource;
    }
}