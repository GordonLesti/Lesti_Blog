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

    public function initAuthor($authorId, $controller)
    {
        // Init and load author
        Mage::dispatchEvent('blog_controller_author_init_before', array(
            'controller_action' => $controller
        ));

        if (!$authorId) {
            return false;
        }

        $author = Mage::getModel('blog/author')
            ->load($authorId);

        // Register current data and dispatch final events
        Mage::register('blog_author', $author);

        try {
            Mage::dispatchEvent('blog_controller_author_init', array('author' => $author));
            Mage::dispatchEvent('blog_controller_author_init_after',
                array('author' => $author,
                    'controller_action' => $controller
                )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }
        $author->setTitle(ucfirst($author->getAuthorName()));
        return $author;
    }

    public function getAuthorUrl($authorName)
    {
        $url = Mage::app()->getStore()->getUrl(Mage::getStoreConfig(
            Lesti_Blog_Model_Post::XML_PATH_BLOG_GENERAL_ROUTER)) .
            'author/' . strtolower($authorName);
        return $url;
    }

}