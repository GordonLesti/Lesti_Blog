<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 04.04.13
 * Time: 11:56
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Post_Comment extends Mage_Core_Model_Abstract
{

    /**
     * Comment's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const CACHE_TAG              = 'blog_post_comment';
    protected $_cacheTag         = 'blog_post_comment';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'blog_post_comment';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/post_comment');
    }

    /**
     * Prepare comment's statuses.
     * Available event blog_post_comment_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_ENABLED => Mage::helper('blog')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('blog')->__('Disabled'),
        ));

        Mage::dispatchEvent('blog_post_comment_get_available_statuses', array('statuses' => $statuses));

        return $statuses->getData();
    }

}