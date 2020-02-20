<?php

namespace App\Http\Controllers;

use App\LocationOrg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationOrgController extends Controller
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
            if($request->has('user_id')) {
                $query[] = ['user_id', $request->user_id];
            }
            return response()->json(LocationOrg::where($query)->with(['user'])->get(), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: locations of organizations were not found.'], 412);
        }
    }

    /**
     * Store a newly created resource in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'es_principal' => 'boolean',
                'direccion' => 'required|string|max:191',
                'pais' => 'required|string|max:191',
                'region' => 'string|max:191',
                'comuna' => 'string|max:191',
                'sector' => 'required|integer'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $user = Auth::user();

            $locationOrg = new LocationOrg([
                'es_principal' => $request->has('es_principal') ? $request->es_principal : false,
                'direccion' => $request->direccion,
                'pais' => $request->pais,
                'region' => $request->region,
                'comuna' => $request->comuna,
                'sector' => $request->sector
            ]);
            $locationOrg->user()->associate($user);
            $locationOrg->save();
            return response()->json([
                'message' => 'Successfully created location of organization!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the location of organization was not created.'], 412);
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
            return response()->json(LocationOrg::with(['user'])->findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the location of organization was not found.'], 412);
        }
    }

    /**
     * Update the specified resource in storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'es_principal' => 'boolean',
                'direccion' => 'required|string|max:191',
                'pais' => 'required|string|max:191',
                'region' => 'string|max:191',
                'comuna' => 'string|max:191',
                'sector' => 'required|integer',
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            if(Auth::user()->hasRole('ADMIN')) {
                $locationOrg = LocationOrg::findOrFail($id);
            } else {
                $locationOrg = LocationOrg::where([
                    ['id', $id],
                    ['user_id', Auth::id()]
                ])->firstOrFail();
            }

            $locationOrg->fill([
                'es_principal' => $request->has('es_principal') ? $request->es_principal : false,
                'direccion' => $request->direccion,
                'pais' => $request->pais,
                'region' => $request->region,
                'comuna' => $request->comuna,
                'sector' => $request->sector
            ]);
            $locationOrg->save();
            return response()->json([
                'message' => 'Successfully updated location of organization!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the location of organization was not updated.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage.
     * About access control: ADMIN and USER type users can use this method (see routes).
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

            if(Auth::user()->hasRole('ADMIN')) {
                $locationOrg = LocationOrg::withTrashed()->findOrFail($id);
            } else {
                $locationOrg = LocationOrg::withTrashed()
                    ->where([
                        ['id', $id],
                        ['user_id', Auth::id()]
                    ])->firstOrFail();
            }
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $locationOrg->forceDelete();
            } else {
                $locationOrg->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted location of organization!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the location of organization was not deleted.'], 412);
        }
    }

    /**
     * Remove the specified resource from storage temporarily.
     * About access control: ADMIN and USER type users can use this method (see routes).
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function undelete($id)
    {
        try {
            if(Auth::user()->hasRole('ADMIN')) {
                LocationOrg::onlyTrashed()->findOrFail($id)->restore();
            } else {
                LocationOrg::onlyTrashed()
                    ->where([
                        ['id', $id],
                        ['user_id', Auth::id()]
                    ])
                    ->firstOrFail()
                    ->restore();
            }
            return response()->json([
                'message' => 'Successfully undeleted location of organization!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the location of organization was not undeleted.'], 412);
        }
    }
}
