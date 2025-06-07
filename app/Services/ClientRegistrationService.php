<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Client;
use App\Models\ClientDetails;
use App\Models\ClientReference;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientRegistrationService
{
    protected $client;

    public static function register(array $data)
    {
        $service = new self();
        return $service->handleRegistration($data);
    }

    protected function handleRegistration(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                if($data['same_as_present_address']) {
                    $data['permanent_province'] = $data['present_province'];
                    $data['permanent_city'] = $data['present_city'];
                    $data['permanent_zipcode'] = $data['present_zipcode'];
                    $data['permanent_address'] = $data['present_address'];
                }
                Log::info($data);

                $this->client = $this->createClient($data['client_details']);
                $this->createClientDetails($data['client_details']);

                // 2. Create co-makers and attach them
                $coMakerIds = collect($data['co_makers'] ?? [])
                    ->map(function ($coMakerData) {
                        $coMaker = $this->createCoMaker($coMakerData);
                        return $coMaker?->id;
                    });

                $this->client->coMakers()->syncWithoutDetaching($coMakerIds);


                $this->createClientReferences($data['client_references'] ?? []);
                $this->dispatchEvent();

                return $this->client;
            });
        } catch (\Throwable $th) {
            return [
                'status' => 'failed',
                'message' => 'Client registration failed: ' . $th->getMessage(),
            ];
        }
    }

    protected function createClient(array $data)
    {
        // Create and return Client model
        $client_code = $this->generateClientCode($data['branch_id']);

        $client = Client::create([
            'branch_id' =>  $data['branch_id'],
            'client_code' => $client_code,
            'status' => 1,
            'created_by' => Auth::id(),
        ]);

        return $client;
    }

    protected function createClientDetails(array $data)
    {
        ClientDetails::create([
            'client_id' => $this->client->id,
            'firstname' => $data['firstname'],
            'middlename' => $data['middlename'],
            'lastname' => $data['lastname'],
            'suffix' => $data['suffix'] ?? null,
            'birthdate' => $data['birthdate'],
            'nationality' => $data['nationality'],
            'gender' => $data['gender'],
            'civil_status' => $data['civil_status'],
            'contact_info' => $data['contact_info'],
            'email' => $data['email'],
            'present_province_id' => $data['present_province'],
            'present_city_id' => $data['present_city'],
            'present_zipcode' => $data['present_zipcode'],
            'present_address' => $data['present_address'],
            'permanent_province_id' => $data['permanent_province'],
            'permanent_city_id' => $data['permanent_city'],
            'permanent_zipcode' => $data['permanent_zipcode'],
            'permanent_address' => $data['permanent_address'],
        ]);
    }

    protected function createClientReferences(array $data)
    {
        $referenceData = [];

        foreach($data as $val){
            $referenceData[] = [
                'client_id' => $this->client->id,
                'fullname' => $val['fullname'],
                'contact_info' => $val['contact_info'],
                'occupation' => $val['occupation']
            ];
        }

        if($referenceData)
            ClientReference::insert($referenceData);
    }

    protected function createCoMaker(array $data)
    {
        $client = Client::create([
            'branch_id' => $data['branch_id'],
            'client_code' => time(),
            'status' => 0,
            'created_by' => Auth::id(),
        ]);

        ClientDetails::create([
            'client_id' => $client->id,
            'firstname' => $data['firstname'],
            'middlename' => $data['middlename'],
            'lastname' => $data['lastname'],
            'suffix' => $data['suffix'] ?? null,
            'birthdate' => $data['birthdate'],
            'nationality' => $data['nationality'],
            'gender' => $data['gender'],
            'civil_status' => $data['civil_status'],
            'contact_info' => $data['contact_info'],
            'email' => $data['email'],
            'present_province_id' => $data['present_province'],
            'present_city_id' => $data['present_city'],
            'present_zipcode' => $data['present_zipcode'],
            'present_address' => $data['present_address'],
            'permanent_province_id' => $data['permanent_province'],
            'permanent_city_id' => $data['permanent_city'],
            'permanent_zipcode' => $data['permanent_zipcode'],
            'permanent_address' => $data['permanent_address'],
        ]);

    }

    protected function dispatchEvent()
    {
        // Dispatch a registration event, e.g. event(new ClientRegistered($this->client));
    }

    protected function generateClientCode($branch)
    {
        $branch_code = Branch::find($branch);
        return strtoupper($branch_code->code).'-'.time();
    }
}
