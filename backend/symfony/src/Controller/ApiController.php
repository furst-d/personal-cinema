<?php

namespace App\Controller;

use App\Helper\Api\ResponseEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Serializer;
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
     * @param ResponseEntity $re
     * @param EntityManagerInterface $em
     * @param SerializerInterface $serializer
     */
    public function __construct(ResponseEntity $re, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->re = $re;
        $this->em = $em;
        $this->serializer = $serializer;
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
