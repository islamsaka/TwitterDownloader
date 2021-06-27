<?php
namespace AnyDownloader\TwitterDownloader;

use AnyDownloader\DownloadManager\Exception\NothingToExtractException;
use AnyDownloader\DownloadManager\Handler\BaseHandler;
use AnyDownloader\DownloadManager\Model\FetchedResource;
use AnyDownloader\DownloadManager\Model\ResourceItem\Image\JPGResourceItem;
use AnyDownloader\DownloadManager\Model\ResourceItem\Video\MP4ResourceItem;
use AnyDownloader\DownloadManager\Model\URL;
use AnyDownloader\TwitterDownloader\Exception\CanNotExtractMediaFromTwitter;
use AnyDownloader\TwitterDownloader\Model\Attribute\TwitterAuthorAttribute;
use AnyDownloader\DownloadManager\Model\Attribute\IdAttribute;
use AnyDownloader\DownloadManager\Model\Attribute\TextAttribute;
use AnyDownloader\TwitterDownloader\Model\Attribute\TwitterHashtagsAttribute;
use AnyDownloader\TwitterDownloader\Model\TwitterVideoFetchedResource;
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class TwitterHandler
 * @package AnyDownloader\TwitterDownloader
 */
final class TwitterHandler extends BaseHandler
{

    /**
     * @var string[]
     */
    protected $urlRegExPatterns = [
        '/(\/\/|www\.|)twitter\.com\/[a-zA-Z0-9]+\/status\/[0-9]+/s'
    ];

    /**
     * @var TwitterOAuth
     */
    private $client;

    /**
     * TwitterHandler constructor.
     * @param TwitterOAuth $client
     */
    public function __construct(TwitterOAuth $client)
    {
        $this->client = $client;
    }

    /**
     * @param URL $url
     * @return FetchedResource
     * @throws CanNotExtractMediaFromTwitter
     * @throws NothingToExtractException
     */
    public function fetchResource(URL $url): FetchedResource
    {
        $urlParts = explode("/", $url->getValue());
        $id = end($urlParts);

        $response = $this->client->get("statuses/show/{$id}");

        if ($response->errors) {
            throw new CanNotExtractMediaFromTwitter($response->errors[0]->message);
        }

        try {
            $media = $response->extended_entities->media[0];
            $preview = JPGResourceItem::fromURL(URL::fromString($media->media_url_https));
            $resource = new TwitterVideoFetchedResource($url);
            $resource->setImagePreview($preview);
            $resource->addAttribute(new IdAttribute($response->id));
            $resource->addAttribute(new TextAttribute($response->text));
            $resource->addAttribute(TwitterAuthorAttribute::fromTwitterUserStdObj($response->user));

            if ($response->entities) {
                $resource->addAttribute(TwitterHashtagsAttribute::fromTwitterEntitiesStdObj($response->entities));
            }

            if (is_array($media->video_info->variants) && !empty($media->video_info->variants)) {
                foreach ($media->video_info->variants as $video) {
                    if ($video->content_type === MP4ResourceItem::MIMEType()) {
                        $videoItem = MP4ResourceItem::fromURL(URL::fromString($video->url), $video->bitrate);
                        $resource->addItem($videoItem);
                    }
                }
                if (isset($videoItem) && $videoItem instanceof MP4ResourceItem) {
                    $resource->setVideoPreview($videoItem);
                }
            }

        } catch (\Exception $exception) {
            throw new NothingToExtractException();
        }

        return $resource;
    }

}