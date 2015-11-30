<?php

class Lesti_Blog_Block_Widget_Excerpt
extends Mage_Core_Block_Abstract
implements Mage_Widget_Block_Interface 
{
    protected function _toHtml() {
        return '<!--more-->';
    }
}
