<?php

namespace App\Helpers;

class ImageHelpers
{
    private function __construct()
    {
    }

    public static function getInstance()
    {
        return new self();
    }

    /**
     * @param string $folder
     * @param $image
     * @return string
     */
    public function getImageUrl(string $folder, $image)
    {
        if ($image)
            return get_baseUrl() . '/images/' . $folder . '/' . $image;
        return get_baseUrl() . '/images/1.png';
    }

    /**
     * @param $folder
     * @param $file
     * @return string
     */
    public function saveImage($folder, $file)
    {
        $image = $file;
        $input['image'] = mt_rand() . time() . '.' . $image->getClientOriginalExtension();
        $dist = public_path('/images/' . $folder . '/');
        $image->move($dist, $input['image']);
        return $input['image'];
    }

    /**
     * @param $folder
     * @param $file
     * @return int
     */
    public function deleteFile($folder, $file)
    {
        $file = public_path('/images/' . $folder . '/' . $file);
        if (file_exists($file)) {
            File::delete($file);
        }
        return 1;
    }
}
