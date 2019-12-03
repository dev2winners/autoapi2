<?php

namespace App\Http\Controllers\Api\Partners;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Partner;

class CommonParentController extends Controller
{

    public $partnerModelData;

    public function __construct($extID)
    {
        $this->partnerModelData = $this->getModelData($extID);
    }

    public function main($query)
    {
        echo $this->partnerModelData->login . '    ' . $this->partnerModelData->ext_id.'  '.$query;
    }

    public function getModelData($extID)
    {
        return \App\Partner::where('ext_id', $extID)->first();
    }

    public function getArticlesFromQuery(string $query = '') //
    {
        $articles = [];
        $queryParts = explode(' ', $query);
        foreach ($queryParts as $part) {
            if (preg_match('/[\d]+/', trim($part))) {
                $articles[] = trim($part);
            }
        }
        return $articles;
    }

    public function getFirstArticleFromQuery(string $query = '') //
    {
        $articles = $this->getArticlesFromQuery($query);
        if (!empty($articles)) {
            return $articles[0];
        } else {
            return false;
        }
    }
    
    /**** Prepare Vars for Guzzle *****/
    public function prepareUri() {} //override in children
    public function prepareHeaders() {} //override in children
    public function prepareQuery(string $query) {} //override in children
    public function doGuzzle(string $url, array $headers, array $query) {} //override in children

    protected function convertPrice(float $price) //
    {
        return ceil($this->partnerModelData->price_ratio * $price);
    }

    protected function convertDeliveryDays(int $deliveryDays) //
    {
        return 1 + $deliveryDays;
    }

}