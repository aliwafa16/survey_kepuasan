<?php

namespace App\Http\Controllers;

use App\Models\Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class AppearanceSettingController extends Controller
{

    public function index(){

        $settings = Setting::where('id_corporate',Auth::user()->id_corporate)->first();
        return view('settings.appearance',compact('settings'));
    }
    public function update(Request $request)
{
    $request->validate([
        'logo' => 'nullable|image|max:2048',
        'banner' => 'nullable|image|max:4096',
        'color_primary' => 'nullable|string',
        'color_secondary' => 'nullable|string',
        'tagline' => 'nullable|string|max:255',
    ]);


    // echo json_encode(auth()->user());die();

    $idCorporate = auth()->user()->f_account_id;

    $settings = Setting::firstOrCreate(['id_corporate' => $idCorporate]);

    // Upload & Simpan
    if ($request->hasFile('logo')) {
        $settings->logo = $request->file('logo')->store('appearance', 'public');
    }

    if ($request->hasFile('banner')) {
        $settings->banner = $request->file('banner')->store('appearance', 'public');
    }

    $settings->color_primary = $request->color_primary;
    $settings->color_secondary = $request->color_secondary;
    $settings->tagline = $request->tagline;
    $settings->save();

    return back()->with('success', 'Pengaturan tampilan berhasil diperbarui!');
}

}
