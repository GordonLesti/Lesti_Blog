<?php

class Lesti_Blog_Model_Tag extends Mage_Core_Model_Abstract
{

    protected $_postCollection;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'blog_tag';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/tag');
    }

    public function getTagUrl($params = array())
    {
        $url = Mage::app()->getStore()->getUrl(
            Mage::getStoreConfig(Lesti_Blog_Model_Post::XML_PATH_BLOG_GENERAL_ROUTER)) .
            'tag/' . $this->getIdentifier();
        return $url;
    }

    public function getPostCollection()
    {
        if(is_null($this->_postCollection)) {
            $this->_postCollection = Mage::getModel('blog/post')->getCollection()
                ->addTagFilter($this->getId());
        }
        return $this->_postCollection;
    }

    /**
     * Check if tag identifier exist for specific store
     * return tag id if tag exists
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
