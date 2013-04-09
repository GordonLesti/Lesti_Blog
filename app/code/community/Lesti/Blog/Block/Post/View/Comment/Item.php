<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 09.04.13
 * Time: 23:52
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Post_View_Comment_Item extends Mage_Core_Block_Template
{
    /**
     * Set default template
     *
     */
    protected function _construct()
    {
        $this->setTemplate('blog/post/view/comment/item.phtml');
    }
}