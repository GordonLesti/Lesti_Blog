<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 27.03.13
 * Time: 11:59
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Resource_Post extends Mage_Core_Model_Resource_Db_Abstract
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
        $this->_init('blog/post', 'post_id');
    }

    /**
     * Process post data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesti_Blog_Model_Resource_Post
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'post_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('blog/post_store'), $condition);
        $this->_getWriteAdapter()->delete($this->getTable('blog/category_post'), $condition);
        $this->_getWriteAdapter()->delete($this->getTable('blog/tag_post'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process post data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesti_Blog_Model_Resource_Post
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {

        if (!$this->getIsUniquePostToStores($object)) {
            Mage::throwException(Mage::helper('blog')->__('A post URL key for specified store already exists.'));
        }

        if (!$this->isValidPostIdentifier($object)) {
            Mage::throwException(Mage::helper('blog')->__('The post URL key contains capital letters or disallowed symbols.'));
        }

        if ($this->isNumericPostIdentifier($object)) {
            Mage::throwException(Mage::helper('blog')->__('The post URL key cannot consist only of numbers.'));
        }

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Assign post to store views
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Lesti_Blog_Model_Resource_Post
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('blog/post_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'post_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'post_id'  => (int) $object->getId(),
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
     * @return Lesti_Blog_Model_Resource_Post
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
     * @return Lesti_Blog_Model_Resource_Post
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
     * @param Lesti_Blog_Model_Post $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('blog_post_store' => $this->getTable('blog/post_store')),
                $this->getMainTable() . '.post_id = blog_post_store.post_id',
                array())
                ->where('is_active = ?', Lesti_Blog_Model_Post::STATUS_ENABLED)
                ->where('blog_post_store.store_id IN (?)', $storeIds)
                ->order('blog_post_store.store_id DESC')
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
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('bp' => $this->getMainTable()))
            ->join(
            array('bps' => $this->getTable('blog/post_store')),
            'bp.post_id = bps.post_id',
            array())
            ->where('bp.identifier = ?', $identifier)
            ->where('bps.store_id IN (?)', $store);

        if (!is_null($isActive)) {
            $select->where('bp.is_active = ?', $isActive);
        }

        return $select;
    }

    /**
     * Check for unique of identifier of post to selected store(s).
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    public function getIsUniquePostToStores(Mage_Core_Model_Abstract $object)
    {
        if (Mage::app()->isSingleStoreMode() || !$object->hasStores()) {
            $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->_getLoadByIdentifierSelect($object->getData('identifier'), $stores);

        if ($object->getId()) {
            $select->where('bps.post_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     *  Check whether post identifier is numeric
     *
     * @date Wed Mar 26 18:12:28 EET 2008
     *
     * @param Mage_Core_Model_Abstract $object
     * @return bool
     */
    protected function isNumericPostIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether post identifier is valid
     *
     *  @param    Mage_Core_Model_Abstract $object
     *  @return   bool
     */
    protected function isValidPostIdentifier(Mage_Core_Model_Abstract $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    /**
     * Check if post identifier exist for specific store
     * return post id if post exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('bp.post_id')
            ->order('bps.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves post title from DB by passed identifier.
     *
     * @param string $identifier
     * @return string|false
     */
    public function getPostTitleByIdentifier($identifier)
    {
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
        if ($this->_store) {
            $stores[] = (int)$this->getStore()->getId();
        }

        $select = $this->_getLoadByIdentifierSelect($identifier, $stores);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('bp.title')
            ->order('bps.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves post title from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getPostTitleById($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'title')
            ->where('post_id = :post_id');

        $binds = array(
            'post_id' => (int) $id
        );

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Retrieves post identifier from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getPostIdentifierById($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'identifier')
            ->where('post_id = :post_id');

        $binds = array(
            'post_id' => (int) $id
        );

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($ostId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('blog/post_store'), 'store_id')
            ->where('post_id = ?',(int)$postId);

        return $adapter->fetchCol($select);
    }

    /**
     * Set store model
     *
     * @param Mage_Core_Model_Store $store
     * @return Lesti_Blog_Model_Resource_Page
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }
}