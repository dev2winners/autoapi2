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

    private function responseObjectCreate(array $arrayFromPartnerApi = [], int $indexOfArray = 0): object
    {
        //var_dump($arrayFromPartnerApi);
        $responseArray['article'] = $arrayFromPartnerApi[$indexOfArray]['number'];
        $responseArray['brand'] = $arrayFromPartnerApi[$indexOfArray]['maker'];
        $responseArray['name'] = $arrayFromPartnerApi[$indexOfArray]['makerId'];
        $responseArray['price'] = $this->convertPrice((float) $arrayFromPartnerApi[$indexOfArray]['price']);
        $responseArray['quantity'] = $arrayFromPartnerApi[$indexOfArray]['quantity'];
        $responseArray['delivery_days'] = $this->convertDeliveryDays((int) $arrayFromPartnerApi[$indexOfArray]['expectedDays']);
        $responseArray['comment'] = '';
        $responseArray['partner'] = $this->partnerModelData->id;

        $responseObject = (object) $responseArray;
        return $responseObject;
    }

    public function responseStringCreate(string $jsonFromPartnerApi = ''): string
    {
        
        //$jsonFromPartnerApi = $this->unicodeJsonConvert($jsonFromPartnerApi);
        $jsonFromPartnerApi = file_get_contents(app_path() . '/TEMP/Partkom.json');
        $arrayOffers = $arrayFromPartnerApi = json_decode($jsonFromPartnerApi, true); //
        
        //return json_encode($arrayOffers, JSON_UNESCAPED_UNICODE);
        
        $responseArray = [];
        $responseString = '';
        
        foreach ($arrayOffers as $key => $value) {
            $responseArray[] = $this->responseObjectCreate($arrayOffers, $key);
        }

        $responseString = json_encode($responseArray, JSON_UNESCAPED_UNICODE);
        return $responseString;
    }

}
