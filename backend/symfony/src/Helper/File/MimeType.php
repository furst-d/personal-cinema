<?php

namespace App\Helper\File;

enum MimeType: string
{
    case IMAGE_PNG = 'image/png';
    case IMAGE_JPEG = 'image/jpeg';
    case VIDEO_MP4 = 'video/mp4';
    case AUDIO_MPEG = 'audio/mpeg';
    case APPLICATION_JSON = 'application/json';
    case APPLICATION_XML = 'application/xml';
    case APPLICATION_MPEGURL = 'application/vnd.apple.mpegurl';
    case TEXT_PLAIN = 'text/plain';
}
