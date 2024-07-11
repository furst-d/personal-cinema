<?php

namespace App\Resolver;

use App\Exception\ApiException;
use App\Exception\BadRequestException;
use App\Helper\Api\ResponseEntity;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationResolver implements ValueResolverInterface
{
    /**
     * @var DenormalizerInterface $denormalizer
     */
    private DenormalizerInterface $denormalizer;

    /**
     * @var ValidatorInterface $validator
     */
    private ValidatorInterface $validator;

    /**
     * @var ResponseEntity $re
     */
    private ResponseEntity $re;

    public function __construct(
        DenormalizerInterface $denormalizer,
        ValidatorInterface $validator,
        ResponseEntity $re
    )
    {
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
        $this->re = $re;
    }

    /**
     * Is used to serialize and validate the DTO objects
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     * @throws ExceptionInterface
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!is_subclass_of($argument->getType(), 'App\DTO\AbstractRequest')) {
            return [];
        }

        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            $this->sendErrorResponse(new BadRequestException('Expected JSON data'));
        }

        try {
            $object = $this->denormalizer->denormalize($data, $argument->getType());
        } catch (NotEncodableValueException) {
            $this->sendErrorResponse(new BadRequestException('Invalid data format'));
        } catch (NotNormalizableValueException $e) {
            $this->sendErrorResponse(new BadRequestException($e->getMessage()));
        }

        $errors = $this->validator->validate($object);

        if (count($errors) > 0) {
            $errorDetails = [];
            foreach ($errors as $error) {
                /** @var ConstraintViolation $error */
                $errorDetails[] = [
                    'property' => $error->getPropertyPath(),
                    'value' => $error->getInvalidValue(),
                    'message' => $error->getMessage(),
                ];
            }
            $this->sendErrorResponse(new BadRequestException('Validation failed', $errorDetails));
        }

        yield $object;
    }

    /**
     * @param ApiException $ex
     * @return void
     */
    #[NoReturn] private function sendErrorResponse(ApiException $ex): void
    {
        $response = $this->re->withException($ex);
        $response->send();
        exit;
    }
}
