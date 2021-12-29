<?php

namespace NovaComponents\DaDataSuggestion\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use Dadata\DadataClient;

class DaDataSuggestionController extends Controller
{
    public function query(NovaRequest $request): JsonResponse
    {
        $query = trim($request->input('q'));
        if (!$query)
        {
            return response()->json([]);
        }

        try {
            $dadata = new DadataClient(config('dadata.token'), config('dadata.secret'));
            $responseRu = $dadata->suggest(
                'address',
                $request->input('q'),
                config('dadata.count', 3),
                [
                    'language' => 'ru'
                ]
            );
            $responseEn = $dadata->suggest(
                'address',
                $request->input('q'),
                config('dadata.count', 3),
                [
                    'language' => 'en'
                ]
            );

            if (array_key_exists('value', $responseRu)){
                $responseRu = [$responseRu];
            }
            if (array_key_exists('value', $responseEn)){
                $responseEn = [$responseEn];
            }
        } catch (Exception $exception){
            $response = [];
        }
        return response()->json([
            'ru'    => $responseRu,
            'en'    => $responseEn
        ]);
    }
}
