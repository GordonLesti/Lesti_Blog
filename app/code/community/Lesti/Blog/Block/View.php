<?php

class Lesti_Blog_Block_View extends Mage_Core_Block_Template
{
    const XML_PATH_BLOG_TITLE = 'blog/general/title';
    
    public function getTitle() {
        return Mage::getStoreConfig(self::XML_PATH_BLOG_TITLE);
    }
}
