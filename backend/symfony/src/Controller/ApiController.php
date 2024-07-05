<?php

namespace App\Controller;

use App\Helper\Api\ResponseEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @var ResponseEntity $re
     */
    protected ResponseEntity $re;

    protected EntityManagerInterface $em;

    /**
     * @param ResponseEntity $re
     * @param EntityManagerInterface $em
     */
    public function __construct(ResponseEntity $re, EntityManagerInterface $em)
    {
        $this->re = $re;
        $this->em = $em;
    }
}
