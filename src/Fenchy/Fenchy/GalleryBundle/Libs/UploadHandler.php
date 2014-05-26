<?php
namespace Fenchy\GalleryBundle\Libs;

use PunkAve\FileUploaderBundle\BlueImp\UploadHandler as base;
/**
 * Description of UploadHandler
 *
 * @author Michał Nowak <mnowak@pgs-soft.com>
 */
class UploadHandler extends base {

    protected $info = array();
    
    public function post() {
        $this->info = array();
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            return $this->delete();
        }
        $upload = isset($_FILES[$this->options['param_name']]) ?
            $_FILES[$this->options['param_name']] : null;

        if ($upload && is_array($upload['tmp_name'])) {
            // param_name is an array identifier like "files[]",
            // $_FILES is a multi-dimensional array:
            foreach ($upload['tmp_name'] as $index => $value) {
                $this->info[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],
                    uniqid().$index,
                    isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                        $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index],
                    isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                        $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index],
                    $upload['error'][$index],
                    $index
                );
            }
        } elseif ($upload || isset($_SERVER['HTTP_X_FILE_NAME'])) {
            // param_name is a single object identifier like "file",
            // $_FILES is a one-dimensional array:
            $this->info[] = $this->handle_file_upload(
                isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                uniqid(),
                isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                    $_SERVER['HTTP_X_FILE_SIZE'] : (isset($upload['size']) ?
                        $upload['size'] : null),
                isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                    $_SERVER['HTTP_X_FILE_TYPE'] : (isset($upload['type']) ?
                        $upload['type'] : null),
                isset($upload['error']) ? $upload['error'] : null
            );
        }
        header('Vary: Accept');
        $json = json_encode($this->info);
        $this->info['action'] = 'upload';
        $redirect = isset($_REQUEST['redirect']) ?
            stripslashes($_REQUEST['redirect']) : null;
        if ($redirect) {
            header('Location: '.sprintf($redirect, rawurlencode($json)));
            return;
        }
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
        echo $json;
    }
    
    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index) {
        
        $file = new \stdClass();
        $file->name = $this->trim_file_name($name, $type, $index);
        $file->size = intval($size);
        $file->type = $type;
        
        if ($this->validate($uploaded_file, $file, $error, $index)) {
            
            // do nothing ?!
            $this->handle_form_data($file, $index);
            
            // set flag. TRUE - override file, FALSE - create new one
            $file_path = $this->options['upload_dir'].$file->name;
            $append_file = !$this->options['discard_aborted_uploads'] &&
                is_file($file_path) && $file->size > filesize($file_path);
            
            clearstatcache();
            
            if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                // multipart/formdata uploads (POST method uploads)
                // override old file
                if ($append_file) {
                    file_put_contents(
                        $file_path,
                        fopen($uploaded_file, 'r'),
                        FILE_APPEND
                    );
                } 
                // create new file
                else {
                    move_uploaded_file($uploaded_file, $file_path);
                }
            } else {
                // Non-multipart uploads (PUT method support)
                file_put_contents(
                    $file_path,
                    fopen('php://input', 'r'),
                    $append_file ? FILE_APPEND : 0
                );
            }
            
            // If created file size is same as size of given file
            $file_size = filesize($file_path);
            if ($file_size === $file->size) {
                
            	if ($this->options['orient_image']) {
            		$this->orient_image($file_path);
            	}
                $file->url = $this->options['upload_url'].rawurlencode($file->name);
                foreach($this->options['image_versions'] as $version => $options) {
                    if ($this->create_scaled_image($file->name, $options)) {
                        if ($this->options['upload_dir'] !== $options['upload_dir']) {
                            $file->{$version.'_url'} = $options['upload_url']
                                .rawurlencode($file->name);
                        } else {
                            clearstatcache();
                            $file_size = filesize($file_path);
                        }
                    }
                }
                
            } else if ($this->options['discard_aborted_uploads']) {
                unlink($file_path);
                $file->error = 'abort';
            }
            $file->size = $file_size;
            $this->set_file_delete_url($file);
        }// end of if validate
        
        return $file;
    }
    
    public function delete() {
        $file_name = isset($_REQUEST['file']) ?
            basename(stripslashes($_REQUEST['file'])) : null;
        $file_path = $this->options['upload_dir'].$file_name;
        $success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
        if ($success) {
            foreach($this->options['image_versions'] as $version => $options) {
                $file = $options['upload_dir'].$file_name;
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        header('Content-type: application/json');
        echo json_encode($success);
        
        if($success) {
            $file = new \stdClass();
            $file->name = $file_name;
        }
        else {
            $file = FALSE;
        }
        $this->info['action'] = 'delete';
        $this->info[] = $file;
    }
    
    public function getInfo() {
        
        return $this->info;
    }
}

?>
