<?php

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
