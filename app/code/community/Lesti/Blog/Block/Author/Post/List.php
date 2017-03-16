<?php

class Lesti_Blog_Block_Author_Post_List extends Lesti_Blog_Block_Post_List
{
    /**
     * @return Lesti_Blog_Model_Resource_Post_Collection
     */
    public function getPosts() {
        parent::getPosts();
        $this->_posts->addAuthorFilter($this->getParentBlock()->getAuthor());
        return $this->_posts;
    }
}
