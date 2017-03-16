<?php

class Lesti_Blog_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Renders Blog Home page
     *
     * @param string $coreRoute
     */
    public function indexAction($coreRoute = null)
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('blog');
        }
        $this->getLayout()->getBlock('head')
            ->setTitle(Mage::getStoreConfig(Lesti_Blog_Block_View::XML_PATH_BLOG_TITLE));
        $this->renderLayout();
    }
}
