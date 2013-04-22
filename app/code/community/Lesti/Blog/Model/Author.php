<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 17.04.13
 * Time: 17:34
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Author extends Mage_Core_Model_Abstract
{

    const CACHE_TAG              = 'blog_author';
    protected $_cacheTag         = 'blog_author';

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