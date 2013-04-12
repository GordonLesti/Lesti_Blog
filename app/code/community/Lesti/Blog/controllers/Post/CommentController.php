<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 10.04.13
 * Time: 16:36
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Post_CommentController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch()
    {
        parent::preDispatch();

        $allowGuest = Mage::helper('blog/post_comment')->getIsGuestAllowToWrite();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        if (!$allowGuest && $action == 'post' && $this->getRequest()->isPost()) {
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current' => true)));
                Mage::getSingleton('blog/session')->setFormData($this->getRequest()->getPost())
                    ->setRedirectUrl($this->_getRefererUrl());
                $this->_redirectUrl(Mage::helper('customer')->getLoginUrl());
            }
        }
    }

    protected function _initPost()
    {
        Mage::dispatchEvent('comment_controller_post_init_before', array('controller_action'=>$this));
        $data = $this->getRequest()->getPost();
        $postId = (int) $data['post_id'];

        $post = $this->_loadPost($postId);

        if(!$post) {
            return false;
        }

        try {
            Mage::dispatchEvent('comment_controller_post_init', array('post'=>$post));
            Mage::dispatchEvent('comment_controller_post_init_after', array(
                'post'           => $post,
                'controller_action' => $this
            ));
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $post;
    }

    protected function _loadPost($postId)
    {
        if(!$postId) {
            return false;
        }

        $post = Mage::getModel('blog/post')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($postId);

        if (!$post->getId() || $post->getIsActive() != Lesti_Blog_Model_Post::STATUS_ENABLED) {
            return false;
        }

        Mage::register('blog_post', $post);
        return $post;
    }

    public function postAction()
    {
        if ($data = Mage::getSingleton('blog/session')->getFormData(true)) {
            $rating = array();
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data   = $this->getRequest()->getPost();
        }
    }
}