<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 04.04.13
 * Time: 12:00
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Resource_Post_Comment extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/post_comment', 'comment_id');
    }

    /**
     * Process comment data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesti_Blog_Model_Resource_Post_Comment
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        return parent::_beforeSave($object);
    }

}