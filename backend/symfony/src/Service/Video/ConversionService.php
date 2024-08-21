<?php

namespace App\Service\Video;

use App\DTO\Admin\Video\ConversionRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Video\Conversion;
use App\Entity\Video\Video;
use App\Exception\NotFoundException;
use App\Helper\DTO\PaginatorResult;
use App\Repository\Video\ConversionRepository;

class ConversionService
{
    /**
     * @var ConversionRepository $conversionRepository
     */
    private ConversionRepository $conversionRepository;

    /**
     * @param ConversionRepository $conversionRepository
     */
    public function __construct(ConversionRepository $conversionRepository)
    {
        $this->conversionRepository = $conversionRepository;
    }

    /**
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Conversion>
     */
    public function getConversions(PaginatorRequest $paginatorRequest): PaginatorResult
    {
        return $this->conversionRepository->findConversions($paginatorRequest);
    }

    /**
     * @param int $id
     * @return Conversion
     * @throws NotFoundException
     */
    public function getConversionById(int $id): Conversion
    {
        $conversion = $this->conversionRepository->find($id);

        if (!$conversion) {
            throw new NotFoundException('Conversion not found');
        }

        return $conversion;
    }

    /**
     * @param Video $video
     * @param array $heights
     * @return Conversion[]
     */
    public function getUnusedConversions(Video $video, array $heights): array
    {
        return $this->conversionRepository->findUnusedConversions($video, $heights);
    }

    /**
     * @param Conversion $conversion
     * @param ConversionRequest $conversionRequest
     * @return void
     */
    public function updateConversion(Conversion $conversion, ConversionRequest $conversionRequest): void
    {
        $conversion->setWidth($conversionRequest->width);
        $conversion->setHeight($conversionRequest->height);
        $conversion->setBandwidth($conversionRequest->bandwidth);
        $this->conversionRepository->save($conversion);
    }

    /**
     * @param Conversion $conversion
     * @return void
     */
    public function deleteConversion(Conversion $conversion): void
    {
        $this->conversionRepository->delete($conversion);
    }

    /**
     * @param int[] $ids
     * @return Conversion[]
     * @throws NotFoundException
     */
    public function getConversionsByIds(array $ids): array
    {
        $conversions = $this->conversionRepository->findByIds($ids);

        if (count($conversions) !== count($ids)) {
            throw new NotFoundException("Some conversions not found.");
        }

        return $conversions;
    }

    /**
     * @param ConversionRequest $conversionRequest
     * @return Conversion
     */
    public function createConversion(ConversionRequest $conversionRequest): Conversion
    {
        $conversion = new Conversion($conversionRequest->width, $conversionRequest->height, $conversionRequest->bandwidth);
        $this->conversionRepository->save($conversion);

        return $conversion;
    }
}
