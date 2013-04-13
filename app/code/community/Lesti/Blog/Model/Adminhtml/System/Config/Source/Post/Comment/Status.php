<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 12.04.13
 * Time: 15:33
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Adminhtml_System_Config_Source_Post_Comment_Status
{

    protected $_statuses;

    public function toOptionArray()
    {
        if(is_null($this->_statuses)) {
            $this->_statuses = Mage::getModel('blog/post_comment')->getAvailableStatuses();
        }
        return $this->_statuses;
    }

}