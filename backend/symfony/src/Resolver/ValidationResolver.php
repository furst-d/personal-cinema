<?php

namespace App\Resolver;

use App\Exception\BadRequestException;
use App\Helper\Api\ResponseEntity;
use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
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

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (is_subclass_of($argument->getType(), 'App\DTO\Filter\FilterRequest')) {
            return $this->resolveFilter($request, $argument);
        }

        if (is_subclass_of($argument->getType(), 'App\DTO\AbstractQueryRequest')) {
            return $this->resolveQuery($request, $argument);
        }

        if (is_subclass_of($argument->getType(), 'App\DTO\AbstractRequest')) {
            return $this->resolveBody($request, $argument);
        }

        return [];
    }

    private function resolveFilter(Request $request, ArgumentMetadata $argument): iterable
    {
        $filterData = $request->query->get('filter');

        if (!$filterData) {
            if ($argument->isNullable()) {
                yield null;
                return;
            } else {
                $this->sendErrorResponse(new BadRequestException('Filter parameter is required'));
            }
        }

        try {
            $filterArray = json_decode($filterData, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->sendErrorResponse(new BadRequestException('Invalid JSON format in filter parameter'));
            }

            $object = $this->denormalizer->denormalize($filterArray, $argument->getType());
        } catch (NotNormalizableValueException | InvalidArgumentException $e) {
            $this->sendErrorResponse(new BadRequestException($e->getMessage()));
        } catch (Exception $e) {
            $this->sendErrorResponse(new BadRequestException('Invalid query parameters'));
        }

        $this->resolveValidation($object);

        yield $object;
    }

    private function resolveQuery(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = $this->convertQueryParameters($request->query->all());

        try {
            $object = $this->denormalizer->denormalize($data, $argument->getType());
        } catch (NotNormalizableValueException|InvalidArgumentException $e) {
            $this->sendErrorResponse(new BadRequestException($e->getMessage()));
        } catch (Exception $e) {
            $this->sendErrorResponse(new BadRequestException('Invalid query parameters'));
        }

        $this->resolveValidation($object);

        yield $object;
    }

    private function resolveBody(Request $request, ArgumentMetadata $argument): iterable
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            $this->sendErrorResponse(new BadRequestException('Expected JSON data'));
        }

        try {
            $object = $this->denormalizer->denormalize($data, $argument->getType());
        } catch (NotNormalizableValueException | InvalidArgumentException $e) {
            $this->sendErrorResponse(new BadRequestException($e->getMessage()));
        } catch (Exception $e) {
            $this->sendErrorResponse(new BadRequestException('Invalid data parameters'));
        }

        $this->resolveValidation($object);

        yield $object;
    }

    private function convertQueryParameters(array $queryParameters): array
    {
        foreach ($queryParameters as $key => $value) {
            if (is_numeric($value)) {
                $queryParameters[$key] = (int) $value;
            }
        }

        return $queryParameters;
    }

    private function sendErrorResponse(BadRequestException $ex): void
    {
        $response = $this->re->withException($ex);
        $response->send();
        exit;
    }

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
