<?php

namespace App\Http\Controllers\Api\Partners;

use App\Http\Controllers\Api\Partners\CommonParentController;
use Illuminate\Http\Request;

class BergController extends CommonParentController
{
    public function main($query)
    {
        $url = $this->prepareUri();
        $headers = $this->prepareHeaders();
        $queryToGuzzle = $this->prepareQuery($query);
        $responseFromExtApi = $this->doGuzzle($url, $headers, $queryToGuzzle);
        echo $responseFromExtApi;
    }

    public function prepareUri()
    {
        return $this->partnerModelData->url;
    }

    public function prepareHeaders()
    {
        return [
            'Accept' => 'application/json',
            'X-Berg-API-Key' => $this->partnerModelData->ext_key,
        ];
    }

    public function prepareQuery(string $query)
    {
        return [
            'items' => [0 => ['resource_article' => $this->getFirstArticleFromQuery($query)]],
        ];
    }

    public function doGuzzle(string $url, array $headers, array $query)
    {
        $client = new \GuzzleHttp\Client(['base_uri' => $url, 'verify' => false]);
        $apiResponse = $client->request('GET', '', [
            'query' => $query,
            'headers' => $headers,
        ]);
        return $apiResponse->getBody()->getContents();
    }
}
