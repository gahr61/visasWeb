<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
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
}
