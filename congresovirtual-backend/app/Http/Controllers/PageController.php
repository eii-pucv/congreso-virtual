<?php

namespace App\Http\Controllers;

use App\Page;
use App\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     * About access control: all users can use this method (see routes).
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
            if($request->has('title')) {
                $filter['title'] = $request->title;
            }
            if($request->has('slug')) {
                $filter['slug'] = $request->slug;
            }

            if(Auth::check() && Auth::user()->hasRole('ADMIN')) {
                $filter['isPublic'] = $request->query('is_public', null);
            } else {
                $filter['isPublic'] = true;
            }

            if(Auth::check() && Auth::user()->hasRole('ADMIN') && isset($request->only_trashed) && $request->only_trashed) {
                $pages = Page::filter($filter)->onlyTrashed();
            } else {
                $pages = Page::filter($filter);
            }

            $totalResults = $pages->count();

            if($request->has('order_by')) {
                $order = $request->query('order', 'ASC');
                $pages = $pages->orderBy($request->order_by, $order);
            }
            $limit = $request->query('limit', 10);
            $offset = $request->query('offset', 0);
            $pages = $pages
                ->offset($offset)
                ->limit($limit);
            $pages = $pages->with(['terms'])->get();

            return response()->json([
                'total_results' => $totalResults,
                'returned_results' => count($pages),
                'results' => $pages
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: pages were not found.'], 412);
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
                'title' => 'required|string|max:191',
                'slug' => 'required|string|max:191|unique:pages',
                'content' => 'required|string',
                'is_public' => 'boolean',
                'terms_id' => 'array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $page = new Page([
                'title' => $request->title,
                'slug' => $request->slug,
                'content' => $request['content'],
                'is_public' => $request->has('is_public') ? $request->is_public : false
            ]);
            DB::beginTransaction();
            $page->save();

            if(isset($request->terms_id)) {
                $terms = Term::whereIn('id', $request->terms_id)->pluck('id');
                $page->terms()->sync($terms);
            }

            DB::commit();
            return response()->json([
                'message' => 'Successfully created page!',
                'data' => $page->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the page was not created.'], 412);
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
            $page = Page::with(['terms'])->findOrFail($id);

            if(!$page->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            return response()->json($page, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the page was not found.'], 412);
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
                'title' => 'required|string|max:191',
                'slug' => 'required|string|max:191|unique:pages,slug,' . $id,
                'content' => 'required|string',
                'is_public' => 'boolean'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $page = Page::findOrFail($id);
            $page->fill([
                'title' => $request->title,
                'slug' => $request->slug,
                'content' => $request['content'],
                'is_public' => $request->has('is_public') ? $request->is_public : false
            ]);
            $page->save();
            return response()->json([
                'message' => 'Successfully updated page!',
                'data' => $page->toArray()
            ], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the page was not updated.'], 412);
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

            $page = Page::withTrashed()->findOrFail($id);
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $page->forceDelete();
            } else {
                $page->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted page!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the page was not deleted.'], 412);
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
            Page::onlyTrashed()->findOrFail($id)->restore();
            return response()->json([
                'message' => 'Successfully undeleted page!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the page was not undeleted.'], 412);
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
            $page = Page::findOrFail($id);

            if(!$page->is_public && !(Auth::check() && Auth::user()->hasRole('ADMIN'))) {
                throw new \Exception();
            }

            return response()->json($page->terms, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the page was not found.'], 412);
        }
    }

    /**
     * Associate some terms with page in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function associateTerms(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'terms_id' => 'required|array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $page = Page::findOrFail($id);
            $page->terms()->sync($request->terms_id, false);
            return response()->json([
                'message' => 'Successfully associated terms with page!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the association terms with page was not created.'], 412);
        }
    }

    /**
     * Dissociate all terms with page in storage.
     * About access control: Only ADMIN type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function dissociateAllTerms(Request $request, $id)
    {
        try {
            $page = Page::findOrFail($id);
            $page->terms()->sync([]);
            return response()->json([
                'message' => 'Successfully dissociate all terms with page!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the dissociation all terms with page was not executed.'], 412);
        }
    }
}
