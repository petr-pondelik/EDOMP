<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.3.19
 * Time: 16:24
 */

namespace App\Services;

use App\Exceptions\NewtonApiException;
use App\Exceptions\NewtonApiRequestException;
use App\Exceptions\NewtonApiSyntaxException;
use App\Exceptions\NewtonApiUnreachableException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Nette\Utils\Strings;

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
    //protected const NEWTON_API_URL = 'https://newton.now.sh/';
    protected const NEWTON_API_URL = 'localhost:3000/';
    //protected const NEWTON_API_URL = 'https://edomp-newton-api.herokuapp.com/';

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
     * @throws NewtonApiException
     * @throws NewtonApiRequestException
     * @throws NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function simplify(string $expression)
    {
        try {
            $res = $this->client->request("GET", self::NEWTON_API_URL . self::SIMPLIFY . $expression);
        } catch (RequestException $e){
            if($e instanceof ConnectException)
                throw new NewtonApiUnreachableException(sprintf("NewtonAPI na adrese %s je nedostupné.", self::NEWTON_API_URL));
            if($e instanceof ClientException)
                throw new NewtonApiRequestException("Nevalidní požadavek na NewtonAPI.");
            throw new NewtonApiException($e->getMessage());
        }

        $res = json_decode($res->getBody())->result;

        if(Strings::contains($res, "Stop"))
            throw new NewtonApiSyntaxException("Šablona není validní matematický výraz.");

        return $res;
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

    /**
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ping(): bool
    {
        try{
            $this->client->request("GET", self::NEWTON_API_URL);
        } catch (RequestException $e){
            return false;
        }
        return true;
    }

}