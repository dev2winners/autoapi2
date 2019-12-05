<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Partner;

class Services extends Controller
{

    private $partners = [
        '1' => 'Partkom',
        '2' => 'Berg',
        '3' => 'Moskvorechie',
        '4' => 'Forumauto',
        '5' => 'Mparts',
    ];

    public function getPartner($partnerid = null): object
    {
        if (array_key_exists($partnerid, $this->partners)) {
            $partnerClass = '\App\Http\Controllers\Api\Partners\\' . $this->partners[$partnerid] . 'Controller';
            $partner = new $partnerClass($partnerid);
        } else {
            $partner = json_decode(json_encode(['partnerModelData' => ['ext_id' => 0]])); //magic ))) recursive translate array to obj
        }
        return $partner;
    }
}
