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
use App\Helpers\StringsHelper;
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
     * @const string
     */
    protected const NEWTON_API_URL = 'localhost:3000/';
    //protected const NEWTON_API_URL = 'https://edomp-newton-api.herokuapp.com/';

    /**
     * @const string
     */
    protected const SIMPLIFY = 'simplify/';

    /**
     * @const string
     */
    protected const ZEROES = 'zeroes/';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * GuzzleHttpClient constructor.
     * @param StringsHelper $stringsHelper
     */
    public function __construct(StringsHelper $stringsHelper)
    {
        $this->client = new Client();
        $this->stringsHelper = $stringsHelper;
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
//        var_dump('SIMPLIFY');
        $expression = $this->stringsHelper::newtonFormat($expression);
        bdump($expression);
        try {
            $res = $this->client->request('GET', self::NEWTON_API_URL . self::SIMPLIFY . $expression);
        } catch (RequestException $e){
            if($e instanceof ConnectException){
                throw new NewtonApiUnreachableException(sprintf('NewtonAPI na adrese %s je nedostupné.', self::NEWTON_API_URL));
            }
            if($e instanceof ClientException){
                throw new NewtonApiRequestException('Nevalidní požadavek na NewtonAPI.');
            }
            throw new NewtonApiException($e->getMessage());
        }

        $res = json_decode($res->getBody())->result;

        if(Strings::contains($res, 'Stop')){
            throw new NewtonApiSyntaxException('Šablona není validní matematický výraz.');
        }

        return $res;
    }

    /**
     * @param string $expression
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function zeroes(string $expression)
    {
        $res = $this->client->request('GET', self::NEWTON_API_URL . self::ZEROES . $expression);
        return json_decode($res->getBody())->result;
    }

    /**
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ping(): bool
    {
        try{
            $this->client->request('GET', self::NEWTON_API_URL);
        } catch (RequestException $e){
            return false;
        }
        return true;
    }

}