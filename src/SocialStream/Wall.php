<?php

namespace SocialStream;

/**
 * Class Wall
 *
 * @package SocialStream
 */
class Wall {

    /**
     * Array of Network entities
     *
     * @var
     */
    protected $networks;

    /**
     * Array of Post entities
     *
     * @var array
     */
    protected $posts;

    /**
     * Absolute path to cache directory
     *
     * @var
     */
    public static $cacheDirectory = __DIR__ . '/../../cache';

    /**
     * Cache duration in seconds
     *
     * @var
     */
    public static $cacheDuration = 3600;

    /**
     * Should display posts body content as html (or plain text if false)
     *
     * @var bool
     */
    public static $bodyHtml = true;

    /**
     * Should displays debug info
     *
     * @var
     */
    public static $debug = false;


    /**
     * Wall constructor.
     */
    public function __construct() {
        $this->posts = [];
    }

    /**
     * @param $debug
     */
    public function setDebug($debug) {
        self::$debug = $debug;
    }

    /**
     * Cache duration is seconds (s)
     *
     * @param int $cacheDuration
     */
    public function setCacheDuration($cacheDuration) {
        self::$cacheDuration = $cacheDuration;
    }

    /**
     * Set absolute system path to cache directory. Do not include a trailing slash.
     *
     * @param null $path
     */
    public function setCacheDirectory($path) {
        self::$cacheDirectory = $path;
    }

    /**
     * Should display posts body content as html (or plain text if false)
     *
     * @param bool $status
     */
    public function setBodyAsHtml($status = true) {
        self::$bodyHtml = $status;
    }

    /**
     * @param $networksInfo
     */
    public function addNetworks($networksInfo) {
        foreach ($networksInfo as $networkName => $networkInfo) {
            $this->addNetwork($networkName, $networkInfo);
        }
    }

    /**
     * List posts for all declared networks
     * @param int $countPostsPerNetwork
     * @param bool $shuffle
     *
     * @return mixed
     */
    public function getPosts(int $countPostsPerNetwork = 6, bool $shuffle = false) {
        $filteredPosts = $localPosts = [];

        if (!empty($this->posts)) {
            foreach ($this->posts as $networkName => $networkPosts) {
                $localPosts[] = $this->getPostsFrom($networkName, $countPostsPerNetwork);
            }
            $filteredPosts = array_merge(...$localPosts);

            if (!empty($filteredPosts)) {
                // Sort by created timestamp desc
                usort($filteredPosts, function ($a, $b) {
                    return -1 * strcmp($a->timestamp, $b->timestamp);
                });

                // Random order
                if ($shuffle === true) {
                    shuffle($filteredPosts);
                }
            }
        }

        return $filteredPosts;
    }

    /**
     * List posts for a specific network
     * @param string $networkName
     * @param int $countPosts
     * @param bool $shuffle
     *
     * @return mixed
     */
    public function getPostsFrom($networkName, $countPosts = 6, $shuffle = false) {
        if (isset($this->posts[$networkName]) && !empty($this->posts[$networkName])) {
            $posts = \array_slice($this->posts[$networkName], 0, $countPosts);

            // Random order
            if ($shuffle === true) {
                shuffle($posts);
            }

            return $posts;
        }

        Helper::debug(sprintf('Network "%s" is not declared', $networkName));

        return [];
    }


    /**
     * @param string $networkName
     * @param array $networkInfo
     */
    protected function addNetwork(string $networkName, array $networkInfo) {
        if ($networkClass = $this->checkNetworkInfo($networkName, $networkInfo)) {
            $this->networks[$networkName] = $networkClass;
            $this->posts[$networkName] = $networkClass->getPosts();
        }
    }

    /**
     * Check if called network can respond and has results
     * @param string $networkName
     * @param array $networkInfo
     *
     * @return mixed
     */
    protected function checkNetworkInfo(string $networkName, array $networkInfo) {
        if (isset($networkInfo['credentials'], $networkInfo['account'])) {
            if ($networkClassName = $this->classExists($networkName)) {
                $networkClass = new $networkClassName($networkInfo);

                if (!empty($networkClass->getPosts())) {
                    return $networkClass;
                }

                Helper::debug(sprintf('The %s API is not responding', $networkName));
                Helper::debug($networkClass->getApiConnectionInfo());

            } else {
                Helper::debug(sprintf('Sorry, we don\'t know "%s" as network', $networkName));
            }

        } else {
            Helper::debug(sprintf('You must declare credentials and account for your "%s" network', $networkName));
        }

        return false;
    }

    /**
     * @param string $networkName
     *
     * @return bool|string
     */
    protected function classExists(string $networkName) {
        $class = '\\SocialStream\\Network\\' . ucfirst(strtolower($networkName));
        return class_exists($class) ? $class : false;
    }
}