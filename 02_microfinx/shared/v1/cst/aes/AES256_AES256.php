<?php
/**
 * AES.php
 * @author Andrey Izman <izmanw@gmail.com>
 * @copyright Andrey Izman (c) 2018
 * @license MIT
 */
//namespace mervick\aesEverywhere;
/**
 * Class AES256
 * @package mervick\aesEverywhere
 */
class AES256AES256
{
    public static $KEY = "vWFb7w2CwWqYF3nE9kfshDsdWhBHCrXTym9dPBbe5njzLg6zYzrP3uuXHF6eqeKfc5FfWCvHBZ8JgD47bPWwKLPms9UnNhQmr96g4vFpF9fsWm5fufJsbB2AtrWtHzcdyGJ5Zrg84NbBKKPDq8Rs8M4SEGu9dPr5hUgxE5R8FfzHuK5A7q2qfqJkuFj49K3zUYupEAaqD9Dc6cKeNHMEDjhV2ns6Uy33UwxHXzwa2FGLhpbcYZVB9UMV9wkzfpUj";
    /**
     * Encrypt string
     *
     * @param string|numeric $text
     * @param string $passphrase
     * @return string
     * @throws \Exception
     */
    public static function encrypt($text, $passphrase)
    {
        //$passphrase = "&^wVoIH4gh9cwviNaGGyaui07Tire5dofL0ZiKnEss";
        $salt = openssl_random_pseudo_bytes(8);
        $salted = $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        return base64_encode('Salted__' . $salt . openssl_encrypt($text . '', 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv));
    }
    /**
     * Decrypt string
     *
     * @param string $encrypted
     * @param string $passphrase
     * @return string|numeric
     * @throws \Exception
     */
    public static function decrypt($encrypted, $passphrase)
    {
        //$passphrase = "&^wVoIH4gh9cwviNaGGyaui07Tire5dofL0ZiKnEss";
        $encrypted = base64_decode($encrypted);
        $salted = substr($encrypted, 0, 8) == 'Salted__';
        if (!$salted) {
            return null;
        }
        $salt = substr($encrypted, 8, 8);
        $encrypted = substr($encrypted, 16);
        $salted = $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, true, $iv);
    }
}


?>

