<?php

namespace Dibs\EasyCheckout\Model\Source;

class SubscriptionExpire extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource 
{
    public function getAllOptions() {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('--Select--'), 'value' => ''],
                ['label' => __('2 periods'), 'value' => '2'],
                ['label' => __('3 periods'), 'value' => '3'],
                ['label' => __('4 periods'), 'value' => '4'],
                ['label' => __('5 periods'), 'value' => '5'],
                ['label' => __('6 periods'), 'value' => '6'],
                ['label' => __('12 periods'), 'value' => '12'],
                ['label' => __('15 periods'), 'value' => '15'],
                ['label' => __('all time'), 'value' => 'all time']
            ];
        }
        return $this->_options;
    }
}