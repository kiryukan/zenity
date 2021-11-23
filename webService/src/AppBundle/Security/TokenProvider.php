<?php


namespace AppBundle\Security;

class TokenProvider
{
    private $encryptionAlgorithm;
    private $tokenDuration;
    private $secretKey;
    public function __construct($encryptionAlgorithm,$tokenDuration,$secretKey){
        $this->encryptionAlgorithm = $encryptionAlgorithm;
        $this->tokenDuration = new \DateInterval($tokenDuration);
        $this->secretKey = $secretKey;
    }

    public function generateToken($clientFingerPrint,$clientId,$dateTime){
        return hash(
            $this->encryptionAlgorithm,
            $clientFingerPrint.$clientId.date_format($dateTime, 'Y-m-d H:i:s').$this->secretKey
        );
    }
    public function validateToken($token,$clientFingerPrint,$clientId,$dateTime){
        if ( date_add($dateTime,$this->tokenDuration) < new \DateTime('now')){//the token has expired
            return false;
        }elseif ($token === $this->generateToken($clientFingerPrint,$clientId,$dateTime)){
            return true;
        }else{
            return false;
        }
    }
}