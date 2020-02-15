<?php

namespace Vuravel\Form\Components;

use Vuravel\Form\Field;

class Date extends Field
{
    public $component = 'Date';

    protected function vlInitialize($label)
    {
        parent::vlInitialize($label);

        if(config('vuravel.default_date_format'))
    	   $this->dateFormat(config('vuravel.default_date_format'));
    }

    /**
     * Sets a FlatPickr accepted date format. By default, it's 'Y-m-d'.
     *
     * @param      string|null  $dateFormat  The date format
     *
     * @return     self   
     */
    public function dateFormat($dateFormat = 'Y-m-d')
    {
    	$this->data(['dateFormat' => $dateFormat]);
    	return $this;
    }

}
