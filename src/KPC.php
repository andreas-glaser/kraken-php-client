<?php

namespace AndreasGlaser\KPC;

use GuzzleHttp\Client;

/**
 * Class KPC
 *
 * @package AndreasGlaser\KPC
 * @author  Andreas Glaser
 */
class KPC
{
    const RESOURCE_BASE = 'https://api.kraken.com/0';
    const RESOURCE_PUBLIC = '/public';
    const RESOURCE_PRIVATE = '/private';

    /**
     * @var bool
     */
    protected $enablePrivate = false;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * KPC constructor.
     *
     * @param string|null $apiKey
     * @param string|null $apiSecret
     * @param array       $guzzleClientOptions
     *
     * @author Andreas Glaser
     */
    public function __construct(string $apiKey = null, string $apiSecret = null, array $guzzleClientOptions = [])
    {
        if (($apiKey || $apiSecret) && (!$apiKey || !$apiSecret)) {
            throw new \LogicException(sprintf('Both "apiKey" and "apiSecret" have to be provided'));
        }

        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;

        if ($this->apiKey) {
            $this->enablePrivate = true;
        }

        $defaultOptions = [
            'timeout' => 10,
        ];

        $guzzleClientOptions = array_replace_recursive($defaultOptions, $guzzleClientOptions);

        $this->httpClient = new Client($guzzleClientOptions);
    }

    /**
     * @return \GuzzleHttp\Client
     * @author Andreas Glaser
     */
    public function getClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * @param string $resource
     * @param array  $params
     *
     * @return \AndreasGlaser\KPC\Result
     * @author Andreas Glaser
     */
    public function sendPrivateRequest(string $resource, array $params = []): Result
    {
        if (!$this->enablePrivate) {
            throw new \LogicException('Private request are not possible if api key and secret have not been set');
        }

        $mt = explode(' ', microtime());
        $params['nonce'] = $mt[1] . substr($mt[0], 2, 6);

        $post_data = http_build_query($params, '', '&');
        $sign = hash_hmac('sha512', $post_data, $this->apiSecret);

        $options = [
            'headers'     => [
                'API-Key'  => $this->apiKey,
                'API-Sign' => $sign,
            ],
            'form_params' => $params,
        ];

        $url = self::RESOURCE_BASE . self::RESOURCE_PRIVATE . '/' . $resource;
        $response = $this->httpClient->post($url, $options);

        return new Result($response);
    }

    /**
     * @param string $resource
     * @param array  $params
     *
     * @return \AndreasGlaser\KPC\Result
     * @author Andreas Glaser
     */
    public function sendPublicRequest(string $resource, array $params = []): Result
    {
        $url = self::RESOURCE_BASE . self::RESOURCE_PUBLIC . '/' . $resource;
        $response = $this->httpClient->post($url, ['form_params' => $params]);

        return new Result($response);
    }

    /**
     * @return \AndreasGlaser\KPC\Result
     * @author Andreas Glaser
     */
    public function getTime(): Result
    {
        return $this->sendPublicRequest('Time');
    }

    /**
     * @param string     $info
     * @param string     $aclass
     * @param array|null $assets
     *
     * @return \AndreasGlaser\KPC\Result
     * @author Andreas Glaser
     */
    public function getAssets(string $info = 'info', string $aclass = 'currency', array $assets = null): Result
    {
        return $this->sendPublicRequest('Assets', [
            'info'   => $info,
            'aclass' => $aclass,
            'asset'   => !empty($assets) ? implode(',', $assets) : 'all',
        ]);
    }

    /**
     * @param string     $info
     * @param array|null $assetPairs
     *
     * @return \AndreasGlaser\KPC\Result
     * @author Andreas Glaser
     */
    public function getAssetPairs(string $info = 'info', array $assetPairs = null): Result
    {
        $this->validateInput('info', $info, ['info', 'leverage', 'fees', 'margin']);

        return $this->sendPublicRequest('AssetPairs', [
            'info' => $info,
            'pair' => !empty($assetPairs) ? implode(',', $assetPairs) : 'all',
        ]);
    }

    /**
     * @param array $assetPairs
     *
     * @return \AndreasGlaser\KPC\Result
     * @author Andreas Glaser
     */
    public function getTicker(array $assetPairs): Result
    {
        return $this->sendPublicRequest('Ticker', [
            'pair' => implode(',', $assetPairs),
        ]);
    }

    /**
     * @param string   $assetPair
     * @param int      $interval
     * @param int|null $since
     *
     * @return \AndreasGlaser\KPC\Result
     * @author Andreas Glaser
     */
    public function getOHLC(string $assetPair, int $interval = 1, int $since = null): Result
    {
        $this->validateInput('interval', $interval, [1, 5, 15, 30, 60, 240, 1440, 10080, 21600]);

        return $this->sendPublicRequest('OHLC', [
            'pair'     => $assetPair,
            'interval' => $interval,
            'since'    => $since,
        ]);
    }

    /**
     * @param string   $assetPair
     * @param int|null $count
     *
     * @return \AndreasGlaser\KPC\Result
     * @author Andreas Glaser
     */
    public function getOrderBook(string $assetPair, int $count = null): Result
    {
        return $this->sendPublicRequest('Depth', [
            'pair'  => $assetPair,
            'count' => $count,
        ]);
    }

    /**
     * @param string   $assetPair
     * @param int|null $since
     *
     * @return \AndreasGlaser\KPC\Result
     * @author Andreas Glaser
     */
    public function getRecentSpread(string $assetPair, int $since = null): Result
    {
        return $this->sendPublicRequest('Spread', [
            'pair'  => $assetPair,
            'since' => $since,
        ]);
    }

    /**
     * @param string $input
     * @param mixed  $provided
     * @param array  $valid
     *
     * @author Andreas Glaser
     */
    private function validateInput(string $input, $provided, array $valid)
    {
        if (!in_array($provided, $valid)) {
            throw new \InvalidArgumentException(sprintf('The provided value "%s" for argument "%s" is invalid. Valid: "%s"', $provided, $input, implode(',', $valid)));
        }
    }
}