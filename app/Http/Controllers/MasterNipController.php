<?php
namespace App\Http\Controllers;

use App\Models\MasterNip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterNipController extends Controller
{
    public function checkNip(Request $request)
    {
        $nip = $request->query('nip');
        $id_account = $request->query('id_account');
        $exists = DB::table('t_master_nip')
            ->where('nip', $nip)
            ->where('f_survey_valid', 'no')
            ->whereRaw('sha1(id_account) = ?', [$id_account])
            ->exists();


//    return response()->json($exists);

        return response()->json([
            'available' => $exists
        ]);
    }
}
