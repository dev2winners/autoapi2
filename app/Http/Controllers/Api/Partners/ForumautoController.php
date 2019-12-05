<?php

namespace App\Http\Controllers\Api\Partners;

use App\Http\Controllers\Api\Partners\CommonParentController;

class ForumautoController extends CommonParentController
{
    public function main($query)
    {
        $url = $this->prepareUrl();
        $URI = $this->prepareURI();
        $headers = $this->prepareHeaders();
        $queryToGuzzle = $this->prepareQuery($query);
        $responseFromExtApi = $this->doGuzzle($url, $URI, $headers, $queryToGuzzle);
        //return $responseFromExtApi;
        if (!empty(json_decode($responseFromExtApi)->errors)) {
        //if (false) {
            return '';
        } else {
            $responseStringToFront = $this->responseStringCreate($responseFromExtApi);
            return $responseStringToFront;
        } 
        //return 'ForumautoController';
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
            'login' => $this->partnerModelData->login,
            'pass' => $this->partnerModelData->password,
            'art' => $this->getFirstArticleFromQuery($query),
        ];
    }

    private function responseObjectCreate(array $arrayFromPartnerApi = [], int $indexOfArray = 0): object
    {
        $responseArray['article'] = $arrayFromPartnerApi['art'];
        $responseArray['brand'] = $arrayFromPartnerApi['brand'];
        $responseArray['name'] = $arrayFromPartnerApi['name'];
        $responseArray['price'] = $this->convertPrice((float) $arrayFromPartnerApi['price']);
        $responseArray['quantity'] = $arrayFromPartnerApi['num'];
        $responseArray['delivery_days'] = $this->convertDeliveryDays((int) $arrayFromPartnerApi['d_deliv']);
        $responseArray['comment'] = '';
        $responseArray['partner'] = $this->partnerModelData->id;

        $responseObject = (object) $responseArray;
        return $responseObject;
    }    
    
    public function responseStringCreate(string $jsonFromPartnerApi = ''): string
    {
        //return $jsonFromPartnerApi;
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
