<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 14.04.13
 * Time: 21:00
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Resource_Tag_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/tag');
        $this->_map['fields']['tag_id']    = 'main_table.tag_id';
        $this->_map['fields']['store']     = 'store_table.store_id';
        $this->_map['fields']['post']      = 'post_table.post_id';
    }

    /**
     * Returns pairs identifier - title for unique identifiers
     * and pairs identifier|tag_id - title for non-unique after first
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $res = array();
        $existingIdentifiers = array();
        foreach ($this as $item) {
            $identifier = $item->getData('identifier');

            $data['value'] = $identifier;
            $data['label'] = $item->getData('title');

            if (in_array($identifier, $existingIdentifiers)) {
                $data['value'] .= '|' . $item->getData('tag_id');
            } else {
                $existingIdentifiers[] = $identifier;
            }

            $res[] = $data;
        }

        return $res;
    }

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return Lesti_blog_Model_Resource_Tag_Collection
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return Lesti_Blog_Model_Resource_Tag_Collection
     */
    protected function _afterLoad()
    {
        if ($this->_previewFlag) {
            $items = $this->getColumnValues('tag_id');
            $connection = $this->getConnection();
            if (count($items)) {
                $select = $connection->select()
                    ->from(array('bts'=>$this->getTable('blog/tag_store')))
                    ->where('bts.tag_id IN (?)', $items);

                if ($result = $connection->fetchPairs($select)) {
                    foreach ($this as $item) {
                        if (!isset($result[$item->getData('tag_id')])) {
                            continue;
                        }
                        if ($result[$item->getData('tag_id')] == 0) {
                            $stores = Mage::app()->getStores(false, true);
                            $storeId = current($stores)->getId();
                            $storeCode = key($stores);
                        } else {
                            $storeId = $result[$item->getData('tag_id')];
                            $storeCode = Mage::app()->getStore($storeId)->getCode();
                        }
                        $item->setData('_first_store_id', $storeId);
                        $item->setData('store_code', $storeCode);
                    }
                }
            }
        }

        return parent::_afterLoad();
    }

    /**
     * Add filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @param bool $withAdmin
     * @return Lesti_Blog_Model_Resource_Tag_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = array($store->getId());
            }

            if (!is_array($store)) {
                $store = array($store);
            }

            if ($withAdmin) {
                $store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
            }

            $this->addFilter('store', array('in' => $store), 'public');
        }
        return $this;
    }

    public function addCountToResult()
    {
        $this->_joinCount();
        return $this;
    }

    public function _joinCount()
    {
        $this->getSelect()->join(
            array('tag_post' => $this->getTable('blog/tag_post')),
            'main_table.tag_id = tag_post.tag_id',
            array('count' => 'COUNT(*)')
        )->group('main_table.tag_id');
        return $this;
    }

    /**
     * Add filter by post
     *
     * @param int|Lesti_Blog_Model_Post $post
     * @return Lesti_Blog_Model_Resource_Tag_Collection
     */
    public function addPostFilter($post)
    {
        if (!$this->getFlag('post_filter_added')) {
            if ($post instanceof Lesti_Blog_Model_Post) {
                $post = array($post->getId());
            }

            if (!is_array($post)) {
                $post = array($post);
            }

            $this->addFilter('post', array('in' => $post), 'public');
        }
        return $this;
    }

    /**
     * Join store and post relation table if there is store or post filter
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('blog/tag_store')),
                'main_table.tag_id = store_table.tag_id',
                array()
            )->group('main_table.tag_id');

            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        if($this->getFilter('post')) {
            $this->getSelect()->join(
                array('post_table' => $this->getTable('blog/tag_post')),
                'main_table.tag_id = post_table.tag_id',
                array()
            )->group('main_table.tag_id');

            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        return parent::_renderFiltersBefore();
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();

        $countSelect->reset(Zend_Db_Select::GROUP);

        return $countSelect;
    }

    protected function _toOptionArray($valueField='tag_id', $labelField='title', $additional=array())
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}