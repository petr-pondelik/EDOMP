<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.3.19
 * Time: 16:24
 */

namespace App\TeacherModule\Services;

use App\TeacherModule\Exceptions\NewtonApiException;
use App\TeacherModule\Exceptions\NewtonApiRequestException;
use App\TeacherModule\Exceptions\NewtonApiSyntaxException;
use App\TeacherModule\Exceptions\NewtonApiUnreachableException;
use App\TeacherModule\Helpers\NewtonParser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Nette\Utils\Json;
use Nette\Utils\Strings;

/**
 * Class GuzzleHttpClient
 * @package App\TeacherModule\Services
 */
final class NewtonApiClient
{
    protected const SIMPLIFY = 'simplify/';
    protected const FACTOR = 'factor/';
    protected const ZEROES = 'zeroes/';

    /**
     * @var string
     */
    protected $newtonApiHost;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var NewtonParser
     */
    protected $newtonParser;

    /**
     * GuzzleHttpClient constructor.
     * @param string $newtonApiHost
     * @param NewtonParser $newtonParser
     */
    public function __construct(string $newtonApiHost, NewtonParser $newtonParser)
    {
        $this->client = new Client();
        $this->newtonApiHost = $newtonApiHost;
        $this->newtonParser = $newtonParser;
    }

    /**
     * @param string $expression
     * @return mixed
     * @throws NewtonApiException
     * @throws NewtonApiRequestException
     * @throws NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Nette\Utils\JsonException
     */
    public function simplify(string $expression)
    {
        bdump('SIMPLIFY');
        $expression = $this->newtonParser::newtonFormat($expression);

        try {
            $res = $this->client->request('GET', $this->newtonApiHost . self::SIMPLIFY . $expression);
        } catch (RequestException $e) {
            if ($e instanceof ConnectException) {
                throw new NewtonApiUnreachableException(sprintf('NewtonAPI na adrese %s je nedostupné.', $this->newtonApiHost));
            }
            if ($e instanceof ClientException) {
                throw new NewtonApiRequestException('Nevalidní požadavek na NewtonAPI.');
            }
            throw new NewtonApiException($e->getMessage());
        }

        $res = Json::decode($res->getBody())->result;

        if (Strings::contains($res, 'Stop')) {
            throw new NewtonApiSyntaxException('Šablona není validní matematický výraz.');
        }

        return $res;
    }

    /**
     * @param string $expression
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws NewtonApiException
     * @throws NewtonApiRequestException
     * @throws NewtonApiSyntaxException
     * @throws NewtonApiUnreachableException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Nette\Utils\JsonException
     */
    public function factor(string $expression)
    {
        $expression = $this->newtonParser::newtonFormat($expression);

        try {
            $res = $this->client->request('GET', $this->newtonApiHost . self::FACTOR . $expression);
        } catch (RequestException $e) {
            if ($e instanceof ConnectException) {
                throw new NewtonApiUnreachableException(sprintf('NewtonAPI na adrese %s je nedostupné.', $this->newtonApiHost));
            }
            if ($e instanceof ClientException) {
                throw new NewtonApiRequestException('Nevalidní požadavek na NewtonAPI.');
            }
            throw new NewtonApiException($e->getMessage());
        }

        $res = Json::decode($res->getBody())->result;

        if (Strings::contains($res, 'Stop')) {
            throw new NewtonApiSyntaxException('Šablona není validní matematický výraz.');
        }

        return $res;
    }

    /**
     * @param string $expression
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Nette\Utils\JsonException
     */
    public function zeroes(string $expression)
    {
        $res = $this->client->request('GET', $this->newtonApiHost . self::ZEROES . $expression);
        return Json::decode($res->getBody(), Json::FORCE_ARRAY)->result;
    }

    /**
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ping(): bool
    {
        try {
            $this->client->request('GET', $this->newtonApiHost);
        } catch (RequestException $e) {
            return false;
        }
        return true;
    }
}