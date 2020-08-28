<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class Admin extends Model
{
    public function getData($params = null) {
        $returnValue = '';

        try {
            $response = Http::retry(3, 100)->get(env('HOST_URL').'data');

            if ($response->successful() && array_key_exists(0, $response->json())) {
                Cache::put('fullData', $response->body());
                $returnValue = $response->body();
            } else {
                $returnValue = Cache::get('fullData');
            }

        } catch (Throwable $e) {
            $returnValue = Cache::get('fullData');
        }

        if ($params) {
            $returnValue = $this->filterData(json_decode($returnValue), $params);
        }

        return $returnValue;
    }

    private function filterData($dataArray, $params) {
        if ($params->theme) {
            $filtered = array_filter($dataArray, function($value) use ($params) {
                return $value->id == $params->theme;
            });

            if (count($filtered) > 0) {
                $dataArray = $filtered;
            }
        }

        if ($params->subtheme) {
            foreach ($dataArray as $key => $value) {
                foreach ($value->sub_themes as $id => $subtheme) {
                    if ($subtheme->id == $params->subtheme) {
                        $filtered = array_filter($dataArray, function($theme) use ($value) {
                            return $theme->id == $value->id;
                        });

                        $dataArray = $filtered;
                    }
                }
            }
        }

        if ($params->category) {
            foreach ($dataArray as $value) {
                foreach ($value->sub_themes as $subtheme) {
                    foreach($subtheme->categories as $category) {
                        if ($category->id == $params->category) {
                            $filtered = array_filter($dataArray, function($theme) use ($value) {
                                return $theme->id == $value->id;
                            });

                            $dataArray = $filtered;
                        }
                    }
                }
            }
        }

        return json_encode($dataArray);
    }
}
