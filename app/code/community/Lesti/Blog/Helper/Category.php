<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 30.03.13
 * Time: 23:07
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Helper_Category extends Mage_Core_Helper_Abstract
{

    public function initCategory($categoryId, $controller)
    {
        // Init and load category
        Mage::dispatchEvent('blog_controller_category_init_before', array(
            'controller_action' => $controller
        ));

        if (!$categoryId) {
            return false;
        }

        $category = Mage::getModel('blog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);

        if(!$category->getId()) {
            return false;
        }

        // Register current data and dispatch final events
        Mage::register('blog_category', $category);

        try {
            Mage::dispatchEvent('blog_controller_category_init', array('category' => $category));
            Mage::dispatchEvent('blog_controller_category_init_after',
                array('category' => $category,
                    'controller_action' => $controller
                )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $category;
    }

}