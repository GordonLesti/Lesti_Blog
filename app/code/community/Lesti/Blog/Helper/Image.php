<?php

/**
 * Class Lesti_Blog_Helper_Image
 *
 *  For resizing post images
 *
 *  TODO: hook into cache system to remove files when cleared
 */
class Lesti_Blog_Helper_Image extends Mage_Core_Helper_Abstract
{

    //location in media folder to store images
    const IMAGE_DIR = 'blog';


    /**
     * Resize image and return associative array of data
     *
     * @param $post
     * @param $width
     * @param $height
     * @return array|void
     */
    public function resize($post, $width, $height = null) {

        $imgData = array();

        if (!$post->getMainImage()) {
            return;
        }
        // filesystem path of image
        $originalImgPath = $post->getMainImageFilePath();

        $imgFile = $width . '-' . $height . '-' . str_replace(self::IMAGE_DIR . DS, '', $post->getMainImage());

        $resizedFile = Mage::getBaseDir('media') . DS . self::IMAGE_DIR . DS . "resized" . DS . $imgFile;

        if (!file_exists($resizedFile) && file_exists($originalImgPath)) {
            try {
                $imageObj = new Varien_Image($originalImgPath);
                $imageObj->keepFrame(false);
                $imageObj->keepAspectRatio(true);
                $imageObj->constrainOnly(true);
                $imageObj->resize($width, $height);
                $imageObj->save($resizedFile);
                $imgData['width'] = $imageObj->getOriginalWidth();
                $imgData['height'] = $imageObj->getOriginalHeight();
            } catch (Exception $e) {
                Mage::logException($e);
                $imgData['url'] = $post->getMainImageUrl();
            }
        } else {
            $imageObj = new Varien_Image($resizedFile);
            $imgData['width'] = $imageObj->getOriginalWidth();
            $imgData['height'] = $imageObj->getOriginalHeight();
        }

        $imgData['url'] = Mage::getUrl('media') . self::IMAGE_DIR . DS . "resized" . DS . $imgFile;
        return $imgData;
    }

    /**
     * Get HTML for a resized post image
     * @param $post
     * @param $width
     * @param null $height
     * @param array $attributes
     * @return string
     */
    public function getPostImage($post, $width, $height = null, $attributes = array()) {

        $imgData = $this->resize($post, $width, $height);
        if ($imgData) {
            $attributes['src'] = $imgData['url'];
            $attributes['width'] = $imgData['width'];
            $attributes['height'] = $imgData['height'];
            $attributes['alt'] = Mage::helper('core')->quoteEscape($post->getTitle());

            $atts = '';
            foreach ($attributes as $key => $value) {
                $atts .= $key . '="' . $value . '" ';
            }
            $html = '<img  ' . $atts . ' />';

            return $html;
        }
    }
}