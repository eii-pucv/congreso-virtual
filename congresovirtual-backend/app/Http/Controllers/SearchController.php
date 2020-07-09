<?php

namespace App\Http\Controllers;

use App\Search\ElasticsearchRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: all users can use this method (see routes).
     *
     * @param ElasticsearchRepository $client
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(ElasticsearchRepository $client, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'string|nullable',
                'etapas' => 'array',
                'terms_id' => 'array',
                'is_available_voting' => 'boolean',
                'include_etapas' => 'array',
                'limit' => 'integer|nullable',
                'use_scroll' => 'boolean',
                'timeout' => 'string|nullable',
                'scroll_id' => 'string|nullable',
                'offset' => 'integer|nullable',
                'order' => 'string',
                'order_by' => 'string',
                'objects_types' => 'array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $query = $request->query('query', '');
            $etapas = $request->query('etapas', []);
            $termsIds = $request->query('terms_id', []);
            $isAvailableVoting = null;
            $includeEtapas = [];
            if(isset($request->is_available_voting)) {
                $isAvailableVoting = (bool) $request->is_available_voting;
                $includeEtapas = $request->include_etapas;
            }

            $objectsTypes = $request->query('objects_types', []);
            $limit = $request->query('limit', 10);
            $limit = $limit > 100 ? 100 : $limit;
            $orderBy = $request->query('order_by', null);
            $order = null;
            if($orderBy) {
                $order = $request->query('order', 'ASC');
            }

            $useScroll = $request->query('use_scroll', false);
            if($useScroll == true) {
                $timeout = $request->query('timeout', '30s');
                $scrollId = $request->query('scroll_id', null);
                if(!$scrollId) {
                    $response = $client->searchWithScroll($query, $etapas, $termsIds, $isAvailableVoting, $includeEtapas, $objectsTypes, $limit, $orderBy, $order, $timeout);
                } else {
                    $response = $client->searchWithScroll(null, null, null, null, null, null, null, null, null, $timeout, $scrollId);
                }
            } else {
                $offset = $request->query('offset', 0);
                $response = $client->searchWithFromAndSize($query, $etapas, $termsIds, $isAvailableVoting, $includeEtapas, $objectsTypes, $offset, $limit, $orderBy, $order);
            }
            return response()->json($response, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the results were not found.'], 412);
        }
    }
}
