<?php

namespace App\Http\Controllers;

use App\StopwordType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class StopwordTypeController extends Controller
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
            if(isset($request->label)) {
                $query[] = ['label', $request->label];
            }
            if(isset($request->table_name)) {
                $query[] = ['table_name', $request->table_name];
            }
            $stopwordTypes = StopwordType::where($query);

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $stopwordTypes = $stopwordTypes->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $stopwordTypes = $stopwordTypes
                ->offset($offset)
                ->limit($limit);
            $stopwordTypes = $stopwordTypes->get();

            return response()->json($stopwordTypes, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: stopword types were not found.'], 412);
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
                'label' => 'required|string|max:191|unique:stopword_types',
                'table_name' => 'string|max:191|nullable|unique:stopword_types'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            if(isset($request->table_name)) {
                if (!Schema::hasTable($request->table_name)) {
                    throw new \Exception();
                }
            }

            $stopwordType = new StopwordType([
                'label' => $request->label,
                'table_name' => $request->table_name
            ]);
            $stopwordType->save();
            return response()->json([
                'message' => 'Successfully created stopword type!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the stopword type was not created.'], 412);
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
            return response()->json(StopwordType::with(['stopwords'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the stopword type was not found.'], 412);
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
                'label' => 'required|string|max:191|unique:stopword_types,label,' . $id,
                'table_name' => 'string|max:191|nullable|unique:stopword_types,table_name,' . $id
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            if(isset($request->table_name)) {
                if (!Schema::hasTable($request->table_name)) {
                    throw new \Exception();
                }
            }

            $stopwordType = StopwordType::findOrFail($id);
            $stopwordType->fill([
                'label' => $request->label,
                'table_name' => $request->table_name
            ]);
            $stopwordType->save();
            return response()->json([
                'message' => 'Successfully updated stopword type!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the stopword type was not updated.'], 412);
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
            StopwordType::findOrFail($id)->delete();
            return response()->json([
                'message' => 'Successfully deleted stopword type!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the stopword type was not deleted.'], 412);
        }
    }
}
