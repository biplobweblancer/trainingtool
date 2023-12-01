<?php

namespace App\Http\Controllers\Api\TrainingMonitoring;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingMonitoring\StoreProviderRequest;
use App\Http\Requests\TrainingMonitoring\UpdateProviderRequest;
use App\Models\TrainingMonitoring\Provider;
use App\Repositories\TrainingMonitoring\Interfaces\ProviderRepositoryInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class ProviderController extends Controller
{

    /*
     * Handle Bridge Between Database and Business layer
     */
    private $providerRepository;
    public function __construct(ProviderRepositoryInterface $providerRepository)
    {

        $this->providerRepository = $providerRepository;
    }

    /**
     * Display all providers
     *
     * @return Json Response
     */
    public function index()
    {
        try {
            $providers = $this->providerRepository->all();
            return response()->json([
                'success' => true,
                'data' => $providers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

    }

    /**
     * Handle Course Provider details
     * 
     * @param Provider $provider
     * 
     * @return Json Response
     */
    public function show(Provider $tmsProvider)
    {
        try {
            $provider = $this->providerRepository->details($tmsProvider->id);
            return response()->json([
                'success' => true,
                'data' => $provider,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    /**
     * Handle Course Provider Edit request
     *
     * @param Provider $provider
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Provider $tmsProvider)
    {
        try {
            $provider = $this->providerRepository->find($tmsProvider->id);
            return response()->json([
                'success' => true,
                'data' => $provider,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle Course Provider request
     *
     * @param StoreProviderRequest $request
     *
     * @return Json Response
     */
    public function store(StoreProviderRequest $storeRequest)
    {
        try {
            $data = $storeRequest->all();
            $providers = $this->providerRepository->store($data);
            return response()->json([
                'success' => true,
                'message' => __('provider-list.provider_created'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update Provider datatmsProvider
     *
     * @param Provider $provider
     * @param UpdateProviderRequest $request
     *
     * @return json Response
     */
    public function update(Provider $tmsProvider, UpdateProviderRequest $request)
    {
        try {
            $data = $request->all();
            $this->providerRepository->update($tmsProvider, $data);
            return response()->json([
                'success' => true,
                'data' => $tmsProvider->name,
                'message' => __('provider-list.provider_updated'),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete Provider data
     *
     * @param Provider $provider
     *
     * @return json Response
     */
    public function destroy(Provider $provider)
    {
        try {
            $provider->delete();
            return response()->json([
                'success' => true,
                'message' => __('provider-list.provider_deleted'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function providerBatches()
    {

        try {
            $providers = $this->providerRepository->info();
            return response()->json([
                'success' => true,
                'data' => $providers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
