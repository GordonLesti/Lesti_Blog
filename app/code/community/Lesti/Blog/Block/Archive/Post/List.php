<?php

class Lesti_Blog_Block_Archive_Post_List extends Lesti_Blog_Block_Post_List
{
    /**
     * @return Lesti_Blog_Model_Resource_Post_Collection
     */
    public function getPosts() {
        return $this->getParentBlock()->getArchive()->getPosts();
    }
}
