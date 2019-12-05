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
        return $responseFromExtApi;
        if (empty(json_decode($responseFromExtApi)->resources)) {
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
            'act' => 'brand_by_nr',
            'cs' => 'utf8',
            'nr' => $this->getFirstArticleFromQuery($query),
        ];
    }
}
