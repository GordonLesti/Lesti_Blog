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

    const XML_PATH_BLOG_TITLE = 'blog/general/title';
    const OBJECT_TYPE_CATEGORY = 'category';
    const OBJECT_TYPE_DEFAULT = 'default';
    const OBJECT_TYPE_AUTHOR = 'author';
    const OBJECT_TYPE_ARCHIV = 'archiv';
    const OBJECT_TYPE_TAG = 'tag';

    protected function _getPostCollection()
    {
        if(is_null($this->_postCollection)) {
            $object = $this->getObject();
            if($object->getId()) {
                $this->_postCollection = $object->getPostCollection();
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