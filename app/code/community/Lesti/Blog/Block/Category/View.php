<?php

class Lesti_Blog_Block_Category_View extends Mage_Core_Block_Template
{
    /**
     * @return string|null
     */
    public function getTitle() {
        if ($this->getCategory()) {
            return Mage::helper('blog')->__('Posts in "%s"', $this->getCategory()->getTitle());
        }
        return null;
    }
}
