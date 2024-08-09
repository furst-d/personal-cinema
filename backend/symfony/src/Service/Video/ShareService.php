<?php

namespace App\Service\Video;

use App\Repository\Video\Share\ShareFolderRepository;
use App\Repository\Video\Share\ShareVideoPublicRepository;
use App\Repository\Video\Share\ShareVideoPublicViewRepository;
use App\Repository\Video\Share\ShareVideoRepository;

class ShareService
{
    /**
     * @var ShareFolderRepository $shareFolderRepository
     */
    private ShareFolderRepository $shareFolderRepository;

    /**
     * @var ShareVideoPublicRepository $shareVideoPublicRepository
     */
    private ShareVideoPublicRepository $shareVideoPublicRepository;

    /**
     * @var ShareVideoPublicViewRepository $shareVideoPublicViewRepository
     */
    private ShareVideoPublicViewRepository $shareVideoPublicViewRepository;

    /**
     * @var ShareVideoRepository $shareVideoRepository
     */
    private ShareVideoRepository $shareVideoRepository;

    /**
     * @param ShareFolderRepository $shareFolderRepository
     * @param ShareVideoPublicRepository $shareVideoPublicRepository
     * @param ShareVideoPublicViewRepository $shareVideoPublicViewRepository
     * @param ShareVideoRepository $shareVideoRepository
     */
    public function __construct(
        ShareFolderRepository $shareFolderRepository,
        ShareVideoPublicRepository $shareVideoPublicRepository,
        ShareVideoPublicViewRepository $shareVideoPublicViewRepository,
        ShareVideoRepository $shareVideoRepository
    )
    {
        $this->shareFolderRepository = $shareFolderRepository;
        $this->shareVideoPublicRepository = $shareVideoPublicRepository;
        $this->shareVideoPublicViewRepository = $shareVideoPublicViewRepository;
        $this->shareVideoRepository = $shareVideoRepository;
    }
}
