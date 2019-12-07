<?php

namespace App\Http\Controllers\Api\Partners;

use App\Http\Controllers\Api\Partners\CommonParentController;

class ShatemController extends CommonParentController
{
    public $token = '';

    public function init()
    {
        $this->token = $this->getToken();
    }

    public function main($query)
    {
        $this->init();
        $article = $this->getFirstArticleFromQuery($query);
        $url = $this->prepareUrl();
        $headers = $this->prepareHeaders();
        
        $TradeMarksByArticleCode = $this->GetTradeMarksByArticleCode($url, $article, $headers);
        $queryToGuzzle = $this->prepareQuery($query);
        $queryToGuzzle = $this->QueryUpdateForTradeMarks($this->GetTradeMarksFromJson($TradeMarksByArticleCode), $queryToGuzzle, $article);
        
        $URI = $this->prepareURI('api/search/GetPricesByArticle'); //

        $responseFromExtApi = $this->doGuzzle($url, $URI, $headers, $queryToGuzzle);
        return $responseFromExtApi;
        if (!empty(json_decode($responseFromExtApi)->errors)) {
            //if (false) {
            return '';
        } else {
            $responseStringToFront = $this->responseStringCreate($responseFromExtApi);
            return $responseStringToFront;
        }
        //return 'ShatemController';
    }

    public function prepareUrl()
    {
        return $this->partnerModelData->url;
    }

    public function prepareHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Token' => $this->token,
        ];
    }

    public function prepareQuery(string $query)
    {
        return [];
            
    }


    public function GetTradeMarksByArticleCode($url, $article, $headers)
    {
        $URI = $this->prepareURI('api/search/GetTradeMarksByArticleCode/' . $article);
        $query = [];
        $responseFromExtApi = $this->doGuzzle($url, $URI, $headers, $query);
        return $responseFromExtApi;
    }

    public function GetTradeMarksFromJson(string $json) : array
    {
        $trademarks = json_decode($json, true)['TradeMarkByArticleCodeModels'];
        return $trademarks;
    }

    public function QueryUpdateForTradeMarks(array $trademarks, array $query, string $article) : array
    {
        exit(print_r($trademarks));
        $query['ArticleCode'] = $article;
        $query['TradeMarkName'] = $trademarks[0]['TradeMarkName']; //working for 'Op595' on index [1]
        $query['TradeMarkId'] = $trademarks[0]['TradeMarkId'];
        $query['IncludeAnalogs'] = false;
        return $query;
    }

    public function getToken()
    {
        $ch_url = $this->prepareUrl();
        $Login = $this->partnerModelData->login;
        $Password = $this->partnerModelData->password;
        $ApiKey = $this->partnerModelData->ext_key;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ch_url . "login");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $Login . ":" . $Password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "ApiKey=" . $ApiKey);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $response = curl_exec($ch);
        $headers = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
        $headers = explode("\r\n", $headers);

        foreach ($headers as $header) {
            if (strpos($header, 'Token:') !== false) {
                $token = array($header);
            }
        }
        curl_close($ch);
        return trim(explode(':', $token[0])[1]);
    }
}
