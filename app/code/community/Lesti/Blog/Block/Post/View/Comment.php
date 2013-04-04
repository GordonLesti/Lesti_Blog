<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 04.04.13
 * Time: 12:46
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Post_View_Comment extends Mage_Core_Block_Template
{

    protected $_post;
    protected $_commentCollection;

    public function getPost()
    {
        if(is_null($this->_post))
        {
            $this->_post = $this->_getPost();
        }
        return $this->_post;
    }

    public function getCommentCollection()
    {
        if(is_null($this->_commentCollection)) {
            $collection = $this->getPost()->getCommentCollection();
            $collection->addFieldToFilter('status', Lesti_Blog_Model_Post_Comment::STATUS_ENABLED);
            $this->_commentCollection = $collection;
        }
        return $this->_commentCollection;
    }

    protected function _getPost()
    {
        return Mage::registry('blog_post');
    }

}