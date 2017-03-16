<?php

class Lesti_Blog_Model_Post extends Mage_Core_Model_Abstract
{

    const XML_PATH_BLOG_GENERAL_ROUTER = 'blog/general/router';

    /**
     * Post's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const ALLOW_COMMENT = 1;
    const DISALLOW_COMMENT = 0;

    const CACHE_TAG              = 'blog_post';
    protected $_cacheTag         = 'blog_post';
    protected $_needReadMore     = false;
    protected $_commentCollection;
    protected $_categoryCollection;
    protected $_tagCollection;
    protected $_author;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'blog_post';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/post');
    }

    public function getPostUrl($params = array())
    {
        $url = Mage::app()->getStore()->getUrl(Mage::getStoreConfig(self::XML_PATH_BLOG_GENERAL_ROUTER)) .
            $this->getIdentifier();
        foreach($params as $param) {
            switch($param) {
                case 'more':
                    $url .= '/#more-' . $this->getId();
                    break;
                case 'comment':
                    $url .= '/#comments';
                    break;
                case 'response':
                    $url .= '/#post-response';
                    break;
            }
        }
        return $url;
    }

    public function getAuthorName()
    {
        if($this->getData('author_name')) {
            return $this->getData('author_name');
        }
        $author = $this->getAuthor();
        if($author->getId()) {
            return $author->getAuthorName();
        }
    }

    public function getAuthor()
    {
        if(is_null($this->_author)) {
            $this->_author = Mage::getModel('blog/author')->load($this->getAuthorId());
        }
        return $this->_author;
    }

    public function getCommentCollection()
    {
        if(is_null($this->_commentCollection)) {
            $this->_commentCollection = Mage::getModel('blog/post_comment')->getCollection()
                ->addFieldToFilter('post_id', $this->getId());
        }
        return $this->_commentCollection;
    }

    public function getCategoryIds()
    {
        return array_unique(explode(',', $this->getData('category_ids')));
    }

    public function getTagIds()
    {
        return array_unique(explode(',', $this->getData('tag_ids')));
    }

    public function getCategoryCollection($storeId = null)
    {
        if(is_null($this->_categoryCollection)) {
            $this->_categoryCollection = Mage::getModel('blog/category')->getCollection()
                ->addPostFilter($this->getId());
            if(!is_null($storeId)) {
                $this->_categoryCollection->addStoreFilter($storeId);
            }
        }
        return $this->_categoryCollection;
    }

    public function getTagCollection($storeId = null)
    {
        if(is_null($this->_tagCollection)) {
            $this->_tagCollection = Mage::getModel('blog/tag')->getCollection()
                ->addPostFilter($this->getId());
            if(!is_null($storeId)) {
                $this->_tagCollection->addStoreFilter($storeId);
            }
        }
        return $this->_tagCollection;
    }

    /**
     * Check if post identifier exist for specific store
     * return post id if post exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Prepare post's statuses.
     * Available event blog_post_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_ENABLED => Mage::helper('blog')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('blog')->__('Disabled'),
        ));

        Mage::dispatchEvent('blog_post_get_available_statuses', array('statuses' => $statuses));

        return $statuses->getData();
    }

    public function getAllowCommentStatuses()
    {
        $statuses = new Varien_Object(array(
            self::ALLOW_COMMENT => Mage::helper('blog')->__('Yes'),
            self::DISALLOW_COMMENT => Mage::helper('blog')->__('No'),
        ));
        return $statuses->getData();
    }

    public function contentHtml()
    {
        $helper = Mage::helper('cms');
        $processor = $helper->getPageTemplateProcessor();
        $html = $processor->filter($this->getContent());
        $count = 1;
        $html = str_replace('<!--more-->', '<span id="more-' . $this->getId() . '"></span>', $html, $count);
        return $html;
    }

    public function getExcerpt()
    {
        $processor = Mage::helper('cms')->getPageTemplateProcessor();
        $content = $processor->filter($this->getContent());
        $excerpt = explode('<!--more-->', $content);
        if (count($excerpt) > 1) {
            $this->_needReadMore = true;
        }
        return $excerpt[0];
    }

    public function needReadMore()
    {
        return $this->_needReadMore;
    }

    public function getMainImageFilePath() {
        if($this->getData('main_image')) {
            return  Mage::getBaseDir('media') .DS .  $this->getData('main_image');
        }
    }

    public function getMainImageUrl() {
        if($this->getData('main_image')) {
            return  Mage::getUrl('media') .  $this->getData('main_image');
        }
    }
}
