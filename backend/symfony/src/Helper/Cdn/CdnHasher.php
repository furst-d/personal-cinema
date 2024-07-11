<?php

namespace App\Helper\Cdn;

class CdnHasher
{
    /**
     * @param array $data
     * @param string $secretKey
     * @return void
     */
    public function addSignature(array &$data, string $secretKey): void
    {
        ksort($data, SORT_STRING);
        $paramString = http_build_query($data);
        $data['signature'] = hash_hmac('sha256', $paramString, $secretKey);
    }
}
