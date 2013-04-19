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

    public function getAuthorUrl($userId)
    {
        $url = Mage::app()->getStore()->getUrl();
        $author = $this->getAuthor($userId);
        if($author->getId()) {
            $url = Mage::app()->getStore()->getUrl(Mage::getStoreConfig(
                Lesti_Blog_Model_Post::XML_PATH_BLOG_GENERAL_ROUTER)) .
                'author/' . strtolower($author->getFirstname());
        }
        return $url;
    }

    public function getAuthor($userId)
    {
        if(!isset($this->_author[$userId])) {
            $this->_author[$userId] = Mage::getModel('admin/user')->load($userId);
        }
        return $this->_author[$userId];
    }
}