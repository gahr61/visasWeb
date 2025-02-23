<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class GeneralController extends Controller
{
    /**
     * 
     */
    public function encriptString($simple_string){        
        // Store the cipher method
        $ciphering = "AES-128-CTR";
        
        // Use OpenSSl Encryption method
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;
        
        // Non-NULL Initialization Vector for encryption
        $encryption_iv = '5999034855919400';
        
        // Store the encryption key
        $encryption_key = "o2uREMGuJB/ErZXdGHScDA==";
        
        // Use openssl_encrypt() function to encrypt the data
        $encryption = openssl_encrypt($simple_string, $ciphering,
                    $encryption_key, $options, $encryption_iv);
        
        return $encryption;
    }

    /**
     * save image on storage
     * @param image $image
     * @param string $path
     * @param string $name
     * @return string
     */
    public function saveImageOnStorage($image, $path, $name){
        try{
           // $imagePath = storage_path().'\\app/'.$path;
            $imagePath = 'app/public/'.$path;

            $fullPath =storage_path().'/'. $imagePath.'/'.$name;

            $img = Image::read($image->getRealPath());

            $width = $img->width();
            $height = $img->height();

            if($width > $height){
                if($width > 600){
                    $width = $width / 2;
                    $height = $height / 2;
                }
            }else{
                if($width > 600){
                    $width = $width / 2;
                    $height = $height / 2;
                }
            }

            $newImage = $img->resize($width, $height);


            if(!Storage::disk('public')->exists($path)){
                Storage::disk('public')->makeDirectory($path);
            }

            $newImage->save($fullPath, 60);

            return 'saved';
        }catch(\Exception $e){
            return 'Error Image ('.$e->getCode().'): '.$e->getMessage().' '.$e->getLine();
        }
        
    }

    public function saveFileOnStorage($file, $path, $name){
        try{
            $file->storeAs($path, $name, 'public');

            return 'saved';
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
}
