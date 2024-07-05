<?php

namespace App\Controller;

use App\Helper\Api\ResponseEntity;
use App\Service\Locator\BaseControllerLocator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * @var ResponseEntity $re
     */
    protected ResponseEntity $re;

    /**
     * @var EntityManagerInterface $em
     */
    protected EntityManagerInterface $em;

    /**
     * @var SerializerInterface $serializer
     */
    protected SerializerInterface $serializer;

    /**
     * @param BaseControllerLocator $locator
     */
    public function __construct(BaseControllerLocator $locator)
    {
        $this->re = $locator->getResponseEntity();
        $this->em = $locator->getEntityManager();
        $this->serializer = $locator->getSerializer();
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    protected function serialize(mixed $data): mixed
    {
        return json_decode($this->serializer->serialize($data, 'json', ['groups' => 'serialize']), true);
    }
}
