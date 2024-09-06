<?php

namespace App\Controller;

use App\Helper\Api\ResponseEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Service\Attribute\Required;

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
     * @return void
     */
    #[Required]
    public function setResponseEntity(ResponseEntity $re): void
    {
        $this->re = $re;
    }

    /**
     * @param EntityManagerInterface $em
     * @return void
     */
    #[Required]
    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    /**
     * @param SerializerInterface $serializer
     * @return void
     */
    #[Required]
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * @param mixed $data
     * @param array $groups
     * @return mixed
     */
    protected function serialize(mixed $data, array $groups = []): mixed
    {
        return json_decode($this->serializer->serialize($data, 'json', [
            'groups' => $groups,
            'enable_max_depth' => true
        ]), true);
    }
}
