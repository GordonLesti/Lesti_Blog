<?php

class Lesti_Blog_Block_Sidebar extends Mage_Core_Block_Template
{
    const XML_PATH_RECENT_POSTS_COUNT = 'blog/sidebar/recent_posts_count';
    const XML_PATH_RECENT_COMMENTS_COUNT = 'blog/sidebar/recent_comments_count';

    protected $_recentPosts;
    protected $_recentComments;
    protected $_categories;
    protected $_tags;
    protected $_max_tag_post;

    public function _construct()
    {
        $this->addData(array(
            'cache_lifetime'    => 3600
        ));
        return parent::_construct();
    }

    public function getRecentPosts()
    {
        if(is_null($this->_recentPosts)) {
            $count = (int) Mage::getStoreConfig(self::XML_PATH_RECENT_POSTS_COUNT);
            $this->_recentPosts = Mage::getModel('blog/post')->getCollection()
                ->addFieldToFilter('is_active', Lesti_Blog_Model_Post::STATUS_ENABLED)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->setOrder('creation_time')
                ->setPageSize($count);
        }
        return $this->_recentPosts;
    }

    public function getRecentComments()
    {
        if(is_null($this->_recentComments)) {
            $count = (int) Mage::getStoreConfig(self::XML_PATH_RECENT_COMMENTS_COUNT);
            $this->_recentComments = Mage::getModel('blog/post_comment')->getCollection()
                ->addFieldToFilter('status', Lesti_Blog_Model_Post_Comment::STATUS_ENABLED)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->setOrder('creation_time')
                ->setPageSize($count);
        }
        return $this->_recentComments;
    }

    public function getArchives()
    {
        $postCollection = Mage::getModel('blog/post')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addGroupByMonth();
        foreach($postCollection as $post) {
            $archiv = array();
            $archiv['creation_time'] = $post->getCreationTime();
            $archiv['url'] = Mage::helper('blog/archive')->getArchiveUrl($post->getCreationTime());
            $archives[] = $archiv;
        }
        return $archives;
    }

    public function getCategories()
    {
        if(is_null($this->_categories)) {
            $this->_categories = Mage::getModel('blog/category')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->setOrder('identifier');
        }
        return $this->_categories;
    }

    public function getTags()
    {
        if(is_null($this->_tags)) {
            $this->_tags = Mage::getModel('blog/tag')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addCountToResult();
            $this->_max_tag_post = 1;
            foreach($this->_tags as $_tag) {
                $this->_max_tag_post = max($this->_max_tag_post, $_tag->getCount());
            }
        }
        return $this->_tags;
    }

    public function getMaxTagPost() {
        return $this->_max_tag_post;
    }

    public function canShow($name) {
        return Mage::getStoreConfig('blog/sidebar/show_' . $name);
    }
}
