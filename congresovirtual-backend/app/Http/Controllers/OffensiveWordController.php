<?php

namespace App\Http\Controllers;

use App\OffensiveWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OffensiveWordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $filter = [];
            if($request->has('query')) {
                $filter['query'] = $request['query'];
            }

            $offensiveWords = OffensiveWord::filter($filter);
            $totalResults = $offensiveWords->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $offensiveWords = $offensiveWords->orderBy($request->order_by, $order);
            }

            if(!isset($request->return_all) || !$request->return_all) {
                $limit = $request->query('limit', 10);
                $offset = $request->query('offset', 0);
                $offensiveWords = $offensiveWords
                    ->offset($offset)
                    ->limit($limit);
            }

            $offensiveWords = $offensiveWords->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($offensiveWords),
                'results' => $offensiveWords
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: offensive words were not found.'], 412);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'word' => 'required|string'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $offensive_word = new OffensiveWord([
                'word' => $request->word
            ]);
            $offensive_word->save();
            return response()->json([
                'message' => 'Successfully created offensive word'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the offensive word was not created.'], 412);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OffensiveWord  $offensiveWord
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return response()->json(OffensiveWord::findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the offensive word was not found.'], 412);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OffensiveWord  $offensiveWord
     * @return \Illuminate\Http\Response
     */
    public function edit(OffensiveWord $offensiveWord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OffensiveWord  $offensiveWord
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'word' => 'required|string'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $offensiveWord = OffensiveWord::findOrFail($id);
            $offensiveWord->fill([
                'word' => $request->word
            ]);
            $offensiveWord->save();
            return response()->json([
                'message' => 'Successfully updated offensive word!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the offensive word was not updated.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OffensiveWord  $offensiveWord
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            OffensiveWord::findOrFail($id)->delete();
            return response()->json([
                'message' => 'Successfully deleted offensive word!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the offensive word was not deleted.'], 412);
        }
    }
}
