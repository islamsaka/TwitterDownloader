<?php
namespace AnyDownloader\TwitterDownloader\Model\Attribute;

use AnyDownloader\DownloadManager\Model\Attribute\AuthorAttribute;
use AnyDownloader\DownloadManager\Model\URL;

/**
 * Class TwitterAuthorAttribute
 * @package AnyDownloader\TwitterDownloader\Model\Attribute
 */
class TwitterAuthorAttribute extends AuthorAttribute
{

    /**
     * @param \stdClass $user
     * @return TwitterAuthorAttribute
     */
    public static function fromTwitterUserStdObj(\stdClass $user): TwitterAuthorAttribute
    {
        $id = $user->id ?? '';
        $fullName = $user->name ?? '';
        $nickname = $user->screen_name ?? '';
        try {
            $avatar = URL::fromString($user->profile_image_url_https);
        } catch (\Exception $e) {
            $avatar = null;
        }

        return new self($id, $nickname, $fullName, $avatar);
    }
}