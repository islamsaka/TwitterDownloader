<?php
namespace AnyDownloader\TwitterDownloader\Tests;

use Abraham\TwitterOAuth\TwitterOAuth;
use AnyDownloader\DownloadManager\Model\URL;
use AnyDownloader\TwitterDownloader\TwitterHandler;
use PHPUnit\Framework\TestCase;

class TwitterHandlerTest extends TestCase
{
    /** @test */
    public function handler_validates_given_url()
    {
        $handler = new TwitterHandler(new TwitterOAuth('', ''));
        $url = URL::fromString('https://www.twitter.com/PassengersMovie/status/821025484150423557');
        $this->assertTrue($handler->isValidUrl($url));
    }

    /** @test */
    public function handler_validates_given_url_with_postfix()
    {
        $handler = new TwitterHandler(new TwitterOAuth('', ''));
        $url = URL::fromString('https://www.twitter.com/PassengersMovie/status/821025484150423557/video/1');
        $this->assertTrue($handler->isValidUrl($url));
    }

    /** @test */
    public function handler_validates_given_url_without_www()
    {
        $handler = new TwitterHandler(new TwitterOAuth('', ''));
        $url = URL::fromString('https://twitter.com/PassengersMovie/status/821025484150423557');
        $this->assertTrue($handler->isValidUrl($url));
    }

    /** @test */
    public function handler_validates_given_short_url()
    {
        $handler = new TwitterHandler(new TwitterOAuth('', ''));
        $url = URL::fromString('https://t.co/X0go99a4hO');
        $this->assertTrue($handler->isValidUrl($url));
    }

    /** @test */
    public function handler_can_not_validates_given_facebook_url()
    {
        $handler = new TwitterHandler(new TwitterOAuth('', ''));
        $url = URL::fromString('https://facebook.com/PassengersMovie/status/821025484150423557');
        $this->assertFalse($handler->isValidUrl($url));
    }

    /** @test */
    public function handler_can_not_validate_given_wrong_twitter_url()
    {
        $handler = new TwitterHandler(new TwitterOAuth('', ''));
        $url = URL::fromString('https://twitter.com/status/PassengersMovie/821025484150423557');
        $this->assertFalse($handler->isValidUrl($url));
    }
}