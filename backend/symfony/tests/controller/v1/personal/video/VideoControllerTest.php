<?php

namespace App\Tests\Controller\V1\Personal\Video;

use App\DTO\Video\UploadRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Video;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Service\Cdn\CdnService;
use App\Service\Jwt\JwtService;
use App\Service\Video\VideoService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class VideoControllerTest extends WebTestCase
{
    private const VIDEO_DETAIL_URL = '/v1/personal/videos/';
    private const APPLICATION_JSON = ['CONTENT_TYPE' => 'application/json'];
    private const AUTHORIZATION_HEADER = ['HTTP_Authorization' => 'Bearer mock_token'];

    private $client;
    private $mockJwtService;
    private $mockCdnService;
    private $mockVideoService;
    private $mockEntityManager;
    private $mockAccountRepository;
    private $mockVideoRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->mockJwtService = $this->createMock(JwtService::class);
        $this->mockCdnService = $this->createMock(CdnService::class);
        $this->mockVideoService = $this->createMock(VideoService::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockAccountRepository = $this->createMock(EntityRepository::class);
        $this->mockVideoRepository = $this->createMock(EntityRepository::class);

        $this->client->getContainer()->set(JwtService::class, $this->mockJwtService);
        $this->client->getContainer()->set(CdnService::class, $this->mockCdnService);
        $this->client->getContainer()->set(VideoService::class, $this->mockVideoService);
        $this->client->getContainer()->set(EntityManagerInterface::class, $this->mockEntityManager);

        // Mock ClassMetadata to avoid uninitialized property error
        $mockMetadata = $this->createMock(ClassMetadata::class);
        $mockMetadata->name = Account::class;

        // Mock the find method of the account repository to return an Account object
        $account = $this->createMock(Account::class);
        $account->method('getId')->willReturn(1);

        $this->mockAccountRepository->method('find')->willReturn($account);

        // Mock the entity manager to return the mocked account repository
        $this->mockEntityManager->method('getRepository')->willReturnMap([
            [Account::class, $this->mockAccountRepository],
            [Video::class, $this->mockVideoRepository]
        ]);

        $this->mockEntityManager->method('getClassMetadata')->willReturn($mockMetadata);

        // Mock JWT service to decode the token and return the user ID
        $this->mockJwtService->method('decodeToken')->willReturn(['user_id' => 1]);
    }

    public function testGetVideoDetailNotFound()
    {
        $this->mockVideoService->method('getAccountVideoById')->willThrowException(new NotFoundException('Video not found'));

        $this->client->request('GET', self::VIDEO_DETAIL_URL . '999', [], [], array_merge(self::APPLICATION_JSON, self::AUTHORIZATION_HEADER));

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
}
