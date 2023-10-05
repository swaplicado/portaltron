<?php

namespace App\Http\Controllers\SDocs;

use App\Constants\SysConst;
use App\Http\Controllers\Controller;
use App\Models\SDocs\VoboDoc;
use App\Utils\SProvidersUtils;
use Illuminate\Http\Request;

class voboDocsController extends Controller
{
    public function voboDocument(Request $request){
        try {
            $id_provider = $request->id_provider;
            $id_area = $request->id_area;
            $id_vobo = $request->id_vobo;
            $is_accept = $request->is_accept;
            $is_reject = $request->is_reject;

            $oVoboDoc = VoboDoc::findOrFail($id_vobo);

            \DB::beginTransaction();
            $oVoboDoc->is_accept = $is_accept;
            $oVoboDoc->is_reject = $is_reject;
            $oVoboDoc->update();

            $lDocuments = SProvidersUtils::getDocumentsProvider($id_provider, $id_area);
            foreach ($lDocuments as $doc) {
                $doc->status = $doc->is_accept == true ? 'Aprobado' : ($doc->is_reject == true ? 'Rechazado' : 'Pendiente');
            }
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lDocuments' => $lDocuments]);
    }
}
