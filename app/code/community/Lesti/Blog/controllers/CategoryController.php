<?php

class Lesti_Blog_CategoryController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize requested post object
     *
     * @return Lesti_Blog_Model_Category
     */
    protected function _initCategory()
    {
        $categoryId  = (int) $this->getRequest()->getParam('category_id');

        $params = new Varien_Object();

        return Mage::helper('blog/category')->initCategory($categoryId, $this);
    }

    public function viewAction()
    {
        $category = $this->_initCategory();

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('blog');
        }
        if($view = $this->getLayout()->getBlock('blog.category.view')) {
            $view->setType(Lesti_Blog_Block_Category_View::OBJECT_TYPE_CATEGORY);
            $view->setObject($category);
        }
        $title = Mage::getStoreConfig(Lesti_Blog_Block_Category_View::XML_PATH_BLOG_TITLE) . ' - ' . $category->getTitle();
        $this->getLayout()->getBlock('head')
            ->setTitle($title);
        $this->renderLayout();
    }
}
