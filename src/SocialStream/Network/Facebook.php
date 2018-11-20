<?php

namespace SocialStream\Network;

use SocialStream\Helper;
use SocialStream\Post;

/**
 * Class Facebook
 *
 * @package SocialStream\Network
 */
class Facebook extends Base {

    /**
     * Facebook constructor.
     *
     * @param $info
     */
    public function __construct($info) {
        $this->networkBaseUrl = 'https://www.facebook.com/';
        $this->expectedCredentials = [
            'access_api',
            'access_token',
        ];

        parent::__construct($info);
    }

    /**
     *
     */
    public function buildApi() {
        $token = implode($this->getCredentials(), '|');

        $params = [
            'key' => 'value',
            'access_token' => $token,
            'limit' => 50,
            'fields' => 'id,admin_creator,caption,permalink_url,message,picture,full_picture,link,name,description,type,created_time,from,object_id,story',
        ];

        $baseUrl = 'https://graph.facebook.com/' . parent::getAccountName() . '/feed';
        $url = Helper::requestUrl($baseUrl, $params);

        $this->setApiConnectionInfo($url);
    }

    /**
     *
     */
    public function getDataFromApi() {
        $this->buildApi();

        if (!empty($this->apiConnectionInfo)) {
            $data = Helper::curl($this->apiConnectionInfo);

            if (!empty($data)) {
                $this->sourceData = $data->data;
            }
        }
    }


    /**
     * @param $data
     *
     * @return mixed|\SocialStream\Post
     */
    public function formatPost($data) {
        $post = new Post($data);

        $post->setNetwork($this->getNetworkName());

        $post->setId($data->id);
        $post->setType($data->type);
        $post->setUrl($data->permalink_url);
        $post->setDate($data->created_time);

        if (property_exists($data, 'from')) {
            $post->setAuthorName($data->from->name);
            $post->setAuthorUrl($this->networkBaseUrl . $data->from->id);
        }

        if (property_exists($data, 'message')) {
            $post->setContent($data->message);
        }
        if (property_exists($data, 'full_picture')) {
            $post->setThumbnailUrl($data->full_picture);
        }

        return $post;
    }
}