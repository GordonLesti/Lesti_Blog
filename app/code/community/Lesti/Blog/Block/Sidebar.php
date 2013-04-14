<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 30.03.13
 * Time: 16:26
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Sidebar extends Mage_Core_Block_Template
{
    const XML_PATH_RECENT_POSTS_COUNT = 'blog/sidebar/recent_posts_count';
    const XML_PATH_RECENT_COMMENTS_COUNT = 'blog/sidebar/recent_comments_count';

    protected $_recentPosts;
    protected $_recentComments;
    protected $_categories;

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
            $url = Mage::app()->getStore()->getUrl(Mage::getStoreConfig(
                Lesti_Blog_Model_Post::XML_PATH_BLOG_GENERAL_ROUTER)) .
                date("Y", strtotime($post->getCreationTime())) . '/' .
                date("m", strtotime($post->getCreationTime()));
            $archiv['url'] = $url;
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
}