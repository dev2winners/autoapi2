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
        $this->partnerModelData = \App\Partner::where('ext_id', $extID)->first();
    }

    public function main()
    {
        echo $this->partnerModelData->login;
    }
}
