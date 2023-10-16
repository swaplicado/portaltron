<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class companiesController extends Controller
{
    public function index(){
        $lCompanies = \DB::table('companies')
                        ->where('is_active', 1)
                        ->where('is_deleted', 0)
                        ->select(
                            'id_company as id',
                            'company_name_ui as text',
                            'logo_url',
                            'logo_mini_url',
                            'external_id',
                        )
                        ->get()
                        ->toArray();

        if(count($lCompanies) < 2){
            session()->put('companie_id', $lCompanies[0]->id);
            session()->put('companie_idDB', $lCompanies[0]->external_id);
            session()->put('companie_name', $lCompanies[0]->text);
            session()->put('companie_logo', $lCompanies[0]->logo_url);
            session()->put('companie_logo_mini', $lCompanies[0]->logo_mini_url);
            return redirect()->route('home');
        }

        return view('companies.loginCompanie')->with('lCompanies', $lCompanies);
    }

    public function setCompanie(Request $request){
        $companie = $request->companie;
        $oCompanie = \DB::table('companies')
                        ->where('id_companie', $companie)
                        ->first();

        session()->put('companie_id', $oCompanie->id_companie);
        session()->put('companie_idDB', $oCompanie->external_id);
        session()->put('companie_name', $oCompanie->company_name_ui);
        session()->put('companie_logo', $oCompanie->logo_url);
        session()->put('companie_logo_mini', $oCompanie->logo_mini_url);

        return redirect()->route('home');
    }
}
