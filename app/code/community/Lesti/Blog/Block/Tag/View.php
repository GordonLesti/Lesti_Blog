<?php

class Lesti_Blog_Block_Tag_View extends Mage_Core_Block_Template
{
    /**
     * @return string|null
     */
    public function getTitle() {
        if ($this->getTag()) {
            return Mage::helper('blog')->__('Posts tagged with "%s"', $this->getTag()->getTitle());
        }
        return null;
    }
}
