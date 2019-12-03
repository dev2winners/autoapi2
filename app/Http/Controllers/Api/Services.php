<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Partner;

class Services extends Controller
{

    public $partners = [
        '2' => 'Berg',
    ];

    public function getPartner($partnerid = null): object
    {
        if (array_key_exists($partnerid, $this->partners)) {
            $partnerClass = '\App\Http\Controllers\Api\Partners\\' . $this->partners[$partnerid] . 'Controller';
            $partner = new $partnerClass($partnerid);
        } else {
            $partner = (object) array();
        }
        return $partner;
    }
}
