<?php

namespace App\Http\Controllers;

use App\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaxonomyController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: all users can use this method (see routes).
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $filter = [];
            if($request->has('query')) {
                $filter['query'] = $request['query'];
            }

            $taxonomies = Taxonomy::filter($filter);
            $totalResults = $taxonomies->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $taxonomies = $taxonomies->orderBy($request->order_by, $order);
            }

            if(!isset($request->return_all) || !$request->return_all) {
                $limit = $request->query('limit', 10);
                $offset = $request->query('offset', 0);
                $taxonomies = $taxonomies
                    ->offset($offset)
                    ->limit($limit > 100 ? 100 : $limit);
            }
            $taxonomies = $taxonomies->with(['terms'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($taxonomies),
                'results' => $taxonomies
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: taxonomies were not found.'], 412);
        }
    }

    /**
     * Store a newly created resource in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'value' => 'required|string|max:191|unique:taxonomies'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $taxonomy = new Taxonomy([
                'value' => $request->value
            ]);
            $taxonomy->save();
            return response()->json([
                'message' => 'Successfully created taxonomy!',
                'data' => $taxonomy->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the taxonomy was not created.'], 412);
        }
    }

    /**
     * Display the specified resource.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return response()->json(Taxonomy::with(['terms'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the taxonomy was not found.'], 412);
        }
    }

    /**
     * Update the specified resource in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'value' => 'required|string|max:191|unique:taxonomies,value,' . $id
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $taxonomy = Taxonomy::findOrFail($id);
            $taxonomy->fill([
                'value' => $request->value
            ]);
            $taxonomy->save();
            return response()->json([
                'message' => 'Successfully updated taxonomy!',
                'data' => $taxonomy->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the taxonomy was not updated.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'force_delete' => 'boolean'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $taxonomy = Taxonomy::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $taxonomy->forceDelete();
            } else {
                $taxonomy->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted taxonomy!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the taxonomy was not deleted.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage temporarily.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function undelete($id)
    {
        try {
            Taxonomy::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted taxonomy!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the taxonomy was not undeleted.'], 412);
        }
    }

    /**
     * Display the terms associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function terms($id)
    {
        try {
            return response()->json(Taxonomy::findOrFail($id)->terms, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the taxonomy was not found.'], 412);
        }
    }
}
