<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Services;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function main(Services $services, Request $request, $partnerid = null)
    {
        if ($partnerid) {
            $partner = $services->getPartner($partnerid);
            $query = $request->q;
            $partner->main();
            //echo $query;
        } else {
            return 'NO PARTNER ID';
        }
    }
}
