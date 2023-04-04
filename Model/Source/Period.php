<?php

namespace Dibs\EasyCheckout\Model\Source;

class Period extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource 
{
    public function getAllOptions() {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('--Select--'), 'value' => ''],
                ['label' => __('Day'), 'value' => 'Day'],
                ['label' => __('Week'), 'value' => 'Week'],
                ['label' => __('Month'), 'value' => 'Month'],
                ['label' => __('Quarter'), 'value' => 'Quarter'],
                ['label' => __('Year'), 'value' => 'Year']
            ];
        }
        return $this->_options;
    }
}