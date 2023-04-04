<?php

namespace Dibs\EasyCheckout\Model\Total;

class Fee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {

    /**
     * Collect grand total address amount
     * 
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null;

    /**
     * @var \Dibs\EasyCheckout\Helper\Data $helper
     */
    protected $helper;
    
    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator,
            \Dibs\EasyCheckout\Helper\Data $helper) {
        $this->quoteValidator = $quoteValidator;
        $this->helper = $helper;
    }

    public function collect(
            \Magento\Quote\Model\Quote $quote,
            \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
            \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $isSubscription = false;
        //$addSignUpFee = false;
        parent::collect($quote, $shippingAssignment, $total);
        if (!count($shippingAssignment->getItems())) {
            return $this;
        }
        if ($quote->getItemsCount() <= 1) {

            $products = $quote->getAllItems();
            foreach ($products as $product) {
                $isSubscription = $product->getProduct()->getData('enable_subscription');
                //$addSignUpFee = $product->getProduct()->getData('add_signup_fee');
                $signupFee = $product->getProduct()->getData('signup_fee');
                $feeName = $product->getProduct()->getData('signup_fee_name');
                $options = $product->getProduct()->getTypeInstance(true)->getOrderOptions($product->getProduct());
            }
        }
        
        if ($isSubscription && $this->helper->isSubscriptionEnabled() && !empty($signupFee)) {
            $additionalData = $options['info_buyRequest'];
            $exist_amount = 0;
            $balance = $signupFee - $exist_amount;
            $total->setTotalAmount($feeName, $balance);
            $total->setBaseTotalAmount($feeName, $balance);
            $total->setFee($balance);
            $total->setBaseFee($balance);
            $total->setGrandTotal($total->getGrandTotal());
            $total->setBaseGrandTotal($total->getBaseGrandTotal());
            return $this;
        }
    }

    protected function clearValues(Address\Total $total) {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * return array|null
     */

    /**
     * Assign subtotal amount and label to address object
     * 
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total) {
        $isSubscription = false;
        // $addSignUpFee = false;
        if ($quote->getItemsCount() <= 1) {
            $products = $quote->getAllItems();
            foreach ($products as $product) {
                $isSubscription = $product->getProduct()->getData('enable_subscription');
                //$addSignUpFee = $product->getProduct()->getData('add_signup_fee');
                $signupFee = $product->getProduct()->getData('signup_fee');
                $feeName = $product->getProduct()->getData('signup_fee_name');
                $options = $product->getProduct()->getTypeInstance(true)->getOrderOptions($product->getProduct());
            }
            if ($isSubscription && $this->helper->isSubscriptionEnabled() && !empty($signupFee)) {
                //if (isset($additionalData['nets-subscription'])) {
                //    if ($additionalData['nets-subscription'] == 'Subscription Order') {
                        return [
                            'code' => 'fee',
                            'title' => $feeName,
                            'value' => $signupFee
                        ];
                //    }
                //}
            }
        }
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel() {
        return __('Custom Fee test');
    }

}
