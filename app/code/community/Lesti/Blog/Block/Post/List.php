<?php

class Lesti_Blog_Block_Post_List extends Mage_Core_Block_Template
{
    const DEFAULT_POST_BLOCK_CLASS = 'blog/post_list_item';
    const DEFAULT_POST_TEMPLATE = 'blog/post/list/item.phtml';
    const DEFAULT_POST_LIMIT = 4;
    
    protected $_posts;
    protected $_categories;
    protected $_categoriesCache;
    protected $_tags;
    protected $_tagsCache;
    protected $_postBlocks = array();
    
    protected function _beforeToHtml() {
        $pager = $this->_getPager();
        foreach ($this->getPosts() as $post) {
            $this->addPost($post);
        }
        
        if ($pager) {
            $pager->setCollection($this->getPosts());
        }
        
        return $this;
    }
    
    /**
     * @return string Magento factory string like 'blog/post_list_item'
     * @see self::DEFAULT_POST_BLOCK_CLASS
     */
    public function getPostBlockClass() {
        if (is_string($this->getData('post_block_class'))) {
            return $this->getData('post_block_class');
        }
        return self::DEFAULT_POST_BLOCK_CLASS;
    }
    
    /**
     * @return string
     * @see self::DEFAULT_POST_TEMPLATE
     */
    public function getPostTemplate() {
        if (is_string($this->getData('post_template'))) {
            return $this->getData('post_template');
        }
        return self::DEFAULT_POST_TEMPLATE;
    }
    
    /**
     * @return mixed|null depends on the value of $this->getPostBlockClass()
     * @param Lesti_Blog_Model_Post|null $post
     * @see self::DEFAULT_POST_BLOCK_CLASS
     * @see $this->getPostBlockClass()
     */
    public function getPostBlock(Lesti_Blog_Model_Post $post = null) {
        $postBlock = Mage::getSingleton('core/layout')
            ->createBlock($this->getPostBlockClass());
        if ($postBlock) {
            $postBlock->setTemplate($this->getPostTemplate());
            if ($post) {
                $postBlock->setPost($post);
            }
        }
        return $postBlock;
    }
    
    /**
     * @param Lesti_Blog_Model_Post $post
     * @return Lesti_Blog_Block_Post_List
     */
    public function addPost(Lesti_Blog_Model_Post $post) {
        $postBlock = $this->getPostBlock($post);
        if ($postBlock) {
            $this->_postBlocks[] = $postBlock;
            $this->setChild("post-{$post->getId()}", $postBlock);
        }
    }
    
    public function getPostsHtml() {
        $html = '';
        foreach ($this->_postBlocks as $block) {
            $html .= $block->toHtml();
        }
        return $html;
    }
    
    /**
     * @return Lesti_Blog_Model_Resource_Tag_Collection
     */
    public function getTags() {
        if (! $this->_tags) {
            $this->_tags = Mage::getModel('blog/tag')->getCollection();
            $this->_tags->addStoreFilter(Mage::app()->getStore()->getId());
        }
        return $this->_tags;
    }
    
    /**
     * @return array an array of each tag object indexed by their IDs. Used to
     *     facilitate generating links for posts in the list.
     */
    public function getTagsCache() {
        if (! $this->_tagsCache) {
            $this->_tagsCache = array();
            foreach ($this->getTags() as $tag) {
                $this->_tagsCache[$tag->getId()] = $tag;
            }
        }
        return $this->_tagsCache;
    }
    
    /**
     * @return Lesti_Blog_Model_Resource_Category_Collection
     */
    public function getCategories() {
        if (! $this->_categories) {
            $this->_categories = Mage::getModel('blog/category')->getCollection();
            $this->_categories->addStoreFilter(Mage::app()->getStore()->getId());
        }
        return $this->_categories;
    }
    
    /**
     * @return array an array of each category object indexed by their IDs. Used
     *     to facilitate generating category links for posts in the list.
     */
    public function getCategoriesCache() {
        if (! $this->_categoriesCache) {
            $this->_categoriesCache = array();
            foreach ($this->getCategories() as $category) {
                $this->_categoriesCache[$category->getId()] = $category;
            }
        }
        return $this->_categoriesCache;
    }
    
    /**
     * @return Lesti_Blog_Model_Resource_Post_Collection
     */
    public function getPosts() {
        if (! $this->_posts) {
            $this->_posts = Mage::getModel('blog/post')->getCollection();
            $this->_posts->addStoreFilter(Mage::app()->getStore()->getId())
                ->addAuthorToResult()
                ->addCategoryIdToResult()
                ->addTagIdToResult()
                ->setPageSize($this->_getPostLimit())
                ->setCurPage($this->_getPager()->getCurrentPage())
                ->addOrder('creation_time', Varien_Data_Collection_Db::SORT_ORDER_DESC);
        }
        return $this->_posts;
    }
    
    /**
     * @return int
     */
    protected function _getPostLimit() {
        $limit = Mage::app()->getRequest()->getParam('limit');
        if (! $limit) {
            $limit = $this->_getPager()->getLimit();
        }
        return $limit;
    }
    
    /**
     * @return Mage_Page_Block_Html_Pager|bool
     */
    protected function _getPager() {
        return $this->getChild('pager');
    }
    
    /**
     * @return string
     */
    public function getPagerHtml() {
        $pager = $this->_getPager();
        if ($pager) {
            return $pager->toHtml();
        }
        return '';
    }
}
