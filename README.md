# TwitterDownloader
Get video sources in different qualities with preview image from Twitter

## IMPORTANT: You need to register Twitter application
## and provide CONSUMER KEY & CONSUMER SECRET

Install via Composer
```
composer require any-downloader/twitter-downloader
```

You have two options of how to use this package

1. Use it standalone

```
<?php
use AnyDownloader\TwitterDownloader\TwitterHandler;
use Abraham\TwitterOAuth\TwitterOAuth;
use AnyDownloader\DownloadManager\Model\URL;

include_once 'vendor/autoload.php';

$consumerKey = '<CONSUMER KEY>';
$consumerSecret = '<CONSUMER SECRET>';
$client = new TwitterOAuth($consumerKey, $consumerSecret);

$twitterHandler = new TwitterHandler($client);
$tweetUrl = URL::fromString('https://twitter.com/PassengersMovie/status/821025484150423557');
$media = $twitterHandler->fetchResource($tweetUrl);

print_r($media->toArray());
/**
Array
(
    [source_url] => https://twitter.com/PassengersMovie/status/821025484150423557
    [preview_image] => Array
        (
            [type] => image
            [format] => jpg
            [quality] =>
            [url] => https://pbs.twimg.com/media/C2GF3vxUQAArTD0.jpg
            [mime_type] => image/jpg
        )

    [preview_video] => Array
        (
            [type] => video
            [format] => mp4
            [quality] => 832000
            [url] => https://video.twimg.com/amplify_video/820082508054179840/vid/480x480/JypbCoP9FFOf1IgE.mp4
            [mime_type] => video/mp4
        )

    [attributes] => Array
        (
            [id] => 821025484150423557
            [text] => Plan your escape aboard the Starship Avalon with #PassengersMovie - see it today! 🚀 🚀 🚀 https://t.co/dlNC50FhBu https://t.co/X0go99a4hO
            [author] => Array
                (
                    [id] => 3651083671
                    [avatar_url] => https://pbs.twimg.com/profile_images/834102207356116993/Z0dFwGnF_normal.jpg
                    [full_name] => Passengers Movie
                    [nickname] => PassengersMovie
                )

            [hashtags] => Array
                (
                    [0] => PassengersMovie
                )

        )

    [items] => Array
        (
            [0] => Array
                (
                    [type] => video
                    [format] => mp4
                    [quality] => 320000
                    [url] => https://video.twimg.com/amplify_video/820082508054179840/vid/240x240/b6ImBrQddohap5-6.mp4
                    [mime_type] => video/mp4
                )

            [1] => Array
                (
                    [type] => video
                    [format] => mp4
                    [quality] => 1280000
                    [url] => https://video.twimg.com/amplify_video/820082508054179840/vid/720x720/K8BEWmSeNsrQI_pA.mp4
                    [mime_type] => video/mp4
                )

            [2] => Array
                (
                    [type] => video
                    [format] => mp4
                    [quality] => 832000
                    [url] => https://video.twimg.com/amplify_video/820082508054179840/vid/480x480/JypbCoP9FFOf1IgE.mp4
                    [mime_type] => video/mp4
                )

        )
)

**/
```

2. Use it with DownloadManager. 
Useful in case if your application is willing to download files from different sources (i.e. has more than one download handler)

```
<?php
use Abraham\TwitterOAuth\TwitterOAuth;
use AnyDownloader\DownloadManager\DownloadManager;
use AnyDownloader\DownloadManager\Model\URL;
use AnyDownloader\TwitterDownloader\TwitterHandler;

include_once 'vendor/autoload.php';

$consumerKey = '<CONSUMER KEY>';
$consumerSecret = '<CONSUMER SECRET>';
$client = new TwitterOAuth($consumerKey, $consumerSecret);

$twitterHandler = new TwitterHandler($client);

$downloadManager = new DownloadManager();
$downloadManager->addHandler($twitterHandler);

$twitterUrl = URL::fromString('https://twitter.com/PassengersMovie/status/821025484150423557');
$media = $downloadManager->fetchResource($twitterUrl);

print_r($media->toArray());
```
