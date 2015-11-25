<?php

class Lesti_Blog_TagController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize requested post object
     *
     * @return Lesti_Blog_Model_Tag
     */
    protected function _initTag()
    {
        $tagId  = (int) $this->getRequest()->getParam('tag_id');

        return Mage::helper('blog/tag')->initTag($tagId, $this);
    }

    public function viewAction()
    {
        $tag = $this->_initTag();

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('blog');
        }
        $title = Mage::getStoreConfig(Lesti_Blog_Block_View::XML_PATH_BLOG_TITLE) . ' - ' . $tag->getTitle();
        $this->getLayout()->getBlock('head')
            ->setTitle($title);
        $this->getLayout()->getBlock('content')->getChild('blog.posts.view')->setTag($tag);
        $this->renderLayout();
    }
}
