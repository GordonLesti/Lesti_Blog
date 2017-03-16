<?php

class Lesti_Blog_ArchiveController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize requested post object
     *
     * @return Lesti_Blog_Model_Tag
     */
    protected function _initArchive()
    {
        return Mage::helper('blog/archive')
            ->initArchive($this->_getYear(), $this->_getMonth(), $this);
    }

    public function viewAction()
    {
        $archive = $this->_initArchive();
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('blog');
        }
        $title = Mage::getStoreConfig(Lesti_Blog_Block_View::XML_PATH_BLOG_TITLE) . ' - ' . $archive->getTitle();
        $this->getLayout()->getBlock('head')->setTitle($title);
        $this->getLayout()->getBlock('content')->getChild('blog.posts.view')->setArchive($archive);
        $this->renderLayout();
    }
    
    /**
     * @return int
     */
    protected function _getYear() {
        return (int) $this->getRequest()->getParam('year');
    }
    
    /**
     * @return int
     */
    protected function _getMonth() {
        return (int) $this->getRequest()->getParam('month');
    }
}
