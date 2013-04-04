<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 04.04.13
 * Time: 15:33
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_helper_Gravatar extends Mage_Core_Helper_Abstract
{

    public function getGravatarSrc($email, $size = 200)
    {
        $hash = md5(strtolower(trim($email)));
        $protocoll = 'http';
        if(Mage::app()->getStore()->isCurrentlySecure()) {
            $protocoll = 'https';
        }
        $url = $protocoll . '://www.gravatar.com/avatar/' . $hash . '?s=' . $size;
        return $url;
    }

}