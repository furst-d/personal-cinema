<?php

namespace App\Resolver;

use App\Exception\ApiException;
use App\Exception\BadRequestException;
use App\Helper\Api\ResponseEntity;
use Exception;
use InvalidArgumentException;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationResolver implements ValueResolverInterface
{
    private DenormalizerInterface $denormalizer;
    private ValidatorInterface $validator;
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
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     * @throws ExceptionInterface
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (is_subclass_of($argument->getType(), 'App\DTO\AbstractQueryRequest')) {
            return $this->resolveQuery($request, $argument);
        }

        if (is_subclass_of($argument->getType(), 'App\DTO\AbstractRequest')) {
            return $this->resolveBody($request, $argument);
        }

        return [];
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     * @throws ExceptionInterface
     */
    private function resolveBody(Request $request, ArgumentMetadata $argument): iterable
    {
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
        } catch (Exception) {
            $this->sendErrorResponse(new BadRequestException('Invalid data parameters'));
        }

        $this->resolveValidation($object);

        yield $object;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable
     * @throws ExceptionInterface
     */
    private function resolveQuery(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $this->convertQueryParameters($request->query->all());

        try {
            $object = $this->denormalizer->denormalize($data, $argument->getType());
        } catch (NotNormalizableValueException|InvalidArgumentException $e) {
            $this->sendErrorResponse(new BadRequestException($e->getMessage()));
        } catch (Exception) {
            $this->sendErrorResponse(new BadRequestException('Invalid query parameters'));
        }

        $this->resolveValidation($object);

        yield $object;
    }

    /**
     * @param array $queryParameters
     * @return array
     */
    private function convertQueryParameters(array $queryParameters): array
    {
        foreach ($queryParameters as $key => $value) {
            if (is_numeric($value)) {
                $queryParameters[$key] = (int) $value;
            }
        }

        return $queryParameters;
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

    /**
     * @param mixed $object
     */
    private function resolveValidation(mixed $object): void
    {
        $errors = $this->validator->validate($object);

        if (count($errors) > 0) {
            $errorDetails = [];
            foreach ($errors as $error) {
                $errorDetails[] = [
                    'property' => $error->getPropertyPath(),
                    'value' => $error->getInvalidValue(),
                    'message' => $error->getMessage(),
                ];
            }
            $this->sendErrorResponse(new BadRequestException('Validation failed', $errorDetails));
        }
    }
}
