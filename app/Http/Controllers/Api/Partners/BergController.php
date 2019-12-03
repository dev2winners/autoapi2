<?php

namespace App\Http\Controllers\Api\Partners;

use App\Http\Controllers\Api\Partners\CommonParentController;
use Illuminate\Http\Request;

class BergController extends CommonParentController
{
    public function main($query)
    {
        $url = $this->prepareUrl();
        $URI = $this->prepareURI();
        $headers = $this->prepareHeaders();
        $queryToGuzzle = $this->prepareQuery($query);
        $responseFromExtApi = $this->doGuzzle($url, $URI, $headers, $queryToGuzzle);
        if (empty(json_decode($responseFromExtApi)->resources)) {
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

    private function responseObjectCreate(array $arrayFromPartnerApi = [], int $indexOfArray = 0): object
    {
        $responseArray['article'] = $arrayFromPartnerApi['resources'][0]['article'];
        $responseArray['brand'] = $arrayFromPartnerApi['resources'][0]['brand']['name'];
        $responseArray['name'] = $arrayFromPartnerApi['resources'][0]['name'];
        $responseArray['price'] = $this->convertPrice((float) $arrayFromPartnerApi['resources'][0]['offers'][$indexOfArray]['price']);
        $responseArray['quantity'] = $arrayFromPartnerApi['resources'][0]['offers'][$indexOfArray]['quantity'];
        $responseArray['delivery_days'] = $this->convertDeliveryDays((int) $arrayFromPartnerApi['resources'][0]['offers'][$indexOfArray]['assured_period']);
        $responseArray['comment'] = '';
        $responseArray['partner'] = $this->partnerModelData->id;

        $responseObject = (object) $responseArray;
        return $responseObject;
    }

    public function responseStringCreate(string $jsonFromPartnerApi = ''): string
    {
        // return $jsonFromPartnerApi;
        $arrayFromPartnerApi = json_decode($jsonFromPartnerApi, true); //
        $arrayOffers = $arrayFromPartnerApi['resources'][0]['offers'];
        $responseArray = [];
        $responseString = '';

        foreach ($arrayOffers as $key => $value) {
            $responseArray[] = $this->responseObjectCreate($arrayFromPartnerApi, $key);
        }

        $responseString = json_encode($responseArray, JSON_UNESCAPED_UNICODE);
        return $responseString;
    }
}
