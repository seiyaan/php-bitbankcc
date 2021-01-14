<?php

namespace seiyaan\bitbankcc;

const PUBLIC_API_ENDPOINT = 'https://public.bitbank.cc';
const PRIVATE_API_ENDPOINT = 'https://api.bitbank.cc';

class EasyBitbankcc
{
    /** @var string */
    private $api_key;

    /** @var string */
    private $api_secret;

    /**
     * privateApi constructor.
     * @param $api_key
     * @param $api_secret
     */
    public function __construct($api_key, $api_secret)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    /**
     * [Public API] 板情報を取得。指定の価格を返す
     * @param string $pair
     * @param string $type bids or asks
     * @param integer $n 配列番号
     * @return mixed
     */
    public function getDepthPrice($pair, $type, $n){
        $url = PUBLIC_API_ENDPOINT . "/{$pair}/depth";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $result = json_decode($response, true);
        return $result["data"][$type][$n][0];
    }

    /**
     * 新規注文を行う
     * @param array $params
     * @return mixed
     */
    public function postPrivateUserSpotOrder(array $params){
        $header = $this->requestHeader(json_encode($params));
        $ch = curl_init();
        $url = PRIVATE_API_ENDPOINT . "/v1/user/spot/order";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        return json_decode($response, true);
    }

    /**
     * アクティブな注文を取得する
     * @param string pair
     * @return mixed
     */
    public function getActiveOrders($pair){
        $header = $this->requestHeader("/v1/user/spot/active_orders?pair=${pair}");
        $url = PRIVATE_API_ENDPOINT . "/v1/user/spot/active_orders?pair=${pair}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        return json_decode($response, true);
    }

    public function postCancelOrders(array $params){
        $header = $this->requestHeader(json_encode($params));
        $ch = curl_init();
        $url = PRIVATE_API_ENDPOINT . "/v1/user/spot/cancel_orders";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        return json_decode($response, true);
    }

    /**
     * @param array $queryParam
     * @return array
     */
    private function requestHeader($queryStr) {
        /** @var string $microtime */
        $microtime = microtime(true) * 10000;

        /** @var string $signature */
        $signature = hash_hmac('sha256', $microtime . $queryStr, $this->api_secret);

        return [
            'Content-Type: application/json',
            'Accept: application/json',
            "ACCESS-KEY: " . $this->api_key,
            "ACCESS-NONCE: {$microtime}",
            "ACCESS-SIGNATURE: {$signature}",
        ];
    }
}