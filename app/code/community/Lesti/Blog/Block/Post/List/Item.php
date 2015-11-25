<?php

class Lesti_Blog_Block_Post_List_Item extends Mage_Core_Block_Template
{
    /**
     * @param int $categoryId
     * @return Lesti_Blog_Model_Category
     */
    public function getCategory($categoryId) {
        $categories = $this->getCategoriesCache();
        if (! empty($categories[$categoryId])) {
            return $categories[$categoryId];
        }
        return null;
    }
    
    /**
     * @param int $tagId
     * @return Lesti_Blog_Model_Tag
     */
    public function getTag($tagId) {
        $categories = $this->getTagsCache();
        if (! empty($categories[$tagId])) {
            return $categories[$tagId];
        }
        return null;
    }
    
    /**
     * @return array
     * @see Lesti_Blog_Block_Post_List->getCategoriesCache()
     */
    public function getCategoriesCache() {
        $listBlock = $this->getParentBlock();
        if ($listBlock) {
            return $listBlock->getCategoriesCache();
        }
        return array();
    }
    
    /**
     * @return array
     * @see Lesti_Blog_Block_Post_List->getTagsCache()
     */
    public function getTagsCache() {
        $listBlock = $this->getParentBlock();
        if ($listBlock) {
            return $listBlock->getTagsCache();
        }
        return array();
    }
}
