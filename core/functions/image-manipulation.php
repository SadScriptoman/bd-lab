<?

    function prepare_e_photo($file, $save_to, $w, $h, $quality = 100) {
        list($width, $height, $type) = getimagesize($file);
        if ($w && $h && ($type == 1 || $type == 2 || $type == 3)){
            switch ($type) {
                case 1 :
                    $img = imageCreateFromGif($file);
                break;
                case 2 :
                    $img = imagecreatefromjpeg($file);
                break;
                case 3:
                    $img = imageCreateFromPng($file);
                break;
            } 
            if (empty($w)) {
                $w = ceil($h / ($height / $width));
            }
            if (empty($h)) {
                $h = ceil($w / ($width / $height));
            }
            $tmp = imageCreateTrueColor($w, $h);
            if ($type == 1 || $type == 3) {
                imagealphablending($tmp, true); 
                imageSaveAlpha($tmp, true);
                $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127); 
                imagefill($tmp, 0, 0, $transparent); 
                imagecolortransparent($tmp, $transparent);    
            }   
            $tw = ceil($h / ($height / $width));
            $th = ceil($w / ($width / $height));
            if ($tw < $w) {
                imageCopyResampled($tmp, $img, ceil(($w - $tw) / 2), 0, 0, 0, $tw, $h, $width, $height);        
            } else {
                imageCopyResampled($tmp, $img, 0, ceil(($h - $th) / 2), 0, 0, $w, $th, $width, $height);    
            }            
            imagejpeg($tmp, $save_to, $quality);
            imagedestroy($img);
            return $tmp;
        }
        return FALSE;
    }
