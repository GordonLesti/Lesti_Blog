<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 04.04.13
 * Time: 12:06
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Resource_Post_Comment_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
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
        $this->_init('blog/post_comment');
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
            $data['label'] = substr($item->getData('content'), 0, 64);

            if (in_array($identifier, $existingIdentifiers)) {
                $data['value'] .= '|' . $item->getData('comment_id');
            } else {
                $existingIdentifiers[] = $identifier;
            }

            $res[] = $data;
        }

        return $res;
    }

}