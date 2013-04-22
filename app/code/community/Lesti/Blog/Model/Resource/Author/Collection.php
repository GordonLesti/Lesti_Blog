<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 22.04.13
 * Time: 19:24
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Model_Resource_Author_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('blog/author');
    }

    /**
     * Returns pairs authorname - title for unique authornames
     * and pairs authorname|author_id - title for non-unique after first
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $res = array();
        $existingAuthornames = array();
        foreach ($this as $item) {
            $authorname = $item->getData('author_name');

            $data['value'] = $authorname;
            $data['label'] = $item->getData('author_name');

            if (in_array($authorname, $existingAuthornames)) {
                $data['value'] .= '|' . $item->getData('author_id');
            } else {
                $existingAuthornames[] = $authorname;
            }

            $res[] = $data;
        }

        return $res;
    }

    protected function _toOptionArray($valueField='author_id', $labelField='authorname', $additional=array())
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}