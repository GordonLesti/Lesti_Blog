<?php

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

    public function validate()
    {
        $errors = array();

        if (!Zend_Validate::is($this->getAuthorName(), 'NotEmpty')) {
            $errors[] = Mage::helper('blog')->__('Name can\'t be empty');
        }

        if (!Zend_Validate::is($this->getAuthorEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('blog')->__('No valid Email Address');
        }

        if (!Zend_Validate::is($this->getContent(), 'NotEmpty')) {
            $errors[] = Mage::helper('blog')->__('Content can\'t be empty');
        }

        if (!Zend_Validate::is($this->getParentId(), 'Int')) {
            $errors[] = Mage::helper('blog')->__('Parent doesn\'t exist.');
        }

        if (!Zend_Validate::is($this->getPostId(), 'Int')) {
            $errors[] = Mage::helper('blog')->__('Post doesn\'t exist.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

}
