<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 17.04.13
 * Time: 17:29
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_AuthorController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize requested post object
     *
     * @return Lesti_Blog_Model_Tag
     */
    protected function _initAuthor()
    {
        $author_id  = mysql_real_escape_string($this->getRequest()->getParam('author_id'));

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
        if($view = $this->getLayout()->getBlock('blog.category.view')) {
            $view->setType(Lesti_Blog_Block_Category_View::OBJECT_TYPE_AUTHOR);
            $view->setObject($author);
        }
        $title = Mage::getStoreConfig(Lesti_Blog_Block_Category_View::XML_PATH_BLOG_TITLE) . ' - ' . $author->getTitle();
        $this->getLayout()->getBlock('head')
            ->setTitle($title);
        $this->renderLayout();
    }
}