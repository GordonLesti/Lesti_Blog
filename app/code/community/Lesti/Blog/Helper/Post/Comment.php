<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 12.04.13
 * Time: 12:01
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Helper_Post_Comment extends Mage_Core_Helper_Abstract
{

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

}