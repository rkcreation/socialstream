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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function buildApi() {
        $token = implode($this->getCredentials(), '|');

        $baseUrl = 'https://graph.facebook.com/' . parent::getAccountName() . '/feed';
        $params = [
            'key' => 'value',
            'access_token' => $token,
            'limit' => 50,
            'fields' => 'id,admin_creator,caption,permalink_url,message,picture,full_picture,link,name,description,type,created_time,from,object_id,story',
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

            if (!empty($data) && property_exists($data, 'data')) {
                $this->sourceData = $data->data;
            }
        }
    }

    /**
     * @inheritdoc
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