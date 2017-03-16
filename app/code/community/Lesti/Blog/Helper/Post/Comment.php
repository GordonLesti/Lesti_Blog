<?php

class Lesti_Blog_Helper_Post_Comment extends Mage_Core_Helper_Abstract
{
    const XML_PATH_COMMENT_GUEST_ALLOW = 'blog/general/allow_guest';
    const ALLOW = 'a[href|title],abbr[title],acronym[title],b,blockquote[cite],cite,code,del,em,i,q[cite],strike,strong';

    public function purifyHtml($html)
    {
        require_once Mage::getBaseDir('lib') . DS . 'HTMLPurifier.includes.php';
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', Mage::getBaseDir('cache'));
        $config->set('Cache.SerializerPermissions', 777);
        $config->set('HTML.Allowed', self::ALLOW);
        $purifier = new HTMLPurifier($config);
        return $purifier->purify($html);
    }

    public function getIsGuestAllowToWrite()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_COMMENT_GUEST_ALLOW);
    }

    public function getIsCustomerAllowedToWrite($post)
    {
        if($post->getId() && $post->getAllowComments()) {
            return $this->getIsGuestAllowToWrite() || Mage::getSingleton('customer/session')->isLoggedIn();
        }
    }

}
