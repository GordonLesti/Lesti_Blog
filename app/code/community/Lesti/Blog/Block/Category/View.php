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
    protected $_categoryCollection;
    protected $_tagCollection;

    const XML_PATH_BLOG_TITLE = 'blog/general/title';
    const OBJECT_TYPE_CATEGORY = 'category';
    const OBJECT_TYPE_DEFAULT = 'default';
    const OBJECT_TYPE_AUTHOR = 'author';
    const OBJECT_TYPE_ARCHIV = 'archiv';
    const OBJECT_TYPE_TAG = 'tag';

    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime'    => 3600
        ));
        return parent::_construct();
    }

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
                ->addAuthorToResult()
                ->addCategoryIdToResult()
                ->addTagIdToResult();
            $categoryIds = array();
            $tagIds = array();
            foreach($this->_postCollection as $post) {
                $categoryIds = array_merge($categoryIds, $post->getCategoryIds());
                $tagIds = array_merge($tagIds, $post->getTagIds());
            }
            // prepaire CategoryCollection
            $this->_categoryCollection = Mage::getModel('blog/category')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addFieldToFilter('category_id', array('in' => $categoryIds));
            // prepaire TagCollection
            $this->_tagCollection = Mage::getModel('blog/tag')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addFieldToFilter('tag_id', array('in' => $tagIds));
        }
        return $this->_postCollection;
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        $cacheKeyInfo[] = 'blog_category_view';
        $cacheKeyInfo[] = $this->getType();
        $cacheKeyInfo[] = $this->getObject()->getId();
        $cacheKeyInfo[] = Mage::getSingleton('customer/session')->isLoggedIn();
        return $cacheKeyInfo;
    }

    public function getCategory($categoryId)
    {
        if(!is_null($this->_categoryCollection)) {
            return $this->_categoryCollection->getItemById($categoryId);
        }
    }

    public function getTag($tagId)
    {
        if(!is_null($this->_tagCollection)) {
            return $this->_tagCollection->getItemById($tagId);
        }
    }

    public function getLoadedPostCollection()
    {
        return $this->_getPostCollection();
    }

}