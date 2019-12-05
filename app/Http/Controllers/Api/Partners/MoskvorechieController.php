<?php

namespace App\Http\Controllers\Api\Partners;

use App\Http\Controllers\Api\Partners\CommonParentController;
use Illuminate\Http\Request;

class MoskvorechieController extends CommonParentController
{
    public function main($query)
    {
        $url = $this->prepareUrl();
        $URI = $this->prepareURI();
        $headers = $this->prepareHeaders();
        $queryToGuzzle = $this->prepareQuery($query);
        $responseFromExtApi = $this->doGuzzle($url, $URI, $headers, $queryToGuzzle);
        //return $responseFromExtApi;
        if (empty(json_decode($responseFromExtApi)->result)) {
            return '';
        } else {
            $responseStringToFront = $this->responseStringCreate($responseFromExtApi);
            return $responseStringToFront;
        }
        //return 'MoskvorechieController';
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
            'l' => $this->partnerModelData->login,
            'p' => $this->partnerModelData->ext_key,
            'act' => 'price_by_nr_firm',
            'cs' => 'utf8',
            'nr' => $this->getFirstArticleFromQuery($query),
        ];
    }

    private function responseObjectCreate(array $arrayFromPartnerApi = [], int $indexOfArray = 0): object
    {
        $responseArray['article'] = $arrayFromPartnerApi['nr'];
        $responseArray['brand'] = $arrayFromPartnerApi['brand'];
        $responseArray['name'] = $arrayFromPartnerApi['name'];
        $responseArray['price'] = $this->convertPrice((float) $arrayFromPartnerApi['price']);
        $responseArray['quantity'] = $arrayFromPartnerApi['stock'];
        $responseArray['delivery_days'] = $this->convertDeliveryDays((int) $arrayFromPartnerApi['delivery']);
        $responseArray['comment'] = '';
        $responseArray['partner'] = $this->partnerModelData->id;

        $responseObject = (object) $responseArray;
        return $responseObject;
    }

    public function responseStringCreate(string $jsonFromPartnerApi = ''): string
    {
        // return $jsonFromPartnerApi;
        $arrayFromPartnerApi = json_decode($jsonFromPartnerApi, true); //
        $arrayOffers = $arrayFromPartnerApi['result'];
        //var_dump($arrayFromPartnerApi['result']);
        $responseArray = [];
        $responseString = '';

        foreach ($arrayOffers as $key => $value) {
            $responseArray[] = $this->responseObjectCreate($value, $key);
        }

        $responseString = json_encode($responseArray, JSON_UNESCAPED_UNICODE);
        return $responseString;
    }
}
