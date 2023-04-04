<?php

namespace Dibs\EasyCheckout\Model\Source;

class Frequency extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource 
{
    public function getAllOptions() {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('--Select--'), 'value' => ''],
                ['label' => __('Every'), 'value' => 'Every'],
                ['label' => __('Every 2nd'), 'value' => 'Every 2nd'],
        				['label' => __('Every 3rd'), 'value' => 'Every 3rd'],
        				['label' => __('Every 4th'), 'value' => 'Every 4th'],
        				['label' => __('Every 5th'), 'value' => 'Every 5th'],
        				['label' => __('Every 6th'), 'value' => 'Every 6th']
            ];
        }
        return $this->_options;
    }
}