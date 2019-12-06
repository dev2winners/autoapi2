<?php

namespace App\Http\Controllers\Api\Partners;

use App\Http\Controllers\Api\Partners\CommonParentController;

class MpartsController extends CommonParentController
{
    public function main($query)
    {
        $brand = $this->getBrand($query);
        if ($brand) {
            $queryToGuzzle = $this->prepareQuery($query);
            $queryToGuzzle['brand'] = $brand;
            $headers = $this->prepareHeaders();
            $URI = $this->prepareURI('articles/');
            $url = $this->prepareUrl();
            $responseFromExtApi = $this->doGuzzle($url, $URI, $headers, $queryToGuzzle);
        } else {
            return '';
        }
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

    public function getBrands(string $jsonFromMpartsSearchBrands): array
    {
        $brands = json_decode($jsonFromMpartsSearchBrands, true);
        $brandsResponse = [];
        foreach ($brands as $brand) {
            $brandsResponse[] = $brand['brand'];
        }
        //var_dump($brandsResponse);
        return $brandsResponse;
    }

    public function getBrand(string $query): string
    {
        $url = $this->prepareUrl();
        $URI = $this->prepareURI('brands/');
        $headers = $this->prepareHeaders();
        $queryToGuzzle = $this->prepareQuery($query);
        $jsonFromMpartsSearchBrands = $this->doGuzzle($url, $URI, $headers, $queryToGuzzle);
        //exit($jsonFromMpartsSearchBrands);
        if (!empty(json_decode($jsonFromMpartsSearchBrands))) {
            $brand = $this->getBrands($jsonFromMpartsSearchBrands)[0];
        } else {
            $brand = '';
        }
        return $brand;
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

    private function responseObjectCreate(array $arrayFromPartnerApi = [], int $indexOfArray = 0): object
    {
        $responseArray['article'] = $arrayFromPartnerApi['number'];
        $responseArray['brand'] = $arrayFromPartnerApi['brand'];
        $responseArray['name'] = $arrayFromPartnerApi['original_article'];
        $responseArray['price'] = $this->convertPrice((float) $arrayFromPartnerApi['price']);
        $responseArray['quantity'] = $arrayFromPartnerApi['availability'];
        $responseArray['delivery_days'] = $this->convertDeliveryDays((int) (($arrayFromPartnerApi['deliveryPeriod'] + 24) / 24));
        $responseArray['comment'] = '';
        $responseArray['partner'] = $this->partnerModelData->id;

        $responseObject = (object) $responseArray;
        return $responseObject;
    }
}
