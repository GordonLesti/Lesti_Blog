<?php

class Lesti_Blog_Model_Observer
{
    /**
     * Add a new author when a new admin user is created.
     * 
     * @param Varien_Event_Observer $observer
     * @return Lesti_Blog_Model_Observer
     */
    public function addNewAuthor(Varien_Event_Observer $observer)
    {
        $user = $observer->getEvent()->getObject();
        if (! $user->isObjectNew()) {
            return $observer;
        }
        
        $author = Mage::getModel('blog/author');
        $firstname = strtolower($user->getFirstname());
        if (! $firstname) {
            $firstname = 'author';
        }
        $author->setAuthorName($firstname)->setAdminUserId($user->getId());
        $author->save();
        
        return $observer;
    }
}
