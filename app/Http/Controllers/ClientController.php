<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

//model declerations
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Client;
use App\Models\ClientDetails;
use App\Models\ClientGuarantor;
use App\Models\ClientPayrollAccount;
use App\Models\ClientJobTitle;
use App\Models\ClientReference;
use App\Models\CountryCode;
use App\Models\Guarantor;
use App\Models\GuarantorJobTitle;
use App\Models\GuarantorPayrollAccount;
use App\Models\JobTitle;
use App\Models\Province;

class ClientController extends Controller {

    public function __construct(){

        //public branch list
        $this->branch = Branch::getBranch([
            'status' => 1,
            'is_multiple' => 1
        ]);

        //public job titles list
        $this->jobTitles = JobTitle::getJobTitles([
            'status' => 1,
            'is_multiple' => 1,
            'order' => [
                'field' => 'title',
                'direction' => 'ASC'
            ],
        ]);

        //public banks list
        $this->banks =  Bank::getBanks([
            'status' => 1,
            'order' => [
                'field' => 'name',
                'direction' => 'ASC'
            ],
            'is_multiple' => 1
        ]);

        //public country codes list
        $this->countryCodes = CountryCode::getCountryCodes([
            'status' => 1,
            'order' => [
                'field' => 'nationality',
                'direction' => 'ASC'
            ],
            'is_multiple' => 1
        ]);

        $this->provinceData = Province::getProvinces([
            'status' => 1,
            'is_multiple' => 1
        ]);
    }

    public function index(Request $request){
        $data = [];

        //compile data
        $data = [
            'title' => 'Client Registration',
            'banks_data' => $this->banks,
            'jobs_data' => $this->jobTitles,
            'countryCodes' => $this->countryCodes,
            'branch_data' => $this->branch,
            'province_data' => $this->provinceData
        ];

        if($request->isMethod('post')){
            $inputData = $request->post();

            $clientDetails = isset($inputData['client_details']) && $inputData['client_details'] ? $inputData['client_details'] : [];
            $clientPayrollAccount = isset($inputData['client_payroll_account']) && $inputData['client_payroll_account'] ? $inputData['client_payroll_account'] : [];
            $clientJobInformation = isset($inputData['client_job_information']) && $inputData['client_job_information'] ? $inputData['client_job_information'] : [];
            $clientReferences = isset($inputData['client_references']) && $inputData['client_references'] ? $inputData['client_references'] : [];

            $clientGuarantor = isset($inputData['guarantor']) && $inputData['guarantor'] ? $inputData['guarantor'] : [];
            $clientGuarantorJobTitle = isset($inputData['guarantor_job_title']) && $inputData['guarantor_job_title'] ? $inputData['guarantor_job_title'] : [];
            $clientGuarantorPayrollAccount = isset($inputData['guarantor_payroll_account']) && $inputData['guarantor_payroll_account'] ? $inputData['guarantor_payroll_account'] : [];

            //generate client file
            $client = [
                'branch_id' => $clientDetails['branch_id'],
                'client_code' => 'CL-' . getNowTimestamp(),
                'username' => slug(isset($clientDetails['fname']) && $clientDetails['fname'] ? $clientDetails['fname'] : '') .  getNowTimestamp(),
                'password' => Hash::make('12345'), //default password
                'api_key' => generateRandomChar(35),
                'email' => isset($clientDetails['email']) && $clientDetails['email'] ? $clientDetails['email'] : ''
            ];

            //insert client table
            Client::create($client);

            $latestClient = Client::latest()->first();
            $lastId = $latestClient->id;

            //add client id to client details
            $clientDetails['client_id'] = $lastId;

            //insert client details
            ClientDetails::create($clientDetails);

            $clientPayrollAccountData = [];
            if(isset($clientPayrollAccount) && $clientPayrollAccount) {
                foreach($clientPayrollAccount as $key => $val){
                    $clientPayrollAccountData[] = [
                        'bank_id' => $val['bank_id'],
                        'client_id' => $lastId,
                        'account_no' => $val['account_no'],
                        'account_name' => $val['account_name'],
                        'is_active' => $val['is_active']
                    ];
                }

                //bulk insert client payroll account
                ClientPayrollAccount::insert($clientPayrollAccountData);
            }

            $clientJobInformationData = [];
            if(isset($clientJobInformation) && $clientJobInformation){
                foreach($clientJobInformation as $key => $val){
                    $clientJobInformationData[] = [
                        'client_id' => $lastId,
                        'job_id' => $val['job_id'],
                        'office_name' => $val['office_name'],
                        'salary' => $val['salary'],
                        'status' => $val['status'],
                    ];
                }

                //insert to client job information table
                ClientJobTitle::insert($clientJobInformationData);
            }

            //client guarantor
            if(isset($clientGuarantor) && $clientGuarantor) {
                Guarantor::create($clientGuarantor);

                $latestGuarantor = Guarantor::latest()->first();
                $lastGuarantorId = $latestGuarantor->id;

                //generate data for client_guarantor
                $clientGuarantor = [
                    'client_id' => $lastId,
                    'guarantor_id' => $lastGuarantorId
                ];

                ClientGuarantor::create($clientGuarantor);
            }

            if($lastGuarantorId){
                //client guarantor job title
                if(isset($clientGuarantorJobTitle) && $clientGuarantorJobTitle){
                    $guarantorJobTitle = [];
                    if(is_array($clientGuarantorJobTitle)){
                        foreach($clientGuarantorJobTitle  as $key => $val){
                            $guarantorJobTitle[] = [
                                'guarantor_id' => $lastGuarantorId,
                                'job_id' => $val['job_id'],
                                'office_name' => $val['office_name'],
                                'salary' => $val['salary'],
                                'status' => $val['status']
                            ];
                        }
                        GuarantorJobTitle::insert($guarantorJobTitle);
                    }
                }

                //client guarantor payroll account
                if(isset($clientGuarantorPayrollAccount) && $clientGuarantorPayrollAccount){
                    $guarantorPayrollAccount = [];

                    if(is_array($clientGuarantorPayrollAccount)){
                        foreach($clientGuarantorPayrollAccount as $key => $val){
                            $guarantorPayrollAccount[]  = [
                                'bank_id' => $val['bank_id'],
                                'guarantor_id' => $lastGuarantorId,
                                'account_no' => $val['account_no'],
                                'account_name' => $val['account_name'],
                                'is_active' => $val['is_active']
                            ];
                        }
                        GuarantorPayrollAccount::insert($guarantorPayrollAccount);
                    }
                }
            }

            //client references
            if(isset($clientReferences) && $clientReferences){
                if(is_array($clientReferences)){
                    $referenceData = [];
                    foreach($clientReferences as $key => $val){
                        $referenceData[] = [
                            'client_id' => $lastId,
                            'fullname' => $val['fullname'],
                            'contact_info' => $val['contact_info'],
                            'occupation' => $val['occupation']
                        ];
                    }
                    ClientReference::insert($referenceData);
                }
            }

            session()->flash('client_notification',
                $this->generateNotification(1)
            );

            return redirect()->to(url()->current());
        }

        return view('client.register.index', $data);
    }

    public function clientList(Request $request){
        $data = [];

        //default fields
        $fields = [
            'id',
            'branch_id',
            'client_code',
            'username',
            'api_key',
            'email',
            'img',
        ];

        $query = Client::select($fields)->with(['clientDetails']);
        $query = Client::getConditions($query, [
            'status' => 1
        ]);

        $clients = $query->get()->toArray();

        //compile data
        $data = [
            'title' => 'Client List',
            'client_data' => $clients
        ];

        return view('client.list.index', $data);
    }

    public function clientEdit(){
        $data = [];

        //compile data
        $data = [
            'title' => 'Client Update',
            'banks_data' => $this->banks,
            'jobs_data' => $this->jobTitles,
            'countryCodes' => $this->countryCodes,
            'branch_data' => $this->branch,
            'province_data' => $this->provinceData
        ];

        return view('client.register.edit', $data);
    }

    public function generateNotification($type) {
        $data = [];

        //client success creation
        if($type == 1){
            $data = [
                'has_error' => 0,
                'title' => 'Success!',
                'message' => 'You have successfully registered a new client.',
                'type' => 'alert-success',
            ];
        }

        return $data;
    }


}