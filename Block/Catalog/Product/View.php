<?php
namespace Dibs\EasyCheckout\Block\Catalog\Product;
class View extends \Magento\Framework\View\Element\Template
{
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Framework\Registry $registry,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Directory\Model\Currency $currency,
    array $data = []
    )
	{
    $this->_storeManager = $storeManager;
    $this->_currency = $currency;  
    $this->_registry = $registry;
		parent::__construct($context, $data);
	}
	
   public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getCurrentCategory()
    {        
        return $this->_registry->registry('current_category');
    }
    
    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }
    
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }  
    
    public function getDisplayCurrencyCode()
    {
        return $this->_storeManager->getStore()->getDisplayCurrencyCode();
    }
}