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
    protected $_category;

    const XML_PATH_BLOG_TITLE = 'blog/general/title';

    public function getCategory()
    {
        if(is_null($this->_category)) {
            $this->_category = $this->_getCategory();
        }
        return $this->_category;
    }

    protected function _getCategory()
    {
        $category = Mage::registry('blog_category');
        if(is_null($category)) {
            $category = Mage::getModel('blog/category')
                ->setTitle(Mage::getStoreConfig(self::XML_PATH_BLOG_TITLE));
        }
        return $category;
    }

    protected function _getPostCollection()
    {
        if(is_null($this->_postCollection)) {
            $category = $this->getCategory();
            if($category->getId()) {
                $this->_postCollection = $category->getPostCollection();
            } else {
                $this->_postCollection = Mage::getModel('blog/post')->getCollection();
            }
            $this->_postCollection->addStoreFilter(Mage::app()->getStore()->getId())
                ->addAuthorToResult();
        }
        return $this->_postCollection;
    }

    public function getLoadedPostCollection()
    {
        return $this->_getPostCollection();
    }

}