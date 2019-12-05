<?php

namespace App\Http\Controllers\Api\Partners;

use App\Http\Controllers\Api\Partners\CommonParentController;

class MpartsController extends CommonParentController
{
    public function main($query)
    {
        $url = $this->prepareUrl();
        $URI = $this->prepareURI();
        $headers = $this->prepareHeaders();
        $queryToGuzzle = $this->prepareQuery($query);
        $responseFromExtApi = $this->doGuzzle($url, $URI, $headers, $queryToGuzzle);
        //return $responseFromExtApi;
        //if (!empty(json_decode($responseFromExtApi)->errors)) {
        if (false) {
            return '';
        } else {
            $responseStringToFront = $this->responseStringCreate($responseFromExtApi);
            return $responseStringToFront;
        }
        //return 'MpartsController';
    }

    public function prepareUrl()
    {
        return $this->partnerModelData->url;
    }

    public function prepareHeaders()
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    public function prepareQuery(string $query)
    {
        return [
            'userlogin' => $this->partnerModelData->login,
            'userpsw' => md5($this->partnerModelData->password),
            'number' => $this->getFirstArticleFromQuery($query),
        ];
    }

    public function responseStringCreate(string $jsonFromPartnerApi = ''): string
    {
        return $jsonFromPartnerApi;
        $arrayFromPartnerApi = json_decode($jsonFromPartnerApi, true); //
        $arrayOffers = $arrayFromPartnerApi;
        //var_dump($arrayOffers);
        $responseArray = [];
        $responseString = '';

        foreach ($arrayOffers as $key => $value) {
            $responseArray[] = $this->responseObjectCreate($value, $key);
        }

        $responseString = json_encode($responseArray, JSON_UNESCAPED_UNICODE);
        return $responseString;
    }
}
