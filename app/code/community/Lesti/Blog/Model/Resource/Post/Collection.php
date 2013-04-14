<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 27.03.13
 * Time: 15:14
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Resource_Post_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;
    protected $_groupByMonth;

    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/post');
        $this->_map['fields']['post_id']    = 'main_table.post_id';
        $this->_map['fields']['store']      = 'store_table.store_id';
        $this->_map['fields']['category']   = 'category_table.category_id';
        $this->_map['fields']['tag']        = 'tag_table.tag_id';
    }

    /**
     * Returns pairs identifier - title for unique identifiers
     * and pairs identifier|post_id - title for non-unique after first
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
                $data['value'] .= '|' . $item->getData('post_id');
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
     * @return Lesti_blog_Model_Resource_Post_Collection
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return Lesti_Blog_Model_Resource_Post_Collection
     */
    protected function _afterLoad()
    {
        if ($this->_previewFlag) {
            $items = $this->getColumnValues('post_id');
            $connection = $this->getConnection();
            if (count($items)) {
                $select = $connection->select()
                    ->from(array('bps'=>$this->getTable('blog/post_store')))
                    ->where('bps.post_id IN (?)', $items);

                if ($result = $connection->fetchPairs($select)) {
                    foreach ($this as $item) {
                        if (!isset($result[$item->getData('post_id')])) {
                            continue;
                        }
                        if ($result[$item->getData('post_id')] == 0) {
                            $stores = Mage::app()->getStores(false, true);
                            $storeId = current($stores)->getId();
                            $storeCode = key($stores);
                        } else {
                            $storeId = $result[$item->getData('post_id')];
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
     * @return Lesti_Blog_Model_Resource_Post_Collection
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

    public function addGroupByMonth()
    {
        $this->_groupByMonth = true;
        return $this;
    }

    /**
     * Add filter by category
     *
     * @param int|Lesti_Blog_Model_Category $category
     * @param bool $withAdmin
     * @return Lesti_Blog_Model_Resource_Post_Collection
     */
    public function addCategoryFilter($category)
    {
        if (!$this->getFlag('category_filter_added')) {
            if ($category instanceof Lesti_Blog_Model_Category) {
                $category = array($category->getId());
            }

            if (!is_array($category)) {
                $category = array($category);
            }

            $this->addFilter('category', array('in' => $category), 'public');
        }
        return $this;
    }

    /**
     * Add filter by tag
     *
     * @param int|Lesti_Blog_Model_Tag $tag
     * @param bool $withAdmin
     * @return Lesti_Blog_Model_Resource_Post_Collection
     */
    public function addTagFilter($tag)
    {
        if (!$this->getFlag('tag_filter_added')) {
            if ($tag instanceof Lesti_Blog_Model_Tag) {
                $tag = array($tag->getId());
            }

            if (!is_array($tag)) {
                $tag = array($tag);
            }

            $this->addFilter('tag', array('in' => $tag), 'public');
        }
        return $this;
    }

    public function addAuthorToResult()
    {
        $this->joinAuthor();
        return $this;
    }

    protected function joinAuthor()
    {
        $this->getSelect()->join(
            array('admin_user' => $this->getTable('admin/user')),
            'main_table.author_id = admin_user.user_id',
            array('admin_user.username')
        )->group('main_table.post_id');
        return $this;
    }

    /**
     * Join store and category relation table if there is store or category filter
     */
    protected function _renderFiltersBefore()
    {
        if($this->_groupByMonth) {
            $this->getSelect()->group('YEAR(creation_time), MONTH(creation_time)');
        }
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('blog/post_store')),
                'main_table.post_id = store_table.post_id',
                array()
            );
            if(!$this->_groupByMonth) {
                $this->getSelect()->group('main_table.post_id');
            }

            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        if($this->getFilter('category')) {
            $this->getSelect()->join(
                array('category_table' => $this->getTable('blog/category_post')),
                'main_table.post_id = category_table.post_id',
                array()
            );
            if(!$this->_groupByMonth) {
                $this->getSelect()->group('main_table.post_id');
            }

            /*
             * Allow analytic functions usage because of one field grouping
             */
            $this->_useAnalyticFunction = true;
        }
        if($this->getFilter('tag')) {
            $this->getSelect()->join(
                array('tag_table' => $this->getTable('blog/tag_post')),
                'main_table.post_id = tag_table.post_id',
                array()
            );
            if(!$this->_groupByMonth) {
                $this->getSelect()->group('main_table.post_id');
            }

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
}