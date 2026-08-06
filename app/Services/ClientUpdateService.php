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

class ClientUpdateService
{
    protected Client $client;

    public static function update(Client $client, array $data)
    {
        $service = new self();
        $service->client = $client;
        return $service->handleUpdate($data);
    }

    protected function handleUpdate(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $client_details = $data['client_details'];
                if (isset($client_details['same_as_present_address']) && $client_details['same_as_present_address']) {
                    $client_details['permanent_province'] = $client_details['present_province'];
                    $client_details['permanent_city'] = $client_details['present_city'];
                    $client_details['permanent_zipcode'] = $client_details['present_zipcode'];
                    $client_details['permanent_address'] = $client_details['present_address'];
                }

                $this->updateClient($client_details);
                $this->updateClientDetails($client_details);

                $this->client->coMakers()->sync($data['client_comaker'] ?? []);

                $this->updateClientReferences($data['client_references'] ?? []);
                $this->updateJobTitle($data['client_job_information'] ?? []);
                $this->updatePayrollAccount($data['client_payroll_account'] ?? []);
                $this->updateDocuments($data['client_documents'] ?? []);

                return [
                    'status' => 'success',
                    'message' => 'Client updated successfully.',
                ];
            });
        } catch (\Throwable $th) {
            return [
                'status' => 'failed',
                'message' => 'Client update failed: ' . $th->getMessage(),
            ];
        }
    }

    protected function updateClient(array $data)
    {
        $this->client->update([
            'branch_id' => $data['branch_id'],
        ]);
    }

    protected function updateClientDetails(array $data)
    {
        $clientDetails = $this->client->details;
        if (!$clientDetails) {
            throw new \Exception('Client details not found.');
        }

        $path = null;
        if (isset($data['photo'])) {
            $path = $data['photo']->store('client_uploads', 'public');
        }

        $updateData = [
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
        ];

        if ($path) {
            $updateData['photo'] = $path;
        }

        $clientDetails->update($updateData);
        Log::info($updateData);
    }

    protected function updateClientReferences(array $data)
    {
        if(empty($data)) {
            return;
        }

        ClientReference::where('client_id', $this->client->id)->delete();

        $referenceData = [];
        foreach ($data as $val) {
            $referenceData[] = [
                'client_id' => $this->client->id,
                'fullname' => $val['fullname'],
                'contact_info' => $val['contact_info'],
                'occupation' => $val['occupation'],
            ];
        }

        if ($referenceData) {
            ClientReference::insert($referenceData);
        }
    }

    protected function updateJobTitle(array $jobs)
    {
        if(empty($jobs)) {
            return;
        }

        ClientJobTitle::where('client_id', $this->client->id)->delete();

        $jobData = [];
        foreach ($jobs['job_id'] as $key => $val) {
            $jobData[] = [
                'client_id' => $this->client->id,
                'job_id' => $jobs['job_id'][$key],
                'office_name' => $jobs['office_name'][$key],
                'salary' => $jobs['salary'][$key],
                'status' => $jobs['status'][$key],
            ];
        }

        if ($jobData) {
            ClientJobTitle::insert($jobData);
        }
    }

    protected function updatePayrollAccount(array $payrollAccounts)
    {
        if(empty($payrollAccounts)) {
            return;
        }

        if(isset($payrollAccounts['deleted_id']) && $payrollAccounts['deleted_id']){
            $ids = explode(',', $payrollAccounts['deleted_id']);
            ClientPayrollAccount::where('client_id', $this->client->id)
                ->whereIn('id', $ids)
                ->delete();
        }

        if(isset($payrollAccounts['bank_id']) && $payrollAccounts['bank_id']){
            $payrollAccountData = [];
            foreach ($payrollAccounts['bank_id'] as $key => $val) {
                $payrollAccountData[] = [
                    'client_id' => $this->client->id,
                    'bank_id' => $payrollAccounts['bank_id'][$key],
                    'account_no' => $payrollAccounts['account_no'][$key],
                    'account_name' => $payrollAccounts['account_name'][$key],
                    'account_expiry' => $payrollAccounts['account_expiry'][$key],
                    'is_active' => $payrollAccounts['is_active'][$key],
                    'is_primary' => $payrollAccounts['is_primary'][$key],
                ];
            }

            if ($payrollAccountData) {
                ClientPayrollAccount::insert($payrollAccountData);
            }
        }
    }

    protected function updateDocuments(array $documents)
    {
        if(empty($documents)) {
            return;
        }

        ClientDocument::where('client_id', $this->client->id)->delete();

        $docData = [];
        foreach ($documents['doc_type'] as $key => $val) {
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

        if ($docData) {
            ClientDocument::insert($docData);
        }
    }
}
