<?php

namespace App\Service\Locator;

use App\Helper\Api\ResponseEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class BaseControllerLocator
{
    /**
     * @var ResponseEntity $re
     */
    private ResponseEntity $re;

    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @var SerializerInterface $serializer
     */
    private SerializerInterface $serializer;

    public function __construct(ResponseEntity $re, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->re = $re;
        $this->em = $em;
        $this->serializer = $serializer;
    }

    public function getResponseEntity(): ResponseEntity
    {
        return $this->re;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }
}
