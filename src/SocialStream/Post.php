<?php

namespace SocialStream;

/**
 * Class Post
 *
 * @package SocialStream
 */
class Post {

    /**
     * @var
     */
    public $data;

    /**
     * @var
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var
     */
    public $url;

    /**
     * @var
     */
    public $network;

    /**
     * @var
     */
    public $date;

    /**
     * @var
     */
    public $timestamp;

    /**
     * @var
     */
    public $content;

    /**
     * @var object
     */
    public $author;

    /**
     * @var
     */
    public $thumbnailUrl;

    /**
     * Post constructor.
     *
     * @param $data
     */
    public function __construct($data) {
        $this->data = $data;
        $this->type = 'default';
        $this->author = (object) [];
    }

    /**
     * @param string $id
     */
    public function setId(string $id) {
        $this->id = $id;
    }

    /**
     * @param string $type
     */
    public function setType(string $type) {
        $this->type = $type;
    }

    /**
     * @param string $network
     */
    public function setNetwork(string $network) {
        $this->network = $network;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url) {
        $this->url = $url;
    }

    /**
     * @param string $date
     * @param bool $isTimestamp
     */
    public function setDate(string $date, bool $isTimestamp = false) {

        if ($isTimestamp === false) {
            $dateTime = new \DateTime($date);
        } else {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($date);
        }

        if ($dateTime instanceof \DateTime) {
            $this->date = $dateTime;
            $this->timestamp = $this->date->getTimestamp();
        }
    }

    /**
     * @param $author
     */
    public function setAuthor($author) {
        $this->author = $author;
    }

    /**
     * @param string $authorName
     */
    public function setAuthorName(string $authorName) {
        $this->author->name = $authorName;
    }

    /**
     * @param string $authorUrl
     */
    public function setAuthorUrl(string $authorUrl) {
        $this->author->url = $authorUrl;
    }

    /**
     * @param string $thumbnailUrl
     */
    public function setThumbnailUrl(string $thumbnailUrl) {
        $this->thumbnailUrl = $thumbnailUrl;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content) {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->get('id');
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->get('type');
    }

    /**
     * @return mixed
     */
    public function getNetwork() {
        return $this->get('network');
    }

    /**
     * @return mixed
     */
    public function getUrl() {
        return $this->get('url');
    }

    /**
     * @param null $subData
     *
     * @return null|object
     */
    public function getAuthor($subData = null) {
        if (!empty($this->author)) {
            if ($subData === null) {
                return $this->author;
            }
            if (property_exists($this->author, $subData)) {
                return $this->author->{$subData};
            }
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getAuthorName() {
        return $this->getAuthor('name');
    }

    /**
     * @return mixed
     */
    public function getAuthorUrl() {
        return $this->getAuthor('url');
    }

    /**
     * @param bool $plainText
     * @param int $maxLength
     *
     * @return mixed|string
     */
    public function getContent($plainText = false, $maxLength = -1) {
        $content = null;

        if (!empty($this->content)) {
            $content = $this->content;

            if ($plainText) {
                $content = strip_tags($this->content);
            }
            if ((int) $maxLength > -1) {
                $content = strip_tags($this->content);
                $content = Helper::truncate($content, $maxLength, true, true);
            }
        }

        return $content;
    }

    /**
     * @return mixed
     */
    public function getThumbnailUrl() {
        return $this->get('thumbnailUrl');
    }

    /**
     * @param string $format
     *
     * @return null|string
     */
    public function getDate($format = 'd m Y H:i') {
        return ($this->date instanceof \DateTime) ? $this->date->format($format) : null;
    }

    /**
     * @param $field
     *
     * @return null
     */
    protected function get($field) {
        return (property_exists($this, $field) && !empty($this->{$field})) ? $this->{$field} : null;
    }
}