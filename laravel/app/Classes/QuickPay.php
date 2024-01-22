<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class QuickPay
{
    private $mid;
    private $secret_key;
    private $endpoint_quickpay;
    private $endpoint_payment;
    private $version;
    public $amount;
    public $description;
    public $url1;
    public $url2;
    public $invoice_no;
    public $rental_id;
    public $quotation_id;
    public $class_name;
    public $expire_date;
    public $qp_id;
    public $params;

    public function __construct()
    {
        $this->mid = config('services.2c2p.mid');
        $this->secret_key = config('services.2c2p.secret_key');
        $this->endpoint_quickpay = config('services.2c2p.qp_endpoint');
        $this->endpoint_payment = config('services.2c2p.payment_endpoint');
        $this->version = config('services.2c2p.version');
        $this->amount = 0;
        $this->description = 'default';
        $this->url1 = route('qp.cb.frontend');
        $this->url2 = route('qp.cb.backend');
        $this->invoice_no = '';
        $this->expire_date = date('Y-m-d H:i:s', strtotime('+30 days'));
        $this->qp_id = '';
    }

    function generateLink()
    {
        $params = [
            'version' => $this->version,
            'timeStamp' => strval(time()),
            'merchantID' => $this->mid,
            'orderIdPrefix' => "QP-" . time(),
            'description' => $this->description,
            'currency' => "THB",
            'amount' => strval($this->amount),
            'allowMultiplePayment' => "N",
            'expiry' => $this->expire_date,
            'userData1' => $this->quotation_id,
            'userData2' => $this->class_name,
            'userData3' => $this->rental_id,
            'userData4' => "",
            'userData5' => "",
            'resultUrl1' => $this->url1,
            'resultUrl2' => $this->url2,
        ];
        return $this->processRequest($params, 'GenerateQPReq', 'GenerateQPRes');
    }

    function update()
    {
        $params = [
            'version' => $this->version,
            'timeStamp' => strval(time()),
            'merchantID' => $this->mid,
            'qpID' => $this->qp_id,
            'description' => $this->description,
            'amount' => strval($this->amount),
            'expiry' => $this->expire_date,
            'request3DS' => 'N'
        ];
        return $this->processRequest($params, 'QPUpdateReq', 'QPUpdateRes');
    }

    function delete()
    {
        $params = [
            'version' => $this->version,
            'timeStamp' => strval(time()),
            'merchantID' => $this->mid,
            'qpID' => $this->qp_id,
        ];
        return $this->processRequest($params, 'QPDeleteReq', 'QPDeleteRes');
    }

    function query()
    {
        $params = [
            'version' => $this->version,
            'timeStamp' => strval(time()),
            'merchantID' => $this->mid,
            'qpID' => $this->qp_id,
        ];
        return $this->processRequest($params, 'QPQueryReq', 'QPQueryRes');
    }

    function inquiry()
    {
        $key = $this->secret_key;
        $params = [
            'merchantID' => $this->mid,
            'invoiceNo' => $this->invoice_no,
            'locale' => 'en',
        ];
        $jwt = JWT::encode($params, $key, 'HS256');
        $payload = [
            'payload' => $jwt
        ];
        $payload = json_encode($payload);
        $response = Http::withBody(
            $payload,
            'application/json'
        )->post($this->endpoint_payment);
        $response = $response->body();
        $response = json_decode($response, true);
        $jwt_decoded = JWT::decode($response['payload'], new Key($key, 'HS256'));
        return json_decode(json_encode($jwt_decoded), true);
    }

    private function processRequest($params, $reqParamName, $resParamName)
    {
        $hashValue = $this->hashValue($params);
        $params['hashValue'] = $hashValue;
        $parameters = [
            $reqParamName => $params
        ];

        $base64_parameters = base64_encode(json_encode($parameters));
        $response = Http::withBody(
            $base64_parameters,
            'text/plain'
        )->post($this->endpoint_quickpay);
        $base64_response = $response->body();
        $response = base64_decode($base64_response);
        $response = json_decode($response, true);
        return $response[$resParamName];
    }

    private function hashValue($parameters = [], $algo = 'sha1')
    {
        $hash_value = implode('', array_values($parameters));
        $hashValue = hash_hmac($algo, $hash_value, $this->secret_key);
        return $hashValue;
    }

    public function processPayload($response)
    {
        $decode_payload = JWT::decode($response, new Key($this->secret_key, 'HS256'));
        $decoded_array = (array) $decode_payload;
        return $decoded_array;
    }
}
