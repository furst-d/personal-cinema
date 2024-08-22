<?php

namespace App\Controller\V1\Admin\Video;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Admin\Video\ConversionRequest;
use App\DTO\Admin\Video\VideoConversionQueryRequest;
use App\DTO\Filter\BatchDeleteFilterRequest;
use App\Entity\Video\Conversion;
use App\Exception\ApiException;
use App\Helper\Regex\RegexRoute;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Video\ConversionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/videos/conversions')]
class VideoConversionController extends BasePersonalController
{
    /**
     * @var ConversionService $conversionService
     */
    private ConversionService $conversionService;

    /**
     * @param BaseControllerLocator $locator
     * @param ConversionService $conversionService
     */
    public function __construct(
        BaseControllerLocator $locator,
        ConversionService $conversionService
    )
    {
        parent::__construct($locator);
        $this->conversionService = $conversionService;
    }

    #[Route('', name: 'admin_conversions', methods: ['GET'])]
    public function getConversions(VideoConversionQueryRequest $conversionQueryRequest): JsonResponse
    {
        $conversions = $this->conversionService->getConversions($conversionQueryRequest);
        return $this->re->withData($conversions, [Conversion::CONVERSION_READ]);
    }

    #[Route('', name: 'admin_conversion_create', methods: ['POST'])]
    public function createConversion(ConversionRequest $conversionRequest): JsonResponse
    {
        $conversion = $this->conversionService->createConversion($conversionRequest);
        return $this->re->withData($conversion, [Conversion::CONVERSION_READ]);
    }

    #[Route('', name: 'admin_conversions_batch_delete', methods: ['DELETE'])]
    public function batchDelete(BatchDeleteFilterRequest $filter): JsonResponse
    {
        try {
            $conversions = $this->conversionService->getConversionsByIds($filter->ids);

            foreach ($conversions as $conversion) {
                $this->conversionService->deleteConversion($conversion);
            }

            return $this->re->withData($conversions, [Conversion::CONVERSION_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID, name: 'admin_conversion', methods: ['GET'])]
    public function getConversionDetail(int $id): JsonResponse
    {
        try {
            $conversion = $this->conversionService->getConversionById($id);
            return $this->re->withData($conversion, [Conversion::CONVERSION_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID, name: 'admin_conversion_update', methods: ['PUT'])]
    public function updateConversion(int $id, ConversionRequest $conversionRequest): JsonResponse
    {
        try {
            $conversion = $this->conversionService->getConversionById($id);
            $this->conversionService->updateConversion($conversion,$conversionRequest);
            return $this->re->withData($conversion, [Conversion::CONVERSION_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route(RegexRoute::ID, name: 'admin_conversion_delete', methods: ['DELETE'])]
    public function deleteConversion(int $id): JsonResponse
    {
        try {
            $conversion = $this->conversionService->getConversionById($id);
            $this->conversionService->deleteConversion($conversion);
            return $this->re->withMessage('Conversion deleted successfully');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
