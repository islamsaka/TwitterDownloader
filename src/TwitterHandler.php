<?php
namespace AnyDownloader\TwitterDownloader;

use AnyDownloader\DownloadManager\Exception\NothingToExtractException;
use AnyDownloader\DownloadManager\Exception\NotValidUrlException;
use AnyDownloader\DownloadManager\Handler\BaseHandler;
use AnyDownloader\DownloadManager\Model\FetchedResource;
use AnyDownloader\DownloadManager\Model\ResourceItem\ResourceItemFactory;
use AnyDownloader\DownloadManager\Model\ResourceItem\VideoResourceItem;
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
        '/(\/\/|www\.|)twitter\.com\/[a-zA-Z0-9]+\/status\/[0-9]+/',
        '/(\/\/|www\.|)twitter\.com\/[a-zA-Z0-9]+\/status\/[0-9]+\/(.*)/',
        '/(\/\/|www\.|)t\.co\/[a-zA-Z0-9]+/'
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
     * @throws NotValidUrlException
     */
    public function fetchResource(URL $url): FetchedResource
    {
        $realUrl = clone $url;
        $realUrl->followLocation();

        if (!$this->isValidUrl($realUrl)) {
            throw new NotValidUrlException();
        }

        preg_match("/status\/[0-9]+/", $realUrl->getValue(), $twitId);

        if (empty($twitId)) {
            throw new NotValidUrlException();
        }

        $twitId = ltrim($twitId[0], 'status/');
        $response = $this->client->get("statuses/show/{$twitId}");
        if ($response->errors) {
            throw new CanNotExtractMediaFromTwitter($response->errors[0]->message);
        }

        if (!$response->extended_entities) {
            throw new NothingToExtractException();
        }

        $resource = new TwitterVideoFetchedResource($origUrl);
        $media = $response->extended_entities->media[0];
        if ($preview = ResourceItemFactory::fromURL(URL::fromString($media->media_url_https))) {
            $resource->setImagePreview($preview);
        }

        $resource->addAttribute(new IdAttribute($response->id));
        $resource->addAttribute(new TextAttribute($response->text));
        $resource->addAttribute(TwitterAuthorAttribute::fromTwitterUserStdObj($response->user));

        if ($response->entities) {
            $resource->addAttribute(TwitterHashtagsAttribute::fromTwitterEntitiesStdObj($response->entities));
        }

        if (is_array($media->video_info->variants) && !empty($media->video_info->variants)) {
            foreach ($media->video_info->variants as $video) {
                if (!$video->url) {
                    continue;
                }
                if ($videoItem = ResourceItemFactory::fromURL(URL::fromString($video->url), (string)$video->bitrate)) {
                    $resource->addItem($videoItem);
                }
            }
            if (isset($videoItem) && $videoItem instanceof VideoResourceItem) {
                $resource->setVideoPreview($videoItem);
            }
        }

        return $resource;
    }

}