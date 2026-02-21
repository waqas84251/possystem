<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SettingsController extends Controller
{
    public function index()
    {
        // Use Gate directly for Laravel 11 compatibility if needed, 
        // or just allow it for authenticated users since we are already authenticated via routes
        $groups = Setting::getGroupedSettings();
        return view('settings.index', compact('groups'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable'
        ]);

        foreach ($validated['settings'] as $key => $value) {
            // Use the correct static method from Model
            Setting::setValueByKey($key, $value);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    public function getSetting($key, $default = null)
    {
        return Setting::getValueByKey($key, $default);
    }

    public function getUserSettings()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $userSettings = [
                'theme' => $user->theme_preference ?? 'light',
                'language' => $user->language_preference ?? 'en',
            ];
            
            return response()->json($userSettings);
        }
        
        return response()->json(['error' => 'Not authenticated'], 401);
    }

    public function updateUserSettings(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            $validated = $request->validate([
                'theme' => 'sometimes|in:light,dark',
                'language' => 'sometimes|in:en,es,fr,de',
            ]);

            foreach ($validated as $key => $value) {
                if (in_array($key, ['theme', 'language'])) {
                    $user->{$key . '_preference'} = $value;
                }
            }

            $user->save();

            return response()->json(['success' => 'User settings updated']);
        }
        
        return response()->json(['error' => 'Not authenticated'], 401);
    }
}