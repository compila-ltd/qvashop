<?php

namespace App\Utility;

use App\Models\Search;

class SearchUtility
{
    public static function store($query)
    {
        if ($query != null && $query != "") {

            $search = Search::where('query', $query)->first();
            if ($search != null) {
                $search->count = $search->count + 1;
                $search->save();
            } else {
                $search = new Search;
                $search->query = $query;
                $search->count = 1;
                $search->save();
            }

        }
    }
}
