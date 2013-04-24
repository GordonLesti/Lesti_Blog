<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 24.04.13
 * Time: 12:55
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_ArchiveController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize requested post object
     *
     * @return Lesti_Blog_Model_Tag
     */
    protected function _initArchive()
    {
        $year  = (int) $this->getRequest()->getParam('year');
        $month = (int) $this->getRequest()->getParam('month');

        return Mage::helper('blog/archive')->initArchive($year . '-' . $month, $this);
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
        if($view = $this->getLayout()->getBlock('blog.category.view')) {
            $view->setType(Lesti_Blog_Block_Category_View::OBJECT_TYPE_ARCHIVE);
            $view->setObject($archive);
        }
        $title = Mage::getStoreConfig(Lesti_Blog_Block_Category_View::XML_PATH_BLOG_TITLE) . ' - ' . $archive->getTitle();
        $this->getLayout()->getBlock('head')
            ->setTitle($title);
        $this->renderLayout();
    }
}