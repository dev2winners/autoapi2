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
            //var_dump($partner);
            if ($partner->partnerModelData->ext_id) {
                if (isset($request->q)) {
                    $query = $request->q;
                    $partner->main($query);
                    //echo $query;
                } else {
                    return response('{"request error":"no query given"}', 404);
                }
            } else {
                return response('{"request error":"partner not found"}', 404);
            }
        } else {
            return response('{"request error":"no partner id given"}', 404);
        }
    }
}
