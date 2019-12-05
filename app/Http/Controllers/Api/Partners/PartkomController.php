<?php

namespace App\Http\Controllers\Api\Partners;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartkomController extends CommonParentController
{
    public function main($query)
    {
        $url = $this->prepareUrl();
        $URI = $this->prepareURI();
        $headers = $this->prepareHeaders();
        $queryToGuzzle = $this->prepareQuery($query);
        $responseFromExtApi = $this->doGuzzle($url, $URI, $headers, $queryToGuzzle);
        if (empty(json_decode($responseFromExtApi))) {
            return '';
        } else {
            $responseStringToFront = $this->responseStringCreate($responseFromExtApi);
            return $responseStringToFront;
        }
    }

    public function prepareUrl()
    {
        return $this->partnerModelData->url;
    }

    public function prepareHeaders()
    {
        $auth64 = 'Basic ' . base64_encode($this->partnerModelData->login . ':' . $this->partnerModelData->password);
        return [
            'Authorization' => $auth64,
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ];
    }

    public function prepareQuery(string $query)
    {
        return ['number' => $this->getFirstArticleFromQuery($query)];
    }

    public function unicodeJsonConvert(string $jsonString = ''): string //unicode correction
    {
        return json_encode(json_decode($jsonString, true), JSON_UNESCAPED_UNICODE);
    }

    public function responseStringCreate(string $jsonFromPartnerApi = ''): string
    {
        
        $jsonFromPartnerApi = $this->unicodeJsonConvert($jsonFromPartnerApi);
        $arrayFromPartnerApi = json_decode($jsonFromPartnerApi, true); //
        $responseArray = $arrayFromPartnerApi;
        
        $responseString = json_encode($responseArray, JSON_UNESCAPED_UNICODE);
        //return $responseString; 
        return $jsonFromPartnerApi;
    }

}
