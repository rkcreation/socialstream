<?php

namespace SocialStream\Network;

use SocialStream\Helper;
use SocialStream\Post;

/**
 * Class Twitter
 *
 * @package SocialStream\Network
 */
class Twitter extends Base {

    /**
     * Facebook constructor.
     *
     * @param $info
     */
    public function __construct($info) {
        $this->networkBaseUrl = 'https://twitter.com/';
        $this->expectedCredentials = [
            'consumer_key',
            'consumer_secret',
            'oauth_access_token',
            'oauth_access_token_secret',
        ];

        parent::__construct($info);
    }

    /**
     *
     */
    public function buildApi() {
        if (class_exists('TwitterAPIExchange')) {
            $service = new \TwitterAPIExchange($this->getCredentials());

            $this->setApiConnectionInfo($service);
        }
    }

    /**
     *
     */
    public function getDataFromApi() {
        $this->buildApi();

        if ($this->apiConnectionInfo instanceof \TwitterAPIExchange) {
            $params = [
                'screen_name' => $this->getAccountName(),
                'count' => 50,
                'tweet_mode' => 'extended'
            ];
            $baseUrl = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
            $paramsQuery = http_build_query($params, '', '&');

            /** @var $data \TwitterAPIExchange */
            $data = $this->apiConnectionInfo
                ->setGetfield($paramsQuery)
                ->buildOauth($baseUrl, 'GET')
                ->performRequest();

            if (!empty($data)) {
                $data = json_decode($data);

                if (!empty($data)) {
                    $this->sourceData = $data;
                }
            }
        }
    }


    /**
     * @param $data
     *
     * @return mixed|Post
     */
    public function formatPost($data) {
        $post = new Post($data);

        $post->setNetwork($this->getNetworkName());

        $post->setId($data->id_str);

        if ((bool) $data->retweeted) {
            $type = 'retweet';
        }
        elseif ((bool) $data->is_quote_status) {
            $type = 'quote';
        }
        else {
            $type = 'tweet';
        }
        $post->setType($type);

        $post->setUrl($this->networkBaseUrl . $data->user->screen_name . '/status/' . $data->id);
        $post->setDate($data->created_at);
        $post->setAuthorName($data->user->screen_name);
        $post->setAuthorUrl($this->networkBaseUrl . $data->user->screen_name);

        if (property_exists($data, 'full_text')) {
            $post->setContent($this->convertTextToHtml($data->full_text));
        }

        if (property_exists($data->entities, 'media') && !empty($data->entities->media)) {
            foreach ($data->entities->media as $media) {
                if ($media->type === 'photo') {
                    $post->setThumbnailUrl($media->media_url_https);
                }
            }
        }

        return $post;
    }

    /**
     * HTML filters for tweet content
     * @param $content
     *
     * @return null|string|string[]
     */
    public function convertTextToHtml($content) {
        // Convert urls to <a> links
        $content = preg_replace('/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/', '<a target="_blank" href="$1">$1</a>', $content);
        // Convert hashtags to twitter searches in <a> links
        $content = preg_replace('/#([A-Za-z0-9\/\.]*)/', '<a target="_blank" href="http://twitter.com/search?q=$1">#$1</a>', $content);
        // Convert attags to twitter profiles in &lt;a&gt; links
        $content = preg_replace('/@([A-Za-z0-9_\/\.]*)/', '<a target="_blank" href="http://www.twitter.com/$1">@$1</a>', $content);

        return $content;
    }
}