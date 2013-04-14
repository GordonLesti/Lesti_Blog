<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 14.04.13
 * Time: 21:35
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Helper_Tag extends Mage_Core_Helper_Abstract
{
    public function initTag($tagId, $controller)
    {
        // Init and load tag
        Mage::dispatchEvent('blog_controller_tag_init_before', array(
            'controller_action' => $controller
        ));

        if (!$tagId) {
            return false;
        }

        $tag = Mage::getModel('blog/tag')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($tagId);

        if(!$tag->getId()) {
            return false;
        }

        // Register current data and dispatch final events
        Mage::register('blog_tag', $tag);

        try {
            Mage::dispatchEvent('blog_controller_tag_init', array('tag' => $tag));
            Mage::dispatchEvent('blog_controller_tag_init_after',
                array('tag' => $tag,
                    'controller_action' => $controller
                )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $tag;
    }
}