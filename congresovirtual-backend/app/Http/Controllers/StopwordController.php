<?php

namespace App\Http\Controllers;

use App\Stopword;
use App\StopwordType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StopwordController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: all users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = [];
            if(isset($request->value)) {
                $query[] = ['value', $request->value];
            }
            if(isset($request->stopword_type_id)) {
                $query[] = ['stopword_type_id', $request->stopword_type_id];
            }
            if(isset($request->object_id)) {
                $query[] = ['object_id', $request->object_id];
            }

            $distinctValue = $request->query('distinct_value', false);
            if($distinctValue) {
                $stopwords = Stopword::select(DB::raw('DISTINCT value'))->where($query);
                $totalResults = Stopword::select(DB::raw('COUNT(DISTINCT value) AS total_results'))->where($query)->pluck('total_results')->first();
            } else {
                $stopwords = Stopword::where($query);
                $totalResults = $stopwords->count();
            }

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $stopwords = $stopwords->orderBy($request->order_by, $order);
            }

            if(!isset($request->return_all) || !$request->return_all) {
                $limit = $request->query('limit', 10);
                $offset = $request->query('offset', 0);
                $stopwords = $stopwords
                    ->offset($offset)
                    ->limit($limit);
            }

            if($distinctValue) {
                $stopwords = $stopwords->get();
            } else {
                $stopwords = $stopwords->with(['stopwordType'])->get();
            }

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($stopwords),
                'results' => $stopwords
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: stopwords were not found.'], 412);
        }
    }

    /**
     * Store multiple newly created resource in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'stopwords' => 'required|array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            DB::beginTransaction();
            foreach($request->stopwords as $stopword) {
                $validator = Validator::make($stopword, [
                    'value' => 'required|string|max:191',
                    'stopword_type_id' => 'required|integer',
                    'object_id' => 'integer|nullable'
                ]);
                if($validator->fails()) {
                    return response()->json($validator->errors(), 412);
                }

                $stopwordType = StopwordType::findOrFail($stopword['stopword_type_id']);

                if(isset($stopword['object_id'])) {
                    if(!DB::table($stopwordType->table_name)->where('id', $stopword['object_id'])->first()) {
                        throw new \Exception();
                    }
                }

                $newStopword = new Stopword([
                    'value' => $stopword['value'],
                    'object_id' => isset($stopword['object_id']) ? $stopword['object_id'] : null
                ]);
                $newStopword->stopwordType()->associate($stopwordType);
                $newStopword->save();
            }
            DB::commit();
            return response()->json([
                'message' => 'Successfully created stopwords!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the stopwords were not created.'], 412);
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
            return response()->json(Stopword::with(['stopwordType'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the stopword was not found.'], 412);
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
                'value' => 'required|string|max:191'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $stopword = Stopword::findOrFail($id);
            $stopword->fill([
                'value' => $request->value
            ]);
            $stopword->save();
            return response()->json([
                'message' => 'Successfully updated stopword!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the stopword was not updated.'], 412);
        }
    }

    /**
     * Update multiples specified resources in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateMultiple(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'stopwords' => 'required|array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            DB::beginTransaction();
            foreach($request->stopwords as $stopwordModified) {
                $validator = Validator::make($stopwordModified, [
                    'id' => 'required|integer',
                    'value' => 'required|string|max:191'
                ]);
                if($validator->fails()) {
                    return response()->json($validator->errors(), 412);
                }

                $oldStopword = Stopword::findOrFail($stopwordModified['id']);
                $oldStopword->fill([
                    'value' => $stopwordModified['value']
                ]);
                $oldStopword->save();
            }
            DB::commit();
            return response()->json([
                'message' => 'Successfully updated stopwords!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the stopwords were not updated.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Stopword::findOrFail($id)->delete();
            return response()->json([
                'message' => 'Successfully deleted stopword!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the stopword was not deleted.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyMultiple(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'stopwords_id' => 'array|nullable',
                'value' => 'string|max:191|nullable',
                'stopword_type_id' => 'integer|nullable',
                'object_id' => 'integer|nullable'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            if($request->has('stopwords_id')) {
                Stopword::whereIn('id', $request->stopwords_id)->delete();
            }

            $query = [];
            if($request->has('value')) {
                $query[] = ['value', $request->value];
            }
            if($request->has('stopword_type_id')) {
                $query[] = ['stopword_type_id', $request->stopword_type_id];
            }
            if($request->has('object_id')) {
                $query[] = ['object_id', $request->object_id];
            }
            if($query) {
                Stopword::where($query)->delete();
            }

            return response()->json([
                'message' => 'Successfully deleted stopwords!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the stopwords were not deleted.'], 412);
        }
    }
}
