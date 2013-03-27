<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 27.03.13
 * Time: 14:41
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Category_View extends Mage_Core_Block_Template
{
    protected $_postCollection;

    protected function _getPostCollection()
    {
        if(is_null($this->_postCollection)) {
            $this->_postCollection = Mage::getModel('blog/post')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId());
        }
        return $this->_postCollection;
    }

    public function getLoadedPostCollection()
    {
        return $this->_getPostCollection();
    }

}