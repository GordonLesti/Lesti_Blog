<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 26.03.13
 * Time: 23:04
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function initControllerRouters($observer)
    {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        $front->addRouter('blog', $this);
    }

    /**
     * Validate and Match Blog Page and modify request
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

        $identifier = trim($request->getPathInfo(), '/');

        $condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue'   => true
        ));
        Mage::dispatchEvent('blog_controller_router_match_before', array(
            'router'    => $this,
            'condition' => $condition
        ));
        $identifier = $condition->getIdentifier();

        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }

        if (!$condition->getContinue()) {
            return false;
        }

        $router = Mage::getStoreConfig(Lesti_Blog_Model_Post::XML_PATH_BLOG_GENERAL_ROUTER);
        if(!$router) {
            $router = 'blog';
        }
        if($identifier == $router) {
            $request->setModuleName('blog')
                ->setControllerName('index')
                ->setActionName('index');
            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $identifier
            );

            return true;
        }
        $identifierExplode = array_map('mysql_real_escape_string', explode('/', $identifier));

        if(!isset($identifierExplode[0]) || $identifierExplode[0] != $router) {
            return false;
        }

        if(isset($identifierExplode[1])) {
            $post = Mage::getModel('blog/post');
            $postId = $post->checkIdentifier($identifierExplode[1], Mage::app()->getStore()->getId());
            if ($postId) {
                $request->setModuleName('blog')
                    ->setControllerName('post')
                    ->setActionName('view')
                    ->setParam('post_id', $postId);
            } else if(isset($identifierExplode[2])){
                // check if category is called
                if($identifierExplode[1] == 'category') {
                    $category = Mage::getModel('blog/category');
                    $categoryId = $category->checkIdentifier($identifierExplode[2], Mage::app()->getStore()->getId());
                    if($categoryId) {
                        $request->setModuleName('blog')
                            ->setControllerName('category')
                            ->setActionName('view')
                            ->setParam('category_id', $categoryId);
                    } else {
                        return false;
                    }
                } else {
                    if($identifierExplode[1] == 'tag') {
                        $tag = Mage::getModel('blog/tag');
                        $tagId = $tag->checkIdentifier($identifierExplode[2], Mage::app()->getStore()->getId());
                        if($tagId) {
                            $request->setModuleName('blog')
                                ->setControllerName('tag')
                                ->setActionName('view')
                                ->setParam('tag_id', $tagId);
                        } else {
                            return false;
                        }
                    } else {
                        if($identifierExplode[1] == 'author') {
                            $author = Mage::getModel('blog/author');
                            if($author->exists($identifierExplode[2], Mage::app()->getStore()->getId())) {
                                $request->setModuleName('blog')
                                    ->setControllerName('author')
                                    ->setActionName('view')
                                    ->setParam('author', $identifierExplode[2]);
                            } else {
                                return false;
                            }
                        } else {
                            // archive-shit
                        }
                    }
                }
            }
        }

        $request->setAlias(
            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $identifier
        );

        return true;
    }
}