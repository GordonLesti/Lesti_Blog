<?php

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
            $this->_prepareCommentBlocks();
        }
        return $this->_commentCollection;
    }

    protected function _prepareCommentBlocks()
    {
        if(!is_null($this->_commentCollection)) {
            $comments = array();
            foreach($this->_commentCollection as $comment) {
                $block = $this->getLayout()->createBlock('blog/post_view_comment_item');
                $comment->setPost($this->_post);
                $block->setComment($comment);
                if(!$comment->getParentId()) {
                    $this->setChild('comment-' . $comment->getId(), $block);
                } elseif(isset($comments[$comment->getParentId()])){
                    $comments[$comment->getParentId()]->setChild('comment-' . $comment->getId(), $block);
                }
                $comments[$comment->getId()] = $block;
            }
        }
        return $this;
    }

    protected function _getPost()
    {
        return Mage::registry('blog_post');
    }

    public function getFormActionUrl()
    {
        return Mage::app()->getStore()->getUrl('blog/post_comment/post');
    }

}
