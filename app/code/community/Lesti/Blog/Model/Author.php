<?php

class Lesti_Blog_Model_Author extends Mage_Core_Model_Abstract
{

    const CACHE_TAG              = 'blog_author';
    protected $_cacheTag         = 'blog_author';
    protected $_postCollection;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'blog_author';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/author');
    }

    public function getPostCollection()
    {
        if(is_null($this->_postCollection)) {
            $this->_postCollection = Mage::getModel('blog/post')->getCollection()
                ->addFieldToFilter('main_table.author_id', $this->getId());
        }
        return $this->_postCollection;
    }

    /**
     * Check if author name exist for specific store
     * return author name if author exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkAuthorName($authorname, $storeId)
    {
        return $this->_getResource()->checkAuthorName($authorname, $storeId);
    }

}
