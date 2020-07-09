<?php

namespace App\Http\Controllers;

use App\MemberOrg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MemberOrgController extends Controller
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
            if(Auth::user()->hasRole('ADMIN')) {
                if($request->has('user_id')) {
                    $query[] = ['user_id', $request->user_id];
                }
            } else {
                if($request->has('user_id')) {
                    $query[] = ['user_id', Auth::id()];
                }
            }
            return response()->json(MemberOrg::where($query)->with(['user'])->get(), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: members of organizations were not found.'], 412);
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
                'name' => 'required|string|max:191',
                'surname' => 'required|string|max:191',
                'dni' => 'string|max:191'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $user = Auth::user();

            $memberOrg = new MemberOrg([
                'name' => $request->name,
                'surname' => $request->surname,
                'dni' => $request->dni
            ]);
            $memberOrg->user()->associate($user);
            $memberOrg->save();
            return response()->json([
                'message' => 'Successfully created member of organization!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the member of organization was not created.'], 412);
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
            $member_org = MemberOrg::with(['user'])->findOrFail($id);
            if(!(Auth::user()->hasRole('ADMIN') || Auth::id() == $member_org->user_id)) {
                throw new \Exception();
            }
            return response()->json($member_org, 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the member of organization was not found.'], 412);
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
                'name' => 'required|string|max:191',
                'surname' => 'required|string|max:191',
                'dni' => 'string|max:191'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            if(Auth::user()->hasRole('ADMIN')) {
                $memberOrg = MemberOrg::findOrFail($id);
            } else {
                $memberOrg = MemberOrg::where([
                    ['id', $id],
                    ['user_id', Auth::id()]
                ])->firstOrFail();
            }

            $memberOrg->fill([
                'name' => $request->name,
                'surname' => $request->surname,
                'dni' => $request->dni
            ]);
            $memberOrg->save();
            return response()->json([
                'message' => 'Successfully updated member of organization!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the member of organization was not updated.'], 412);
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
                $memberOrg = MemberOrg::withTrashed()->findOrFail($id);
            } else {
                $memberOrg = MemberOrg::withTrashed()
                    ->where([
                        ['id', $id],
                        ['user_id', Auth::id()]
                    ])->firstOrFail();
            }
            $forceDelete = $request->query('force_delete', false);
            if($forceDelete) {
                $memberOrg->forceDelete();
            } else {
                $memberOrg->delete();
            }
            return response()->json([
                'message' => 'Successfully deleted member of organization!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the member of organization was not deleted.'], 412);
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
                MemberOrg::onlyTrashed()->findOrFail($id)->restore();
            } else {
                MemberOrg::onlyTrashed()
                    ->where([
                        ['id', $id],
                        ['user_id', Auth::id()]
                    ])
                    ->firstOrFail()
                    ->restore();
            }
            return response()->json([
                'message' => 'Successfully undeleted member of organization!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the member of organization was not undeleted.'], 412);
        }
    }
}
