<?php

class Lesti_Blog_Model_Resource_Tag extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Store model
     *
     * @var null|Mage_Core_Model_Store
     */
    protected $_store  = null;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/tag', 'tag_id');
    }

    /**
     * Process tag data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesti_Blog_Model_Resource_Tag
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'tag_id = ?'     => (int) $object->getId(),
        );
        $this->_getWriteAdapter()->delete($this->getTable('blog/tag_post'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process tag data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesti_Blog_Model_Resource_Tag
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {

        if (!$this->getIsUniqueTagToStores($object)) {
            Mage::throwException(Mage::helper('blog')->__('A tag URL key for specified store already exists.'));
        }

        if (!$this->isValidTagIdentifier($object)) {
            Mage::throwException(Mage::helper('blog')->__('The tag URL key contains capital letters or disallowed symbols.'));
        }

        if ($this->isNumericTagIdentifier($object)) {
            Mage::throwException(Mage::helper('blog')->__('The tag URL key cannot consist only of numbers.'));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Assign tag to store views
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesti_Blog_Model_Resource_Tag
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('blog/tag_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'tag_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'tag_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * Load an object using 'identifier' field if there's no field specified and value is not numeric
     *
     * @param Mage_Core_Model_Abstract $object
     * @param mixed $value
     * @param string $field
     * @return Lesti_Blog_Model_Resource_Tag
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesti_Blog_Model_Resource_Tag
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());

            $object->setData('store_id', $stores);

        }

        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Lesti_Blog_Model_Tag $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('blog_tag_store' => $this->getTable('blog/tag_store')),
                $this->getMainTable() . '.tag_id = blog_tag_store.tag_id',
                array())
                ->where('blog_tag_store.store_id IN (?)', $storeIds)
                ->order('blog_tag_store.store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return Varien_Db_Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('bt' => $this->getMainTable()))
            ->join(
            array('bts' => $this->getTable('blog/tag_store')),
            'bt.tag_id = bts.tag_id',
            array())
            ->where('bt.identifier = ?', $identifier)
            ->where('bts.store_id IN (?)', $store);

        return $select;
    }

    /**
     * Check for unique of identifier of tag to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniqueTagToStores(Mage_Core_Model_Abstract $object)
    {
        if (Mage::app()->isSingleStoreMode() || !$object->hasStores()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->_getLoadByIdentifierSelect($object->getData('identifier'), $stores);

        if ($object->getId()) {
            $select->where('bts.tag_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     *  Check whether tag identifier is numeric
     *
     * @date Wed Mar 26 18:12:28 EET 2008
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    protected function isNumericTagIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether tag identifier is valid
     *
     *  @param    Mage_Core_Model_Abstract $object
     *  @return   bool
     */
    protected function isValidTagIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    /**
     * Check if tag identifier exist for specific store
     * return tag id if tag exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('bt.tag_id')
            ->order('bts.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves tag title from DB by passed identifier.
     *
     * @param string $identifier
     * @return string|false
     */
    public function getTagTitleByIdentifier($identifier)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        if ($this->_store) {
            $stores[] = (int)$this->getStore()->getId();
        }

        $select = $this->_getLoadByIdentifierSelect($identifier, $stores);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('bt.title')
            ->order('bts.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves tag title from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getTagTitleById($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'title')
            ->where('tag_id = :tag_id');

        $binds = array(
            'tag_id' => (int) $id
        );

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Retrieves tag identifier from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getTagIdentifierById($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'identifier')
            ->where('tag_id = :tag_id');

        $binds = array(
            'tag_id' => (int) $id
        );

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($tagId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('blog/tag_store'), 'store_id')
            ->where('tag_id = ?',(int)$tagId);

        return $adapter->fetchCol($select);
    }

    /**
     * Set store model
     *
     * @param Mage_Core_Model_Store $store
     * @return Lesti_Blog_Model_Resource_Tag
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

}
