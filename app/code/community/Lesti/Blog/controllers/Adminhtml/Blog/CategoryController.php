<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 30.03.13
 * Time: 21:53
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Adminhtml_Blog_CategoryController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Lesti_Blog_Adminhtml_Blog_CategoryController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('blog/category')
            ->_addBreadcrumb(Mage::helper('blog')->__('Blog'), Mage::helper('blog')->__('Category'))
            ->_addBreadcrumb(Mage::helper('blog')->__('Manage Categories'), Mage::helper('blog')->__('Manage Categories'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Categories'))
            ->_title($this->__('Manage Categories'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new category
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit category
     */
    public function editAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Category'))
            ->_title($this->__('Manage Categories'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('category_id');
        $model = Mage::getModel('blog/category');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blog')->__('This category no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Category'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('blog_category', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
            $id ? Mage::helper('blog')->__('Edit Category')
                : Mage::helper('blog')->__('New Category'),
            $id ? Mage::helper('blog')->__('Edit Category')
                : Mage::helper('blog')->__('New Category'));

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
            $model = Mage::getModel('blog/category');

            if ($id = $this->getRequest()->getParam('category_id')) {
                $model->load($id);
            }

            $model->setData($data);

            Mage::dispatchEvent('blog_category_prepare_save', array('category' => $model, 'request' => $this->getRequest()));


            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('The category has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('category_id' => $model->getId(), '_current'=>true));
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
                    Mage::helper('blog')->__('An error occurred while saving the category.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('category_id' => $this->getRequest()->getParam('category_id')));
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
        if ($id = $this->getRequest()->getParam('category_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('blog/category');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('cms')->__('The category has been deleted.'));
                // go to grid
                Mage::dispatchEvent('adminhtml_blog_category_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_blog_category_on_delete', array('title' => $title, 'status' => 'fail'));
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('category_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Unable to find a category to delete.'));
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
                return Mage::getSingleton('admin/session')->isAllowed('blog/category/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('blog/category/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('blog/category');
                break;
        }
    }
}