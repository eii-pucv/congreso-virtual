<?php

namespace App\Http\Controllers;

use App\TaxonomyTerm;
use App\Taxonomy;
use App\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaxonomyTermController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: all users can use this method (see routes).
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return response()->json(TaxonomyTerm::with(['taxonomy', 'term'])->get(), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: taxonomies terms were not found.'], 412);
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
                'taxonomy_id' => 'required|integer|unique:taxonomy_term,taxonomy_id,NULL,NULL,term_id,' . $request->term_id,
                'term_id' => 'required|integer|unique:taxonomy_term,term_id,NULL,NULL,taxonomy_id,' . $request->taxonomy_id
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $taxonomy = Taxonomy::findOrFail($request->taxonomy_id);
            $term = Term::findOrFail($request->term_id);

            $taxonomyTerm = new TaxonomyTerm;
            $taxonomyTerm->taxonomy()->associate($taxonomy);
            $taxonomyTerm->term()->associate($term);
            $taxonomyTerm->save();
            return response()->json([
                'message' => 'Successfully created taxonomy term!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the taxonomy term was not created.'], 412);
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
            return response()->json(TaxonomyTerm::with(['taxonomy', 'term'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the taxonomy term was not found.'], 412);
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
            TaxonomyTerm::findOrFail($id)->delete();
            return response()->json([
                'message' => 'Successfully deleted taxonomy term!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the taxonomy term was not deleted.'], 412);
        }
    }

    /**
     * Display the taxonomy associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function taxonomy($id)
    {
        try {
            return response()->json(TaxonomyTerm::findOrFail($id)->taxonomy, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the taxonomy term was not found.'], 412);
        }
    }

    /**
     * Display the term associated.
     * About access control: all users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function term($id)
    {
        try {
            return response()->json(TaxonomyTerm::findOrFail($id)->term, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the taxonomy term was not found.'], 412);
        }
    }
}
