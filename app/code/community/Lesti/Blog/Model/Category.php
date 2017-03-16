<?php

class Lesti_Blog_Model_Category extends Mage_Core_Model_Abstract
{

    const CACHE_TAG              = 'blog_category';
    protected $_cacheTag         = 'blog_category';
    protected $_postCollection;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'blog_category';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/category');
    }

    public function getCategoryUrl()
    {
        $url = Mage::app()->getStore()->getUrl(
            Mage::getStoreConfig(Lesti_Blog_Model_Post::XML_PATH_BLOG_GENERAL_ROUTER)) .
            'category/' . $this->getIdentifier();
        return $url;
    }

    public function getPostCollection()
    {
        if(is_null($this->_postCollection)) {
            $this->_postCollection = Mage::getModel('blog/post')->getCollection()
                ->addCategoryFilter($this->getId());
        }
        return $this->_postCollection;
    }

    /**
     * Check if category identifier exist for specific store
     * return category id if category exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

}
