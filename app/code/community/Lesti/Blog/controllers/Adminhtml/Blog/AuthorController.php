<?php

class Lesti_Blog_Adminhtml_Blog_AuthorController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init actions
     *
     * @return Lesti_Blog_Adminhtml_Blog_PostController
     */
    protected function _initAction() {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('blog/author')
            ->_addBreadcrumb(Mage::helper('blog')->__('Blog'), Mage::helper('blog')->__('Blog'))
            ->_addBreadcrumb(Mage::helper('blog')->__('Manage Authors'), Mage::helper('blog')->__('Manage Authors'));
        return $this;
    }

    public function indexAction() {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Manage Authors'));
        
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Edit author
     */
    public function editAction() {
        $this->_title($this->__('Blog'))
             ->_title($this->__('Manage Authors'));
        
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('author_id');
        $model = Mage::getModel('blog/author');
        
        if (! $id) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('blog')->__('This author no longer exists.')
            );
            $this->_redirect('*/*/');
            return;

        }
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blog')->__('This author no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }
        
        $this->_title($model->getAuthorName());
        
        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }
        
        // 4. Register model to use later in blocks
        Mage::register('blog_author', $model);
        
        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('blog')->__('Edit Author'),
                Mage::helper('blog')->__('Edit Author'));
        
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction() {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            
            //init model and set data
            $model = Mage::getModel('blog/author');
            if ($id = $this->getRequest()->getParam('author_id')) {
                $model->load($id);
            }
            $model->setData($data);
            Mage::dispatchEvent('blog_author_prepare_save', array('author' => $model, 'request' => $this->getRequest()));
            
            // try to save it
            try {
                // save the data
                $model->save();
                
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('The author has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('author_id' => $model->getId(), '_current' => true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('blog')->__('An error occurred while saving the author.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('author_id' => $this->getRequest()->getParam('author_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
    
    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed() {
        switch ($this->getRequest()->getActionName()) {
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('blog/author/save');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('blog/author');
                break;
        }
    }
}
