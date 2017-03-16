<?php

class Lesti_Blog_AuthorController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize requested post object
     *
     * @return Lesti_Blog_Model_Tag
     */
    protected function _initAuthor()
    {
        $author_id  = $this->getRequest()->getParam('author_id');

        return Mage::helper('blog/author')->initAuthor($author_id, $this);
    }

    public function viewAction()
    {
        $author = $this->_initAuthor();

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('blog');
        }
        $title = Mage::getStoreConfig(Lesti_Blog_Block_View::XML_PATH_BLOG_TITLE) . ' - ' . $author->getTitle();
        $this->getLayout()->getBlock('head')
            ->setTitle($title);
        $this->getLayout()->getBlock('content')->getChild('blog.posts.view')->setAuthor($author);
        $this->renderLayout();
    }
}
