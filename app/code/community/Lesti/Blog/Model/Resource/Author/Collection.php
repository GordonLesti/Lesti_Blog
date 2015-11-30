<?php

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
    
    /**
     * Add admin user info to the authors
     * 
     * @return Lesti_Blog_Model_Resource_Author_Collection
     */
    public function addAdminUserToResult() {
        $this->getSelect()->join(
            array('admin_user' => $this->getTable('admin/user')),
            'main_table.admin_user_id = admin_user.user_id',
            array(
                'admin_user_username'  => 'admin_user.username',
                'admin_user_email'     => 'admin_user.email',
                'admin_user_firstname' => 'admin_user.firstname',
                'admin_user_lastname'  => 'admin_user.lastname'
            )
        );
        
        return $this;
    }
}
