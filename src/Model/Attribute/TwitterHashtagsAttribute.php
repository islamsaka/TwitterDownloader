<?php
namespace AnyDownloader\TwitterDownloader\Model\Attribute;

use AnyDownloader\DownloadManager\Model\Attribute\HashtagsAttribute;

/**
 * Class TwitterHashtagsAttribute
 * @package AnyDownloader\TwitterDownloader\Model\Attribute
 */
class TwitterHashtagsAttribute extends HashtagsAttribute
{
    /**
     * @param \stdClass $entities
     * @return TwitterHashtagsAttribute
     */
    public static function fromTwitterEntitiesStdObj(\stdClass $entities): TwitterHashtagsAttribute
    {
        $hashtags = [];
        if (!$entities->hashtags || empty($entities->hashtags) || !is_array($entities->hashtags)) {
            return new self($hashtags);
        }
        foreach ($entities->hashtags as $hashtag) {
            $hashtags[] = $hashtag->text;
        }
        return new self($hashtags);
    }
}