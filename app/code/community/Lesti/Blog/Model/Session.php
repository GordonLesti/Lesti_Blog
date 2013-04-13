<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 12.04.13
 * Time: 17:57
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('blog');
    }
}
