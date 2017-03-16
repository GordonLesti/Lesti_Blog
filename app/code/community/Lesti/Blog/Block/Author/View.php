<?php

class Lesti_Blog_Block_Author_View extends Mage_Core_Block_Template
{
    /**
     * @return string|null
     */
    public function getTitle() {
        if ($this->getAuthor()) {
            return Mage::helper('blog')->__('Posts by %s', $this->getAuthor()->getTitle());
        }
        return null;
    }
}
