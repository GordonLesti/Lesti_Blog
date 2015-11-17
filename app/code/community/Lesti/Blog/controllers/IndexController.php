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
        if($view = $this->getLayout()->getBlock('blog.category.view')) {
            $view->setType(Lesti_Blog_Block_Category_View::OBJECT_TYPE_DEFAULT);
            $view->setObject(new Varien_Object(
                array('title' => Mage::getStoreConfig(Lesti_Blog_Block_Category_View::XML_PATH_BLOG_TITLE))
            ));
        }
        $this->getLayout()->getBlock('head')
            ->setTitle(Mage::getStoreConfig(Lesti_Blog_Block_Category_View::XML_PATH_BLOG_TITLE));
        $this->renderLayout();
    }
}
