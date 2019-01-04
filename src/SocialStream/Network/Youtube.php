<?php

namespace SocialStream\Network;

use SocialStream\Helper;
use SocialStream\Post;

/**
 * Class Youtube
 *
 * @package SocialStream\Network
 */
class Youtube extends Base {

    /**
     * @inheritdoc
     */
    public function __construct($info) {
        $this->networkBaseUrl = 'https://www.youtube.com/';
        $this->expectedCredentials = [
            'api_key',
        ];

        parent::__construct($info);
    }

    /**
     * @inheritdoc
     */
    public function buildApi() {
        $baseUrl = 'https://www.googleapis.com/youtube/v3/search';
        $apiKey = current($this->getCredentials());

        $params = [
            'order' => 'date',
            'part' => 'snippet',
            'channelId' => $this->getAccountName(),
            'maxResults' => 50,
            'key' => $apiKey,
        ];
        $url = Helper::buildUrl($baseUrl, $params);

        $this->setApiConnectionInfo($url);
    }

    /**
     * @inheritdoc
     */
    public function getDataFromApi() {
        $this->buildApi();

        if (!empty($this->apiConnectionInfo)) {
            $data = Helper::curl($this->apiConnectionInfo);

            if (!empty($data) && property_exists($data, 'items')) {
                $this->sourceData = $data->items;
            }
        }
    }


    /**
     * @inheritdoc
     */
    public function formatPost($data) {
        $post = new Post($data);

        $post->setNetwork($this->getNetworkName());

        if (property_exists($data->id, 'videoId')) {
            $dataType = explode('#', $data->id->kind);
            $post->setId($data->id->videoId);
            $post->setType(array_pop($dataType));
            $post->setUrl($this->networkBaseUrl . 'watch?v=' . $data->id->videoId);

            if (property_exists($data, 'snippet')) {
                $post->setThumbnailUrl($data->snippet->thumbnails->high->url);
                $post->setDate($data->snippet->publishedAt);
                $post->setAuthorName($data->snippet->channelTitle);

                if (property_exists($data->snippet, 'channelId')) {
                    $post->setAuthorUrl($this->networkBaseUrl . 'channel/' . $data->snippet->channelId);
                }
                $post->setContent($data->snippet->description);
            }
        }

        return $post;
    }
}