<?php

namespace App\Controller;

use App\Helper\Api\ResponseEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * @var ResponseEntity $re
     */
    protected ResponseEntity $re;

    /**
     * @param ResponseEntity $re
     */
    public function __construct(ResponseEntity $re)
    {
        $this->re = $re;
    }
}
