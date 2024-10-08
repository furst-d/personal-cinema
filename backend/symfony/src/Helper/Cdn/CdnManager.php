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
     * @param int $quality
     * @return string
     * @throws InternalException
     */
    public function getManifestContent(Video $video, int $quality): string
    {
        try {
            $response = $this->client->get("{$this->cdnUrl}/videos/{$video->getCdnId()}/file.m3u8", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->cdnSecretKey,
                ],
                'query' => [
                    'quality' => $quality,
                ],
            ]);

            $body = $response->getBody()->getContents();
            if (empty($body)) {
                throw new InternalException('Invalid response from CDN');
            }

            return $body;
        } catch (RequestException|GuzzleException $e) {
            throw new InternalException('Error communicating with CDN: ' . $e->getMessage());
        }
    }

    /**
     * @param Video $video
     * @param int $thumbNumber
     * @return string
     * @throws InternalException
     */
    public function getThumbnailContent(Video $video, int $thumbNumber): string
    {
        try {
            $response = $this->client->get("{$this->cdnUrl}/videos/{$video->getCdnId()}/thumbs/{$thumbNumber}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->cdnSecretKey,
                ],
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new InternalException('Error retrieving thumbnail from CDN');
            }

            return $response->getBody()->getContents();
        } catch (RequestException|GuzzleException $e) {
            throw new InternalException('Error communicating with CDN: ' . $e->getMessage());
        }
    }

    /**
     * @param Video $video
     * @return array
     * @throws InternalException
     */
    public function getDownloadContent(Video $video): array
    {
        try {
            $response = $this->client->get("{$this->cdnUrl}/videos/{$video->getCdnId()}/download", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->cdnSecretKey,
                ],
            ]);

            $body = $response->getBody()->getContents();

            return json_decode($body, true);
        } catch (RequestException|GuzzleException $e) {
            throw new InternalException('Error communicating with CDN: ' . $e->getMessage());
        }
    }

    /**
     * @param Video[] $videos
     * @return void
     * @throws InternalException
     */
    public function batchDelete(array $videos): void
    {
        $cdnIds = array_map(fn(Video $video) => $video->getCdnId(), $videos);

        try {
            $this->client->delete("{$this->cdnUrl}/videos", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->cdnSecretKey,
                ],
                'json' => [
                    'videoIds' => $cdnIds,
                ]
            ]);
        } catch (RequestException|GuzzleException $e) {
            throw new InternalException('Error communicating with CDN: ' . $e->getMessage());
        }
    }
}
