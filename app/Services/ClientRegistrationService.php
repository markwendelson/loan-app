<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Client;
use App\Models\ClientDetails;
use App\Models\ClientReference;
use App\Models\ClientJobTitle;
use App\Models\ClientPayrollAccount;
use App\Models\ClientDocument;
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
                $client_details = $data['client_details'];
                if($client_details['same_as_present_address']) {
                    $client_details['permanent_province'] = $client_details['present_province'];
                    $client_details['permanent_city'] = $client_details['present_city'];
                    $client_details['permanent_zipcode'] = $client_details['present_zipcode'];
                    $client_details['permanent_address'] = $client_details['present_address'];
                }

                $this->client = $this->createClient($client_details);
                $this->createClientDetails($client_details);

                $this->client->coMakers()->syncWithoutDetaching($data['client_comaker']);

                $this->createClientReferences($data['client_references'] ?? []);
                $this->saveJobTitle($data['client_job_information'] ?? []);
                $this->savePayrollAccount($data['client_payroll_account'] ?? []);
                $this->saveDocuments($data['client_documents']);
                $this->dispatchEvent();

                return [
                    'status' => 'success',
                    'message' => 'Client created successfully.',
                ];
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

        if (isset($data['photo'])) {
            $path = $data['photo']->store('client_uploads', 'public');
        }
       
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
            'photo' => $path ?? null
        ]);
    }

    protected function createClientReferences(array $data)
    {
        if(empty($data)) {
            return;
        }

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

    protected function saveJobTitle(array $jobs)
    {
        if(empty($jobs)) {
            return;
        }

        $jobData = [];

        foreach($jobs['job_id'] as $key => $val){
            $jobData[] = [
                'client_id' => $this->client->id,
                'job_id' => $jobs['job_id'][$key],
                'office_name' => $jobs['office_name'][$key],
                'salary' => $jobs['salary'][$key],
                'status' => $jobs['status'][$key]
            ];
        }

        if($jobData)
            ClientJobTitle::insert($jobData);
    }

    protected function savePayrollAccount(array $payrollAccounts)
    {
        if(empty($payrollAccounts)) {
            return;
        }

        $payrollAccountData = [];

        foreach($payrollAccounts['bank_id'] as $key => $val){
            $payrollAccountData[] = [
                'client_id' => $this->client->id,
                'bank_id' => $payrollAccounts['bank_id'][$key],
                'account_no' => $payrollAccounts['account_no'][$key],
                'account_name' => $payrollAccounts['account_name'][$key],
                'is_active' => $payrollAccounts['is_active'][$key]
            ];
        }

        if($payrollAccountData)
            ClientPayrollAccount::insert($payrollAccountData);
    }

    protected function saveDocuments(array $documents)
    {
        if(empty($documents)) {
            return;
        }

        $docData = [];
        foreach($documents['doc_type'] as $key => $val){
            
            if (isset($documents['file_upload'][$key])) {
                $path = $documents['file_upload'][$key]->store('client_uploads', 'public');
                $docData[] = [
                    'client_id' => $this->client->id,
                    'document_type' => $documents['doc_type'][$key],
                    'file_path' => $path ?? null,
                    'remarks' => $documents['file_upload_remarks'][$key] ?? '',
                ];
            } 
        }

        if($docData)
            ClientDocument::insert($docData);
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
            'notes' => $data['notes'],
            'recommendations' => $data['recommendations'],
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
