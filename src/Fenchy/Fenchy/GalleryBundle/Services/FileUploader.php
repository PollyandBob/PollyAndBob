<?php
namespace Fenchy\GalleryBundle\Services;

use PunkAve\FileUploaderBundle\Services\FileUploader as base;
use Fenchy\GalleryBundle\Libs\UploadHandler;
use Fenchy\GalleryBundle\Entity\Image;

/**
 * Description of FileUploader
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class FileUploader extends base {

    /**
     * 
     * @param type $options
     * @return type
     * @throws \Exception
     */
    public function handleFileUpload($options = array())
    {
                
        if (!isset($options['folder']))
        {
            throw new \Exception("You must pass the 'folder' option to distinguish this set of files from others");
        }
        
        if(isset($options['max']) && is_numeric($options['max'])) {
            $max = $options['max'];
        } else {
            $max = NULL;
        }

        $options = array_merge($this->options, $options);

        $allowedExtensions = $options['allowed_extensions'];

        // Build a regular expression like /(\.gif|\.jpg|\.jpeg|\.png)$/i
        $allowedExtensionsRegex = '/(' . implode('|', array_map(function($extension) { return '\.' . $extension; }, $allowedExtensions)) . ')$/i';

        $sizes = (isset($options['sizes']) && is_array($options['sizes'])) ? $options['sizes'] : array();

        $filePath = $options['file_base_path'] . '/' . $options['folder'];
        $webPath = $options['web_base_path'] . '/' . $options['folder'];

        foreach ($sizes as &$size)
        {
            $size['upload_dir'] = $filePath . '/' . $size['folder'] . '/';
            $size['upload_url'] = $webPath . '/' . $size['folder'] . '/';
        }

        $originals = $options['originals'];

        $uploadDir = $filePath . '/' . $originals['folder'] . '/';

        foreach ($sizes as &$size)
        {
            @mkdir($size['upload_dir'], 0777, true);
        }

        @mkdir($uploadDir, 0777, true);

        $upload_handler = new UploadHandler(
            array(
                'upload_dir' => $uploadDir, 
                'upload_url' => $webPath . '/' . $originals['folder'] . '/', 
                'script_url' => $options['request']->getUri(),
                'image_versions' => $sizes,
                'accept_file_types' => $allowedExtensionsRegex,
                'max_number_of_files' => $max
            ));

        // From https://github.com/blueimp/jQuery-File-Upload/blob/master/server/php/index.php
        // There's lots of REST fanciness here to support different upload methods, so we're
        // keeping the blueimp implementation which goes straight to the PHP standard library.
        // TODO: would be nice to port that code fully to Symfonyspeak.

        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
                break;
            case 'HEAD':
            case 'GET':
                $upload_handler->get();
                break;
            case 'POST':
                if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
                    $upload_handler->delete();
                } else {
                    $upload_handler->post();
                }
                break;
            case 'DELETE':
                $upload_handler->delete();
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }
        
        return $upload_handler->getInfo();
    }
    
    public function updateGallery($options) {
        
        $options = array_merge($this->options, $options);

        if (isset($options['remove_folder']) && $options['remove_folder']) {
            $remove = $options['file_base_path'] . '/' . $options['remove_folder'];
            system("rm -rf " . escapeshellarg($remove));
        }
        
        if (isset($options['from_folder']) && $options['from_folder'] && isset($options['to_folder']) && $options['to_folder']) {
            
            $from = $options['file_base_path'] . '/' . $options['from_folder'];
            $to = $options['file_base_path'] . '/' . $options['to_folder'];
            if(FALSE) { // for linux
                @mkdir($to, 0777, true);
                system("rsync -a --delete " . escapeshellarg($from . '/') . " " . escapeshellarg($to));
            } else { // for windows
                system('rm -r ' . escapeshellarg($to));
                system('cp -r ' . escapeshellarg($from . '') . " " . escapeshellarg($to));
            }
        }
    }
    
    /**
     * Creates cropped images from original one.
     * New images details can be set by file_uploader.crop config parameter.
     * 
     * Returns TRUE if all cropped images are created correctly.
     * 
     * @param \Fenchy\GalleryBundle\Entity\Image $image
     * @param int $x
     * @param int $y
     * @param int $w
     * @param int $h
     * @return boolean
     * 
     * @author Michał Nowak <mnowak@pgs-soft.com>
     */
    public function cropImage(Image $image, $x, $y, $w, $h) {

        $jpeg_quality = 100;

	$src = $this->options['file_base_path'].'/tmp/images/'.$image->getGallery()->getOriginal()->getId().'/originals/'.$image->getName();
        $img_r = imagecreatefromjpeg($src);

        foreach($this->options['crop'] as $crop) {
            
            $dir = $this->options['file_base_path'].'/tmp/images/'.$image->getGallery()->getOriginal()->getId().'/'.$crop['folder'];
            $target = $dir.'/'.$image->getName();
            // create new true color image
            $dst_r = ImageCreateTrueColor($crop['width'], $crop['height']);
            @mkdir($dir, 0777, true);
            imagecopyresampled($dst_r , $img_r , 0 , 0 , $x , $y , $crop['width'] , $crop['height'] , $w , $h);
            if(!imagejpeg($dst_r, $target, $jpeg_quality)) {
                return FALSE;
            }
        }
        
        return TRUE;
    }
}

?>
