<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 27.03.13
 * Time: 13:25
 * To change this template use File | Settings | File Templates.
 */
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
            ->setTitle(Mage::getStoreConfig(Lesti_Blog_Block_Category_View::XML_PATH_BLOG_TITLE));
        $this->renderLayout();
    }
}