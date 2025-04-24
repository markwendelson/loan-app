<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientDetails;
use App\Models\ClientReference;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                dd($data);

                $this->client = $this->createClient($data['client_details']);
                $this->createClientDetails($data['client_details']);

                // 2. Create co-makers and attach them
                $coMakerIds = collect($data['co_makers'] ?? [])
                    ->map(function ($coMakerData) {
                        $coMaker = $this->createCoMaker($coMakerData);
                        return $coMaker?->id;
                    });

                $this->client->coMakers()->syncWithoutDetaching($coMakerIds);


                $this->createClientReferences($data['client_references']);
                $this->dispatchEvent();

                dd($this->client);
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
        $client = Client::create([
            'branch_id' => $data['branch_id'],
            'client_code' => time(),
            'status' => 0,
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
            'present_province_id' => $data['present_province_id'],
            'present_city_id' => $data['present_city_id'],
            'present_zipcode' => $data['present_zipcode'],
            'present_address' => $data['present_address'],
            'permanent_province_id' => $data['permanent_province_id'],
            'permanent_city_id' => $data['permanent_city_id'],
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
            'present_province_id' => $data['present_province_id'],
            'present_city_id' => $data['present_city_id'],
            'present_zipcode' => $data['present_zipcode'],
            'present_address' => $data['present_address'],
            'permanent_province_id' => $data['permanent_province_id'],
            'permanent_city_id' => $data['permanent_city_id'],
            'permanent_zipcode' => $data['permanent_zipcode'],
            'permanent_address' => $data['permanent_address'],
        ]);

    }

    protected function dispatchEvent()
    {
        // Dispatch a registration event, e.g. event(new ClientRegistered($this->client));
    }
}
