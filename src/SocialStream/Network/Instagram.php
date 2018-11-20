<?php

namespace SocialStream\Network;

use SocialStream\Helper;
use SocialStream\Post;

/**
 * Class Instagram
 *
 * @package SocialStream\Network
 * @expectedCredentials array('access_token')
 * @see https://rudrastyh.com/instagram/get-recent-photos-php.html
 */
class Instagram extends Base {

    /**
     * Facebook constructor.
     *
     * @param $info
     */
    public function __construct($info) {
        $this->networkBaseUrl = 'https://www.instagram.com/';
        $this->expectedCredentials = [
            'access_token',
        ];

        parent::__construct($info);
    }

    /**
     *
     */
    public function buildApi() {
        $credentials = $this->getCredentials();

        $baseUrl = 'https://api.instagram.com/v1/users/self/media/recent';
        $params = [
            'access_token' => $credentials['access_token'],
            'count' => 50
        ];

        $url = Helper::buildUrl($baseUrl, $params);
        $this->setApiConnectionInfo($url);
    }

    /**
     *
     */
    public function getDataFromApi() {
        $this->buildApi();

        if ($this->apiConnectionInfo) {
            $data = Helper::curl($this->apiConnectionInfo);

            if (!empty($data)) {
                $this->sourceData = $data->data;
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

        $postType = \in_array($data->type, ['image', 'video'], false) ? $data->type : 'image';

        $post->setNetwork($this->getNetworkName());

        $post->setId($data->id);
        $post->setType($postType);
        $post->setUrl($data->link);

        if (property_exists($data, 'images') && property_exists($data->images, 'standard_resolution')) {
            $post->setThumbnailUrl($data->images->standard_resolution->url);
        }

        $post->setDate($data->created_time, true);
        $post->setAuthorName($data->user->full_name);
        $post->setAuthorUrl($this->networkBaseUrl . $data->user->username);
        $post->setContent($data->caption->text);

        return $post;
    }
}