<?php

namespace SocialStream\Network;

/**
 * Interface BaseInterface
 *
 * @package SocialStream\Network
 */
interface BaseInterface {

    /**
     * @return mixed
     */
    public function buildApi();

    /**
     * @return mixed
     */
    public function getDataFromApi();

    /**
     * @param $data
     *
     * @return mixed
     */
    public function formatPost($data);
}