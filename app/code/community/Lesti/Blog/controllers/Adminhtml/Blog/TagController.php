<?php

class Lesti_Blog_Adminhtml_Blog_TagController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Lesti_Blog_Adminhtml_Blog_TagController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('blog/tag')
            ->_addBreadcrumb(Mage::helper('blog')->__('Tag'), Mage::helper('blog')->__('Tag'))
            ->_addBreadcrumb(Mage::helper('blog')->__('Manage Tags'), Mage::helper('blog')->__('Manage Tags'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Tags'))
            ->_title($this->__('Manage Tags'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new tag
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit tag
     */
    public function editAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Tag'))
            ->_title($this->__('Manage Tags'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('tag_id');
        $model = Mage::getModel('blog/tag');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blog')->__('This tag no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Tag'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('blog_tag', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('blog')->__('Edit Tag')
                    : Mage::helper('blog')->__('New Tag'),
                $id ? Mage::helper('blog')->__('Edit Tag')
                    : Mage::helper('blog')->__('New Tag'));

        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            //init model and set data
            $model = Mage::getModel('blog/tag');

            if ($id = $this->getRequest()->getParam('tag_id')) {
                $model->load($id);
            }

            $model->setData($data);

            Mage::dispatchEvent('blog_tag_prepare_save', array('tag' => $model, 'request' => $this->getRequest()));


            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('The tag has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('tag_id' => $model->getId(), '_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('blog')->__('An error occurred while saving the tag.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('tag_id' => $this->getRequest()->getParam('tag_id')));
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('tag_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('blog/tag');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('cms')->__('The tag has been deleted.'));
                // go to grid
                Mage::dispatchEvent('adminhtml_blog_tag_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_blog_tag_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('tag_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Unable to find a tag to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('blog/tag/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('blog/tag/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('blog/tag');
                break;
        }
    }
}
