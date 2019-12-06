<?php

namespace App\Http\Controllers\Api\Partners;

use App\Http\Controllers\Api\Partners\CommonParentController;

class ShatemController extends CommonParentController
{
    public function main($query)
    {
        //return $this->getToken();
        $article = $this->getFirstArticleFromQuery($query);
        $url = $this->prepareUrl();
        $URI = $this->prepareURI('api/search/GetPricesByArticle'); //not working
        //$URI = $this->prepareURI('api/search/GetTradeMarksByArticleCode/' . $article); //its working
        $headers = $this->prepareHeaders();
        $queryToGuzzle = $this->prepareQuery($query);
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
            'Token' => $this->getToken(),
        ];
    }

    public function prepareQuery(string $query)
    {
        return [
            /* 'ArticleCode' => 'Op595',
            'TradeMarkName' => 'HJS',
            'TradeMarkId' => '118',
            'IncludeAnalogs' => false, ******* its working *******/ 
            'ArticleCode' => 'Op595', //обязательный
            'TradeMarkName' => 'HJS', //обязательный
            'TradeMarkId' => '118', //обязательный
            //'IncludeAnalogs' => false,
        ];
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
