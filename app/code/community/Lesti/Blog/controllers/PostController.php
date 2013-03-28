<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 28.03.13
 * Time: 10:50
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_PostController extends Mage_Core_Controller_Front_Action
{
    /**
     * Initialize requested post object
     *
     * @return Lesti_Blog_Model_Post
     */
    protected function _initPost()
    {
        $postId  = (int) $this->getRequest()->getParam('post_id');

        $params = new Varien_Object();

        return Mage::helper('blog/post')->initPost($postId, $this);
    }

    public function viewAction()
    {
        $post = $this->_initPost();

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('blog');
        }
        $title = Mage::getStoreConfig(Lesti_Blog_Block_Category_View::XML_PATH_BLOG_TITLE) . ' - ' . $post->getTitle();
        $this->getLayout()->getBlock('head')
            ->setTitle($title);
        $this->renderLayout();
    }
}