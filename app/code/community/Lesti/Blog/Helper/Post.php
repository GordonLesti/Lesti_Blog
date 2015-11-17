<?php

class Lesti_Blog_Helper_Post extends Mage_Core_Helper_Abstract
{

    public function initPost($postId, $controller)
    {
        // Init and load post
        Mage::dispatchEvent('blog_controller_post_init_before', array(
            'controller_action' => $controller
        ));

        if (!$postId) {
            return false;
        }

        $post = Mage::getModel('blog/post')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($postId);

        if(!$post->getId() || !$post->getIsActive()) {
            return false;
        }

        // Register current data and dispatch final events
        Mage::register('blog_post', $post);

        try {
            Mage::dispatchEvent('blog_controller_post_init', array('post' => $post));
            Mage::dispatchEvent('blog_controller_post_init_after',
                array('post' => $post,
                    'controller_action' => $controller
                )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $post;
    }

}
