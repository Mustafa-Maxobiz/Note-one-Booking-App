<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait Encryptable
{
    /**
     * Encrypt a value.
     */
    public function encrypt($value)
    {
        if ($value === null) {
            return null;
        }
        
        if (is_array($value)) {
            return Crypt::encrypt($value);
        }
        
        return Crypt::encryptString($value);
    }

    /**
     * Decrypt a value.
     */
    public function decrypt($value)
    {
        try {
            if ($value === null) {
                return null;
            }
            
            // Try to decrypt as string first
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                // If that fails, try to decrypt as array
                return Crypt::decrypt($value);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get encrypted attributes.
     */
    public function getEncryptedAttributes()
    {
        return $this->encrypted ?? [];
    }


    /**
     * Get an attribute from the model.
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        
        if (in_array($key, $this->getEncryptedAttributes()) && $value !== null) {
            return $this->decrypt($value);
        }
        
        return $value;
    }

    /**
     * Set a given attribute on the model.
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getEncryptedAttributes()) && $value !== null) {
            $value = $this->encrypt($value);
        }
        
        return parent::setAttribute($key, $value);
    }
}
