<?php

namespace SocialStream\Network;

use SocialStream\Helper;
use SocialStream\Post;

/**
 * Class Example
 *
 * @package SocialStream\Network
 */
class Example extends Base {

    /**
     * Facebook constructor.
     *
     * @param $info
     */
    public function __construct($info) {
        $this->networkBaseUrl = 'https://my-example.io/';
        $this->expectedCredentials = [
            'access_token',
        ];

        parent::__construct($info);
    }

    /**
     *
     */
    public function buildApi() {
        $url = '';
        $this->setApiConnectionInfo($url);
    }

    /**
     *
     */
    public function getDataFromApi() {
        $this->buildApi();

        if ($this->apiConnectionInfo) {
            $this->sourceData = [];
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

        $post->setId(null);
        $post->setType(null);
        $post->setUrl(null);
        $post->setThumbnailUrl(null);
        $post->setDate(null);
        $post->setAuthorName(null);
        $post->setAuthorUrl(null);
        $post->setContent(null);

        return $post;
    }
}