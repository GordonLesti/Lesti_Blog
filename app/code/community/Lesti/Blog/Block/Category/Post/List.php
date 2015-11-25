<?php

class Lesti_Blog_Block_Category_Post_List extends Lesti_Blog_Block_Post_List
{
    /**
     * @return Lesti_Blog_Model_Resource_Post_Collection
     */
    public function getPosts() {
        parent::getPosts();
        $this->_posts->addCategoryFilter($this->getParentBlock()->getCategory());
        return $this->_posts;
    }
}
