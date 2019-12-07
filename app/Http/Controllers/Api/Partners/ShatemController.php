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
        $trademarks = $this->GetTradeMarksFromJson($TradeMarksByArticleCode);
        $URI = $this->prepareURI('api/search/GetPricesByArticle'); //

        $responseFromExtApi = '';
        $resultArrayFromExtApi = [];

        foreach ($trademarks as $i => $trademark) {
            $queryToGuzzle = $this->QueryCreateForTradeMarks($trademark, $article, $i);

            $response = $this->doGuzzle($url, $URI, $headers, $queryToGuzzle);
            $responseArray = json_decode($response, true)['PriceModels'];
            if (!empty($responseArray)) {
                //$responseFromExtApi .= $response;
                $resultArrayFromExtApi = array_merge($resultArrayFromExtApi, $responseArray);
            }
        }
        
        $responseToFront = $this->createFinalResponse($resultArrayFromExtApi);
        
        $responseToFront = json_encode($responseToFront, JSON_UNESCAPED_UNICODE);
        return $responseToFront;

        /* if (false) {
            return '';
        } else {
            $responseStringToFront = $this->responseStringCreate($responseFromExtApi);
            return $responseStringToFront;
        } */
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

    public function GetTradeMarksFromJson(string $json): array
    {
        $trademarks = json_decode($json, true)['TradeMarkByArticleCodeModels'];
        return $trademarks;
    }

    public function QueryCreateForTradeMarks(array $trademark, string $article, $i): array
    {
        //exit(print_r($trademarks));
        $query['ArticleCode'] = $article;
        $query['TradeMarkName'] = $trademark['TradeMarkName']; //working for 'Op595' on index [1]
        $query['TradeMarkId'] = $trademark['TradeMarkId'];
        $query['IncludeAnalogs'] = false;
        return $query;
    }

    public function createFinalResponse(array $dataFromExtApi): array
    {
        $result = [];

        foreach ($dataFromExtApi as $brand) {
            foreach ($brand['ArticlePriceInfo'] as $offer) {

                $a['article'] = $brand['ArticleCode'];
                $a['brand'] = $brand['TradeMarkName'];
                $a['name'] = $brand['TradeMarkName'] . ' ' . $brand['ArticleCode'];
                $a['price'] = $this->convertPrice((float) $offer['Price']);
                $a['quantity'] = $offer['Qty'];
                $a['delivery_days'] = $this->convertDeliveryDays((int) $offer['DeliveryTerm']);
                $a['comment'] = '';
                $a['partner'] = $this->partnerModelData->id;

                $o = (object) $a;
                $result[] = $o;
            }
        }
        return $result;
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
