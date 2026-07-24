<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Legacy passphrase the frontend used (CryptoJS AES) to pre-encrypt this
     * column before saving. Needed once to recover the real value for rows
     * saved before password encryption moved server-side.
     */
    private string $legacyFrontendSecret = 'm4~3R3/7I5sU1d=*p5V0';

    public function up(): void
    {
        $accounts = DB::table('sales_process_account')->get(['id', 'password']);

        foreach ($accounts as $account) {
            if (empty($account->password)) {
                continue;
            }

            if ($this->isAlreadyLaravelEncrypted($account->password)) {
                continue;
            }

            $plain = $this->decryptLegacyCryptoJs($account->password, $this->legacyFrontendSecret);

            if ($plain === false) {
                $plain = $account->password;
            }

            DB::table('sales_process_account')
                ->where('id', $account->id)
                ->update(['password' => Crypt::encryptString($plain)]);
        }
    }

    public function down(): void
    {
        // Not reversible: original plaintext/legacy ciphertext is not preserved on purpose.
    }

    private function isAlreadyLaravelEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function decryptLegacyCryptoJs(string $base64, string $passphrase): string|false
    {
        $data = base64_decode($base64, true);

        if ($data === false || substr($data, 0, 8) !== 'Salted__') {
            return false;
        }

        $salt = substr($data, 8, 8);
        $ciphertext = substr($data, 16);

        [$key, $iv] = $this->evpKDF($passphrase, $salt);

        $plain = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return $plain === false ? false : $plain;
    }

    private function evpKDF(string $password, string $salt, int $keySize = 32, int $ivSize = 16): array
    {
        $targetSize = $keySize + $ivSize;
        $derived = '';
        $block = null;

        while (strlen($derived) < $targetSize) {
            $block = md5(($block ?? '') . $password . $salt, true);
            $derived .= $block;
        }

        return [substr($derived, 0, $keySize), substr($derived, $keySize, $ivSize)];
    }
};
