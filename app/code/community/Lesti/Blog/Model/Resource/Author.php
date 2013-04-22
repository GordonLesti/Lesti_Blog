<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 22.04.13
 * Time: 18:27
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Resource_Author extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/author', 'author_id');
    }

    /**
     * Process author data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesti_Blog_Model_Resource_Author
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {

        if (!$this->isValidAuthorName($object)) {
            Mage::throwException(Mage::helper('blog')->__('The author URL key contains capital letters or disallowed symbols.'));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Load an object using 'authorname' field if there's no field specified and value is not numeric
     *
     * @param Mage_Core_Model_Abstract $object
     * @param mixed $value
     * @param string $field
     * @return Lesti_Blog_Model_Resource_Author
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (!is_string($value) && is_null($field)) {
            $field = 'author_name';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Retrieve load select with filter by authorname, store and activity
     *
     * @param string $authorname
     * @param int|array $store
     * @param int $isActive
     * @return Varien_Db_Select
     */
    protected function _getLoadByAuthorNameSelect($authorname, $storeId)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_getReadAdapter()->select()
            ->from(array('ba' => $this->getMainTable()))
            ->join(
                array('au' => $this->getTable('admin/user')),
                'ba.admin_user_id = au.user_id',
                array()
            )
            ->join(
                array('bp' => $this->getTable('blog/post')),
                'au.user_id = bp.author_id',
                array()
            )
            ->join(
                array('bps' => $this->getTable('blog/post_store')),
                'bp.post_id = bps.post_id',
                array()
            )
            ->where('ba.author_name = ?', $authorname)
            ->where('bps.store_id IN (?)', $stores);

        return $select;
    }

    /**
     *  Check whether author authorname is valid
     *
     *  @param    Mage_Core_Model_Abstract $object
     *  @return   bool
     */
    protected function isValidAuthorName(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('author_name'));
    }

    /**
     * Check if author name exist for specific store
     * return author name if author exists
     *
     * @param string $authorname
     * @param int $storeId
     * @return int
     */
    public function checkAuthorName($authorname, $storeId)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_getLoadByAuthorNameSelect($authorname, $stores);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('ba.author_name')
            ->order('bps.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

}