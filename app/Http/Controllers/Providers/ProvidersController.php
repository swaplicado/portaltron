<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Providers\Provider;

class ProvidersController extends Controller
{
    public function index(){
        $lProviders = Provider::where('providers.is_active', 1)
                            ->where('providers.is_deleted', 0)
                            ->leftJoin('users as uCreated', 'uCreated.id', '=', 'providers.created_by')
                            ->leftJoin('users as uUpdated', 'uUpdated.id', '=', 'providers.updated_by')
                            ->select(
                                'providers.id_provider',
                                'providers.provider_short_name',
                                'providers.provider_name',
                                'providers.provider_rfc',
                                'providers.provider_email',
                                'uCreated.full_name as created_by',
                                'uUpdated.full_name as updated_by',
                                \DB::raw('DATE_FORMAT(providers.created_at, "%Y-%m-%d") as created'),
                                \DB::raw('DATE_FORMAT(providers.updated_at, "%Y-%m-%d") as updated'),
                            )
                            ->get()
                            ->toArray();

        foreach ($lProviders as &$subArray) { // se pasa por referencia para modificar el array original
            $subArray = array_values($subArray); // se obtiene un array simple con los valores del sub-array
        }
        unset($subArray);

        return view('providers.index')->with('lProviders', $lProviders);
    }

    public function createProvider(Request $request){
        try {
            $provider_name = $request->provider_name;
            $provider_short_name = $request->provider_short_name;
            $provider_rfc = $request->provider_rfc;
            $provider_email = $request->provider_email;

            \DB::beginTransaction();
            $oProvider = new Provider();
            $oProvider->provider_name = $provider_name;
            $oProvider->provider_short_name = $provider_short_name;
            $oProvider->provider_rfc = $provider_rfc;
            $oProvider->provider_email = $provider_email;
            $oProvider->save();

            $lProviders = Provider::where('providers.is_active', 1)
                            ->where('providers.is_deleted', 0)
                            ->leftJoin('users as uCreated', 'uCreated.id', '=', 'providers.created_by')
                            ->leftJoin('users as uUpdated', 'uUpdated.id', '=', 'providers.updated_by')
                            ->select(
                                'providers.id_provider',
                                'providers.provider_short_name',
                                'providers.provider_name',
                                'providers.provider_rfc',
                                'providers.provider_email',
                                'uCreated.full_name as created_by',
                                'uUpdated.full_name as updated_by',
                                \DB::raw('DATE_FORMAT(providers.created_at, "%Y-%m-%d") as created'),
                                \DB::raw('DATE_FORMAT(providers.updated_at, "%Y-%m-%d") as updated'),
                            )
                            ->get()
                            ->toArray();

        foreach ($lProviders as &$subArray) { // se pasa por referencia para modificar el array original
            $subArray = array_values($subArray); // se obtiene un array simple con los valores del sub-array
        }
        unset($subArray);

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    public function updateProvider(Request $request){
        try {
            $id_provider = $request->id_provider;
            $provider_name = $request->provider_name;
            $provider_short_name = $request->provider_short_name;
            $provider_rfc = $request->provider_rfc;
            $provider_email = $request->provider_email;

            \DB::beginTransaction();
            $oProvider = Provider::findOrFail($id_provider);
            $oProvider->provider_name = $provider_name;
            $oProvider->provider_short_name = $provider_short_name;
            $oProvider->provider_rfc = $provider_rfc;
            $oProvider->provider_email = $provider_email;
            $oProvider->update();

            $lProviders = Provider::where('providers.is_active', 1)
                            ->where('providers.is_deleted', 0)
                            ->leftJoin('users as uCreated', 'uCreated.id', '=', 'providers.created_by')
                            ->leftJoin('users as uUpdated', 'uUpdated.id', '=', 'providers.updated_by')
                            ->select(
                                'providers.id_provider',
                                'providers.provider_short_name',
                                'providers.provider_name',
                                'providers.provider_rfc',
                                'providers.provider_email',
                                'uCreated.full_name as created_by',
                                'uUpdated.full_name as updated_by',
                                \DB::raw('DATE_FORMAT(providers.created_at, "%Y-%m-%d") as created'),
                                \DB::raw('DATE_FORMAT(providers.updated_at, "%Y-%m-%d") as updated'),
                            )
                            ->get()
                            ->toArray();

        foreach ($lProviders as &$subArray) { // se pasa por referencia para modificar el array original
            $subArray = array_values($subArray); // se obtiene un array simple con los valores del sub-array
        }
        unset($subArray);

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }

    public function deleteProvider(Request $request){
        try {
            $id_provider = $request->id_provider;

            \DB::beginTransaction();
            $oProvider = Provider::findOrFail($id_provider);
            $oProvider->is_active = 0;
            $oProvider->is_deleted = 1;
            $oProvider->update();

            $lProviders = Provider::where('providers.is_active', 1)
                            ->where('providers.is_deleted', 0)
                            ->leftJoin('users as uCreated', 'uCreated.id', '=', 'providers.created_by')
                            ->leftJoin('users as uUpdated', 'uUpdated.id', '=', 'providers.updated_by')
                            ->select(
                                'providers.id_provider',
                                'providers.provider_short_name',
                                'providers.provider_name',
                                'providers.provider_rfc',
                                'providers.provider_email',
                                'uCreated.full_name as created_by',
                                'uUpdated.full_name as updated_by',
                                \DB::raw('DATE_FORMAT(providers.created_at, "%Y-%m-%d") as created'),
                                \DB::raw('DATE_FORMAT(providers.updated_at, "%Y-%m-%d") as updated'),
                            )
                            ->get()
                            ->toArray();

        foreach ($lProviders as &$subArray) { // se pasa por referencia para modificar el array original
            $subArray = array_values($subArray); // se obtiene un array simple con los valores del sub-array
        }
        unset($subArray);

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lProviders' => $lProviders]);
    }
}
