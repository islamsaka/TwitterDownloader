<?php
namespace AnyDownloader\TwitterDownloader\Model;

use AnyDownloader\DownloadManager\Model\FetchedResource;

final class TwitterVideoFetchedResource extends FetchedResource
{
    /**
     * @return string
     */
    public function getExtSource(): string
    {
        return 'twitter';
    }
}

