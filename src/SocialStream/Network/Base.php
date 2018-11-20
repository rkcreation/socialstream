<?php

namespace SocialStream\Network;

use SocialStream\Helper;
use SocialStream\Wall;

/**
 * Class Base
 *
 * @package SocialStream\Network
 */
abstract class Base implements BaseInterface {

    /**
     * @var
     */
    public $expectedCredentials;

    /**
     * @var
     */
    public $credentials;

    /**
     * @var
     */
    public $accountName;

    /**
     * @var
     */
    public $networkBaseUrl;

    /**
     * @var
     */
    public $apiConnectionInfo;

    /**
     * @var
     */
    public $sourceData;

    /**
     * @var
     */
    public $posts;

    /**
     * Base constructor.
     *
     * @param $info
     */
    public function __construct($info) {
        // Defaults
        $this->setCredentials($info['credentials']);
        $this->setAccountName($info['account']);
        $this->setApiConnectionInfo(null);
        $this->setPosts([]);

        if ($this->checkCredentials()) {
            $this->handlePosts();
        }
        else {
            Helper::debug(sprintf('Expected credentials for %s are %s', $this->getAccountName(), json_encode($this->expectedCredentials)));
        }
    }

    /**
     * @return mixed
     */
    public function checkCredentials() {
        return \count(array_intersect_key($this->expectedCredentials, $this->getCredentials())) === 0;
    }

    /**
     * Get posts from cache or API
     */
    public function handlePosts() {
        $type = 'cache';
        $cachedData = $this->getDataFromCache();

        if (empty($cachedData)) {
            $type = 'api';
            $this->getDataFromApi();
            $this->setDataToCache();

        }
        else {
            $this->sourceData = $cachedData;
        }

        Helper::debug(sprintf('%s loaded from %s', $this->getNetworkName(), $type));

        $this->formatPosts();
    }

    /**
     * @return bool|null|string
     */
    public function getDataFromCache() {
        $directoryPath = Wall::$cacheDirectory;
        $filePath = $this->getCacheFilePath();

        // Create non existing cache directory
        if (!is_dir($directoryPath)) {
            $directoryCreation = mkdir($directoryPath, 0755, true);
            Helper::debug(sprintf('Cache directory %s creation status : %s', $directoryPath, $directoryCreation));
        }

        // Check current network cache file existence and duration
        if (file_exists($filePath) && (time() - filemtime($filePath)) < Wall::$cacheDuration) {
            $data = file_get_contents($filePath);

            if (!empty($data)) {
                return json_decode($data);
            }
        }

        return null;
    }

    /**
     *
     */
    public function setDataToCache() {
        $filePath = $this->getCacheFilePath();
        $data = json_encode($this->sourceData);

        file_put_contents($filePath, $data);
    }

    /**
     * @return mixed
     */
    public function getCacheFilePath() {
        return Wall::$cacheDirectory . '/' . utf8_encode($this->getAccountName()) . '-' . utf8_encode($this->getNetworkName()) . '.txt';
    }

    /**
     * @param $credentials
     */
    public function setCredentials($credentials) {
        $this->credentials = $credentials;
    }

    /**
     * @return mixed
     */
    public function getCredentials() {
        return $this->credentials;
    }

    /**
     * @param $accountName
     */
    public function setAccountName($accountName) {
        $this->accountName = $accountName;
    }

    /**
     * @return mixed
     */
    public function getAccountName() {
        return $this->accountName;
    }

    /**
     * @return mixed
     */
    public function getNetworkName() {
        $className = explode('\\', \get_class($this));
        return strtolower(array_pop($className));
    }

    /**
     * @param $info
     */
    public function setApiConnectionInfo($info) {
        $this->apiConnectionInfo = $info;
    }

    /**
     * @return mixed
     */
    public function getApiConnectionInfo() {
        return $this->apiConnectionInfo;
    }

    /**
     *
     */
    public function formatPosts() {
        if (!empty($this->sourceData) && \is_array($this->sourceData)) {
            foreach ($this->sourceData as $post) {
                $postEntity = $this->formatPost($post);
                $this->setPost($postEntity);
            }
        }
    }

    /**
     * @param $posts
     */
    public function setPosts($posts) {
        $this->posts = $posts;
    }

    /**
     * @param $post
     */
    public function setPost($post) {
        $this->posts[] = $post;
    }

    /**
     * @param int $countPosts
     *
     * @return mixed
     */
    public function getPosts(int $countPosts = -1) {
        return (!empty($this->posts) && ($countPosts > -1)) ? \array_slice($this->posts, 0, $countPosts) : $this->posts;
    }
}