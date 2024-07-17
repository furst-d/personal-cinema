<?php

namespace App\Helper\Cdn;

use App\Entity\Video\Video;
use App\Exception\InternalException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class CdnManager
{
    /**
     * @var string $cdnSecretKey
     */
    private string $cdnSecretKey;

    /**
     * @var string $cdnUrl
     */
    private string $cdnUrl;

    /**
     * @var Client $client
     */
    private Client $client;

    /**
     * @param string $cdnSecretKey
     * @param string $cdnUrl
     */
    public function __construct(string $cdnSecretKey, string $cdnUrl)
    {
        $this->cdnSecretKey = $cdnSecretKey;
        $this->cdnUrl = $cdnUrl;
        $this->client = new Client();
    }

    /**
     * @param Client $client
     * @return void
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @param Video $video
     * @return string
     * @throws InternalException
     * @throws GuzzleException
     */
    public function getManifestContent(Video $video): string
    {
        try {
            $response = $this->client->get("{$this->cdnUrl}/videos/{$video->getCdnId()}/file.m3u8", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->cdnSecretKey,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new InternalException('Error retrieving manifest from CDN');
            }

            $body = $response->getBody()->getContents();
            if ($response->getStatusCode() !== 200 || empty($body)) {
                throw new InternalException('Invalid response from CDN');
            }

            return $body;
        } catch (RequestException $e) {
            throw new InternalException('Error communicating with CDN: ' . $e->getMessage());
        }
    }
}
