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

    public function main()
    {
        echo $this->partnerModelData->login . '    ' . $this->partnerModelData->ext_id;
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
}
