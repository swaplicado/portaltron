<?php

namespace App\Http\Controllers\Quotations;

use App\Constants\SysConst;
use App\Http\Controllers\Controller;
use App\Models\Quotations\Quotation;
use App\Utils\FilesUtils;
use App\Utils\QuotationsUtils;
use App\Utils\SysUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class QuotationsController extends Controller
{
    public function index(){
        $lQuotations = QuotationsUtils::getlQuotationsByUser(\Auth::user()->id);
        $lQuotations = SysUtils::collectToArray($lQuotations);

        return view('quotations.quotations')->with('lQuotations', $lQuotations);
    }

    public function uploadQuotation(Request $request){
        try {
            $folioUser = $request->folio;
            $description = $request->description;
            $provider_id = session()->get('provider_id');
            
            $pdf = $request->file('pdf');
            
            $result = FilesUtils::validateFile($pdf, 'pdf', '20 MB');
            if(!$result[0]){
                return json_encode(['success' => false, 'message' => $result[1]]);
            }
            
            $fileName = $folioUser.'_'.time().'.'.$pdf->extension();  
            $pdf->move(public_path('PDFs'), $fileName);

            \DB::beginTransaction();
            $count = (\DB::table('quotations')->where('provider_id', $provider_id)->count() + 1);
            $pdf_path = public_path('PDFs') . '/' . $fileName;
            
            $oQuotation = new Quotation();
            $oQuotation->provider_id = $provider_id;
            $oQuotation->folio_system = $count;
            $oQuotation->folio_user = $folioUser;
            $oQuotation->description = $description;
            $oQuotation->pdf_path = $pdf_path;
            $oQuotation->pdf_original_name = $pdf->getClientOriginalName();
            $oQuotation->created_by = \Auth::user()->id;
            $oQuotation->updated_by = \Auth::user()->id;
            $oQuotation->save();

            $lQuotations = QuotationsUtils::getlQuotationsByUser(\Auth::user()->id);
            $lQuotations = SysUtils::collectToArray($lQuotations);

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lQuotations' => $lQuotations]);
    }

    public function showQuotation($id_quotation){
        try {
            if(SysUtils::isAdmin()){
                $oQuotation = Quotation::findOrFail($id_quotation);
            }else{
                $oQuotation = Quotation::where('provider_id', session()->get('provider_id'))
                                        ->where('id_quotation', $id_quotation)
                                        ->first();
            }

            // Get the path to the PDF file
            $pdf_path = $oQuotation->pdf_path;

            // Check if the file exists
            if (!\File::exists($pdf_path)) {
                abort(404, 'The PDF file was not found.');
            }

            $fileContents = file_get_contents($pdf_path);

            $filename = $oQuotation->pdf_original_name.time();

        } catch (\Throwable $th) {
            abort(404, 'The PDF file was not found.');
        }

        // header('Content-Type: application/pdf');
        // header('Content-Disposition: inline; filename="'.$filename.'"');

        // echo $fileContents;

        header('Content-Type: application/pdf');
        header("Content-Disposition: inline; filename=$filename");
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        @readfile($pdf_path);
    }

    public function updateQuotation(Request $request){
        try {
            $folioUser = $request->folio;
            $description = $request->description;
            
            $oQuotation = Quotation::findOrfail($request->id_quotation);

            if (File::exists($oQuotation->pdf_path)) {
                $path = $oQuotation->pdf_path;
                File::delete($path);
            }

            $pdf = $request->file('pdf');
            
            $result = FilesUtils::validateFile($pdf, 'pdf', '20 MB');
            if(!$result[0]){
                return json_encode(['success' => false, 'message' => $result[1]]);
            }
            
            $fileName = $folioUser.'_'.time().'.'.$pdf->extension();  
            $pdf->move(public_path('PDFs'), $fileName);

            \DB::beginTransaction();
            $pdf_path = public_path('PDFs') . '/' . $fileName;
            
            $oQuotation->folio_user = $folioUser;
            $oQuotation->description = $description;
            $oQuotation->pdf_path = $pdf_path;
            $oQuotation->pdf_original_name = $pdf->getClientOriginalName();
            $oQuotation->updated_by = \Auth::user()->id;
            $oQuotation->update();

            $lQuotations = QuotationsUtils::getlQuotationsByUser(\Auth::user()->id);
            $lQuotations = SysUtils::collectToArray($lQuotations);

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lQuotations' => $lQuotations]);
    }

    public function deleteQuotation(Request $request){
        try {
            $oQuotation = Quotation::findOrfail($request->id_quotation);

            if (File::exists($oQuotation->pdf_path)) {
                $path = $oQuotation->pdf_path;
                File::delete($path);
            }

            \DB::beginTransaction();

            $oQuotation->delete();

            $lQuotations = QuotationsUtils::getlQuotationsByUser(\Auth::user()->id);
            $lQuotations = SysUtils::collectToArray($lQuotations);

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }
        return json_encode(['success' => true, 'lQuotations' => $lQuotations]);
    }
}
