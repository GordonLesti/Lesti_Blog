<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 17.04.13
 * Time: 17:34
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Author extends Varien_Object
{

    public function exists($authorName, $storeId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('read');
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $user = Mage::getResourceModel('admin/user');
        $select = $connection->select()
            ->from(array('ba' => $user->getMainTable()))
            ->join(
                array('bp' => $user->getTable('blog/post')),
                'ba.user_id = bp.author_id',
                array())
            ->join(
                array('bps' => $user->getTable('blog/post_store')),
                'bp.post_id = bps.post_id',
                array())
            ->where('ba.firstname = ?', $authorName)
            ->where('bps.store_id IN (?)', $stores);

        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('ba.firstname')
            ->order('bps.store_id DESC')
            ->limit(1);

        return $connection->fetchOne($select);
    }

}