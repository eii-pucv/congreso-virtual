<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
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
            if($request->has('key')) {
                $query[] = ['key', $request->key];
            }
            return response()->json(Setting::where($query)->get(), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: settings were not found.'], 412);
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
                'settings' => 'required|array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            DB::beginTransaction();
            foreach($request->settings as $setting) {
                $validator = Validator::make($setting, [
                    'key' => 'required|string|max:191|unique:settings',
                    'label' => 'required|string|max:191',
                    'value' => 'string|nullable'
                ]);
                if($validator->fails()) {
                    return response()->json($validator->errors(), 412);
                }

                $newSetting = new Setting([
                    'key' => $setting['key'],
                    'label' => $setting['label'],
                    'value' => isset($setting['value']) ? $setting['value'] : null
                ]);
                $newSetting->save();
            }
            DB::commit();
            return response()->json([
                'message' => 'Successfully created settings!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the settings were not created.'], 412);
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
            return response()->json(Setting::findOrFail($id), 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the setting was not found.'], 412);
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
                'value' => 'string|nullable'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            $setting = Setting::findOrFail($id);
            $setting->fill([
                'value' => $request->value
            ]);
            $setting->save();
            return response()->json([
                'message' => 'Successfully updated setting!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the setting was not updated.'], 412);
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
                'settings' => 'required|array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            DB::beginTransaction();
            foreach($request->settings as $settingModified) {
                $validator = Validator::make($settingModified, [
                    'key' => 'required|string|max:191',
                    'value' => 'string|nullable'
                ]);
                if($validator->fails()) {
                    return response()->json($validator->errors(), 412);
                }

                $oldSetting = Setting::where('key', '=', $settingModified['key'])->firstOrFail();
                $oldSetting->fill([
                    'value' => isset($settingModified['value']) ? $settingModified['value'] : null
                ]);
                $oldSetting->save();
            }
            DB::commit();
            return response()->json([
                'message' => 'Successfully updated settings!'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error: the settings were not updated.'], 412);
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
            Setting::findOrFail($id)->delete();
            return response()->json([
                'message' => 'Successfully deleted setting!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the setting was not deleted.'], 412);
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
                'settings_id' => 'required|array'
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 412);
            }

            Setting::whereIn('id', $request->settings_id)->delete();
            return response()->json([
                'message' => 'Successfully deleted settings!'], 201);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Error: the settings were not deleted.'], 412);
        }
    }
}
