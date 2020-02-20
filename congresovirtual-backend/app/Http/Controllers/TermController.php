<?php

namespace App\Http\Controllers;

use App\Taxonomy;
use App\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TermController extends Controller
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

            $whereAndFilter = [];
            if(isset($request->parent_id)) {
                $whereAndFilter[] = ['parent_id', $request->parent_id];
            }

            $terms = Term::filter($filter)->where($whereAndFilter);
            $totalResults = Term::filter($filter)->where($whereAndFilter)->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $terms = $terms->orderBy($request->order_by, $order);
            }

            if(!isset($request->return_all) || !$request->return_all) {
                $limit = $request->query('limit', 10);
                $offset = $request->query('offset', 0);
                $terms = $terms
                    ->offset($offset)
                    ->limit($limit);
            }
            $terms = $terms->with(['parent', 'taxonomies', 'children'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($terms),
                'results' => $terms
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: terms were not found.'], 412);
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
                'value' => 'required|string|max:191',
                'parent_id' => 'integer|nullable',
                'taxonomies_id' => 'array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $parent = null;
            if(isset($request->parent_id)) {
                $parent = Term::findOrFail($request->parent_id);
            }

            $term = new Term([
                'value' => $request->value
            ]);
            $term->parent()->associate($parent);
            DB::beginTransaction();
            $term->save();

            if(isset($request->taxonomies_id)) {
                $taxonomies = Taxonomy::whereIn('id', $request->taxonomies_id)->pluck('id');
                $term->taxonomies()->sync($taxonomies);
            }

            $data = $term->toArray();
            $data['taxonomies'] = $term->taxonomies;
            DB::commit();

            return response()->json([
                'message' => 'Successfully created term!',
                'data' => $data
            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the term was not created.'], 412);
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
            return response()->json(Term::with(['parent', 'taxonomies', 'children'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the term was not found.'], 412);
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
                'value' => 'required|string|max:191',
                'parent_id' => 'integer|nullable'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $parent = null;
            if($request->has('parent_id')) {
                if(isset($request->parent_id)) {
                    $parent = Term::findOrFail($request->parent_id);
                }
            }

            $term = Term::findOrFail($id);
            $term->fill([
                'value' => $request->value
            ]);
            if($request->has('parent_id')) {
                $term->parent()->associate($parent);
            }
            $term->save();
            return response()->json([
                'message' => 'Successfully updated term!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the term was not updated.'], 412);
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

            $term = Term::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $term->forceDelete();
            } else {
                $term->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted term!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the term was not deleted.'], 412);
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
            Term::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted term!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the term was not undeleted.'], 412);
        }
    }

    /**
     * Display the taxonomies associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function taxonomies($id)
    {
        try {
            return response()->json(Term::findOrFail($id)->taxonomies, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the term was not found.'], 412);
        }
    }
}
