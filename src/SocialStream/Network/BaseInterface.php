<?php

namespace SocialStream\Network;

/**
 * Interface BaseInterface
 *
 * @package SocialStream\Network
 */
interface BaseInterface {

    /**
     * Build connection to Network API
     *
     * @return mixed
     */
    public function buildApi();

    /**
     * Retrieve source data from API response
     *
     * @return mixed
     */
    public function getDataFromApi();

    /**
     * Each network post is formatted
     *
     * @param $data
     * @return mixed
     */
    public function formatPost($data);
}