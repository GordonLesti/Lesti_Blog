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

    protected $_post;

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

    public function getPost()
    {
        if(is_null($this->_post)) {
            $this->_post = Mage::getModel('blog/post')->load($this->getPostId());
        }
        return $this->_post;
    }

    public function getPostTitle()
    {
        return $this->getPost()->getTitle();
    }

    public function setPost($post)
    {
        $this->_post = $post;
    }

    public function getCommentUrl()
    {
        return Mage::app()->getStore()->getUrl(
            Mage::getStoreConfig(Lesti_Blog_Model_Post::XML_PATH_BLOG_GENERAL_ROUTER)) .
            $this->getPost()->getIdentifier() . '/#comment-' . $this->getId();
    }

    public function getContent()
    {
        $content = Mage::helper('blog/post_comment')->purifyHtml($this->getData('content'));
        return nl2br($content);
    }

}