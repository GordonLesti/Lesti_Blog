<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 12.04.13
 * Time: 15:49
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Helper_Author extends Mage_Core_Helper_Abstract
{
    protected $_author = array();

    public function initAuthor($authorName, $controller)
    {
        // Init and load author
        Mage::dispatchEvent('blog_controller_author_init_before', array(
            'controller_action' => $controller
        ));

        if (!$authorName) {
            return false;
        }

        $user = Mage::getModel('admin/user')
            ->load($categoryId);

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

    public function getAuthorUrl($authorName)
    {
        $url = Mage::app()->getStore()->getUrl(Mage::getStoreConfig(
            Lesti_Blog_Model_Post::XML_PATH_BLOG_GENERAL_ROUTER)) .
            'author/' . strtolower($authorName);
        return $url;
    }

}