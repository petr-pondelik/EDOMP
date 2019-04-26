<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.3.19
 * Time: 16:24
 */

namespace App\Services;

use GuzzleHttp\Client;

/**
 * Class GuzzleHttpClient
 * @package app\services
 */
class NewtonApiClient
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @const string
     */
    protected const NEWTON_API_URL = "https://newton.now.sh/";

    /**
     * @const string
     */
    protected const SIMPLIFY = "simplify/";

    /**
     * @const string
     */
    protected const ZEROES = "zeroes/";

    /**
     * GuzzleHttpClient constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $expression
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function simplify(string $expression)
    {
        $res = $this->client->request("GET", self::NEWTON_API_URL . self::SIMPLIFY . $expression);
        return json_decode($res->getBody())->result;
    }

    /**
     * @param string $expression
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function zeroes(string $expression)
    {
        $res = $this->client->request("GET", self::NEWTON_API_URL . self::ZEROES . $expression);
        return json_decode($res->getBody())->result;
    }

}