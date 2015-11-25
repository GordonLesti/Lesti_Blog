<?php

class Lesti_Blog_Block_Archive_View extends Mage_Core_Block_Template
{
    /**
     * @return string|null
     */
    public function getTitle() {
        if ($this->getArchive()) {
            return Mage::helper('blog')->__('Posts from %s', $this->getArchive()->getTitle());
        }
        return null;
    }
}
