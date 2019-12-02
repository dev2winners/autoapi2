<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Partner;

class Services extends Controller
{
    public function getPartner($partnerid = null): object
    {
        if ('2' == $partnerid) {
            $partner = new \App\Http\Controllers\Api\Partners\BergController($partnerid);
        } else {
            $partner = (object) array();
        }
        return $partner;
    }
}
