<?php

class Lesti_Blog_Block_Tag_Post_List extends Lesti_Blog_Block_Post_List
{
    /**
     * @return Lesti_Blog_Model_Resource_Post_Collection
     */
    public function getPosts() {
        parent::getPosts();
        $this->_posts->addTagFilter($this->getParentBlock()->getTag());
        return $this->_posts;
    }
}
