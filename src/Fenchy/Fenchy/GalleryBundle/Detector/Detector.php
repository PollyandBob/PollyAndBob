<?php

namespace Fenchy\GalleryBundle\Detector;

require_once(__DIR__.'/lib/FaceDetector.php');

class Detector
{
    public function resizeImage($file){
        
    if(file_exists($file)){
        $content = file_get_contents($file);
        list($width, $height) = getimagesize($file);
        
        if($width != $height){
            $detector = new \Face_Detector(__DIR__.'/lib/detection.dat');
            $detector->face_detect($file);
            $points = $detector->getFace();

            $x = $points['x'];
            $y = $points['y'];
            $w = $points['w'];
            if($w>35){
                $content = file_get_contents($file);
                $image = imagecreatefromstring($content);


                $left = $x;
                $top = $y;
                $right = $width - ($w + $x) ;
                $bottom = $height - ($w + $y);

                $width_size = $w + $left + $right;
                $height_size = $w + $top + $bottom;

                if($width_size > $height_size){
                    $maxw = $height_size;
                }elseif($width_size < $height_size){
                    $maxw = $width_size;
                }

                if($left < (($maxw - $w)/2)){
                    $start_x = 0;
                } elseif($right < (($maxw - $w)/2)) {
                    $start_x = $width_size - $maxw;
                } else {
                    $start_x = ($width_size - $maxw)/2;
                }

                if($top < (($maxw - $w)/2)){
                    $start_y = 0;

                } elseif($bottom < (($maxw - $w)/2)) {
                    $start_y = $width_size - $maxw;
                } else {
                    $start_y = ($y - ($width_size - $maxw)/2)/2;
                }

                $image_new = imagecreatetruecolor($maxw, $maxw);

                imagecopy( $image_new, $image, 0, 0, $start_x, $start_y, $maxw, $maxw );
                imagepng( $image_new, $file );

                imagedestroy( $image_new );

                imagedestroy( $image );
            }
            else{
                    $content = file_get_contents($file);
                    $image = imagecreatefromstring($content);
                    list($width, $height) = getimagesize($file);

                    $const_w = $width;
                    $const_h= $height;

                    $new_width = $width;
                    $new_height = $height;

                    if($width>$height){
                            $new_width = $height;
                            $const_w = $height;
                            $width = $height;
                    }
                    elseif($width<$height){
                            $new_height = $width;
                            $const_h = $width;
                            $height = $width;
                    }

                    $image_new = imagecreatetruecolor($const_w, $const_h);
                    imagecopyresized($image_new, $image, 0, 0, 0, 0, $new_width, $new_height,$width, $height);

                    imagecopy( $image_new, $image, 0, 0, 0, 0, $width, $height );
                    imagepng( $image_new, $file );

                    imagedestroy( $image_new );

                    imagedestroy( $image );
                }
            }
        }
    }
    
}
