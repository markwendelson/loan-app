<?php

namespace App\Http\Controllers;

use App\Events\NewEmployee_Notification;  // Make sure to import the ev
use Illuminate\Support\Facades\Log; // Add this at the top
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Employee; 
use App\Models\EmployeeDetail;
use App\Models\EmployeeAddress;
use App\Models\Branch;
use App\Models\JobTitle; 
use App\Models\Province; 
use App\Models\City;
use App\Models\Department;
use App\Models\IdentificationType;
use App\Models\EmployeeEducation;
use App\Models\EmployeeFamily;
use App\Models\EmployeeIdentification;
use App\Models\EmployeeJobTitle;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Notifications\NewEmployeeNotification;
use Illuminate\Support\Facades\Notification;



class EmployeeController extends Controller {

    public function __construct(){
        // parent::__construct();
    
        $this->branch = Branch::getBranch([
            'status' => 1,
            'is_multiple' => 1
        ]);

        $this->provinceData = Province::getProvinces([
            'status' => 1,
            'is_multiple' => 1
        ]);
     

        $this->cityData = City::getCities([
            'status' => 1,
            'is_multiple' => 1
        ]);

        $this->identification_Data = IdentificationType::getIdentifications([
            'status' => 1,
            'is_multiple' => 1
        ]);

        $this->departmentData = Department::getDepartments([
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
    }

    public function update(Request $request, $id) {

        // Fetch employee information if the request is GET
        if ($request->isMethod('get')) {
            $empStatusDefault =0;
            // Get the employee ID from the request
            $employeeId = $id ?? $request->query('id');
                              
            // Check if employee ID is provided
            if (!$employeeId) {
                return response()->json(['error' => 'Employee ID is required'], 400);
            }
                    
            // Retrieve the employee 
            $employee = Employee::getEmployee([
                'id' => $employeeId,
                'all_fields' => 1 // Retrieve all fields
            ]);;
           
            //Get Emp. Details            
            $empDetails = EmployeeDetail::getEmp_Details([
                'employee_id' => $employeeId,
                'all_fields' => 1 // Retrieve all fields
            ]);

            if($empDetails){
                $empPresentCity = City::getCities([
                    'province_id' => $empDetails['present_province_id'],
                    'is_multiple' => 1 // Retrieve all fields
                ]);
                
                $empPermanentCity = City::getCities([
                    'province_id' =>$empDetails['permanent_province_id'],
                    'is_multiple' => 1 // Retrieve all fields
                ]);
            }
                                          
            //Get Emp. Educ.
            $empEducation = EmployeeEducation::getEducation([
                'employe_id' => $employeeId,
                'all_fields' => 1 // Retrieve all fields
            ]);;
            
            //Get Emp. Family.
            $family_data = EmployeeFamily::getEmpFamily([
                'employee_id' => $employeeId,
                'is_multiple' => 1,
                'all_fields' => 1 // Retrieve all fields
            ]);

            // dd($family_data, $employeeId);
                    
             //Get Emp. IDs
             $empJobs = EmployeeJobTitle::getEmp_Job([
                'employee_id' => $employeeId,
                'is_multiple' => 1// Retrieve all fields
                // 'all_fields' => 1
            ]);;

            // dd($empJobs);                     
            //Get Emp. IDs
            $empIDs = EmployeeIdentification::getEmp_Identifications([
                'employee_id' => $employeeId,
                'is_multiple' => 1,
                 'all_fields' => 1 // Retrieve all fields
            ]);;

            if (!$employeeId) {
                return response()->json(['error' => 'Employee ID is required'], 400);
            }
                    
            $mergedDataFlat = array_merge(
                 $employee,
                $empDetails,
                $family_data,
                $empEducation,                        
                $empIDs                            
            );     
            
            // Define default values
            $presentCityId = null;
            $permanentCityId = null;
            
            if (!empty($empPresentCity) && !empty($empPermanentCity)) {
                $mergedDataFlat['emp_present_city'] = $empPresentCity;
                $mergedDataFlat['emp_permanent_city'] = $empPermanentCity;
            
                $presentCityId = $empDetails['present_city_id'] ?? null; 
                $permanentCityId = $empDetails['permanent_city_id'] ?? null;
            }
           
            // if ($empPresentCity && $empPermanentCity) {
            //     $mergedDataFlat['emp_present_city'] = $empPresentCity;
            //     $mergedDataFlat['emp_permanent_city'] = $empPermanentCity;
              
            //     $presentCityId = $empDetails['present_city_id']; 
            //     $permanentCityId = $empDetails['permanent_city_id']; 
            // }
           

            $employeeRelations = Config::get('const.employee_relation');

            // Check if the employee exists
            if (!$mergedDataFlat) {
                return response()->json(['error' => 'Employee not found'], 404);
            }
            
            //List of Identifications
            $empIDs_data = array_column( $this->identification_Data, 'name', 'id');

            // $branch_data = array_column( $this->branch, 'name', 'id');
            // dd($empIDs);

            $newJobs_data = array_column($this->jobTitles, 'title', 'id');
            //  dd( $newArray);

            //dd($this->branch);
   
            // dd($empJobs);

            // Check if employee data is retrieved
            if ($mergedDataFlat) {                        
                // Return the data to the view or as JSON
                return view('employee.register.index', [
                    'header_title' => 'Update Employee',
                    'jobs_data' => $this->jobTitles,
                    'branch_data' => $this->branch,
                    'province_data' => $this->provinceData,
                    'cityData' => $this->cityData,
                    'department_data' => $this->departmentData,                    
                    'identification_Data' => $this->identification_Data,                     
                    'empStus' => $empStatusDefault,
                    'presentCityId' => $presentCityId,  
                    'permanentCityId' => $permanentCityId,      
                    'employeeRelations' => $employeeRelations,
                    'family_data' => $family_data,
                    'empJobs' => $empJobs,
                    'empIDs' => $empIDs,
                    'empIDs_data' => $empIDs_data,
                    'newJobs_data' => $newJobs_data,
                    'employee' => $mergedDataFlat // Convert to object if it's an array
                ]);
            } else {
                // Handle the case if employee not found
                return redirect()->back()->with('error', 'Employee not found');
            }

        }

        //Update Mode --------------------------------------------------
        if ($request->isMethod('post')) {
            $inputData = $request->all();
            
            $request->validate([
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Customize rules as needed
            ]);

            $EmpTab = new Employee();
            // $EmpTabDetails = new EmployeeDetail();

            $employee = isset($inputData['employee_userinfo']) && $inputData['employee_userinfo']  ? $inputData['employee_userinfo'] : [];
            $employeeJob = isset($inputData['emp_job']) && $inputData['emp_job']  ? $inputData['emp_job'] : [];
            // $employee['password'] = isset($employee['password']) && $employee['password'] ? Hash::make($employee['password']) : '';
                       
           
            // Handle password only if it's not empty
            if (!empty($employee['password'])) {
                $employee['password'] = Hash::make($employee['password']);
            } else {
                unset($employee['password']); // Remove password key if empty
            }
       
            $employeeDetails = isset($inputData['employee_details']) && $inputData['employee_details']  ? $inputData['employee_details'] : [];                     
            $employeeEduc = isset($inputData['employee_educ']) && $inputData['employee_educ']  ? $inputData['employee_educ'] : [];
            $employeeFam = isset($inputData['emp_fam']) && $inputData['emp_fam']  ? $inputData['emp_fam'] : [];
            $employeeIDs= isset($inputData['emp_ids']) && $inputData['emp_ids']  ? $inputData['emp_ids'] : [];
                       

            // dd($employee['password'],$employeeDetails);
                              
            // If an ID is provided, attempt to update the existing record
            $EmpTab = null;
            if (isset($employee['id']) && $employee['id']) {
                $EmpTab = Employee::find($id);
            }
                               
            if ($id) {
                // Update the employee record 
                $updatedEmployee = Employee::updateRecord($id, $employee);
          
                
                // Update or create employee details                             
                if ($updatedEmployee) {
                    // Update employee details 
                    $employeeDetails['email'] = $updatedEmployee['username'];

                    $updatedDetails = EmployeeDetail::updateDetails(
                        ['employee_id' => $id], // Criteria to find the record
                        $employeeDetails                    // Data to update
                    );
                    $result  = $updatedDetails;
                                  
                    if (!$updatedDetails) {
                        // If no record found, create a new one
                        $result =  EmployeeDetail::create(array_merge(['employee_id' => $employee['id']], $employeeDetails));
                    }

                    //Update Employee Educ.
                    $updateEduc = EmployeeEducation::updateEducation(
                        ['employee_id' => $id], // Criteria to find the record
                        $employeeEduc                    // Data to update
                    );   
                    $result = $updateEduc;
                    

                    if (!$updateEduc) {
                        // If no record found, create a new one
                        $result =  EmployeeEducation::create(array_merge(['employee_id' => $employee['id']], $employeeEduc));
                    }
                }
                                                                               
                    //Update and Create Employee jobs 
                    $jobData = [];
                    if (!empty($employeeJob) && is_array($employeeJob)) {
                        foreach ($employeeJob as $val) {
                            if (isset($val['is_old']) && $val['is_old']) {
                                // Update old job record
                            $result = EmployeeJobTitle::where('id', $val['id'])->update([
                                    'job_id' => $val['job_id'] ?? null,
                                    'effective_date' => $val['effective_date'] ?? null,
                                    'salary' => $val['salary'] ?? null,
                                    'updated_at' => now(),
                                ]);
                            } else {
                                // Prepare new job data for insertion
                                $jobData[] = [
                                    'employee_id' => $id,
                                    'job_id' => $val['job_id'] ?? null,
                                    'effective_date' => $val['effective_date'] ?? null,
                                    'salary' => $val['salary'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                    }                    
                    // Insert new job data
                    if (!empty($jobData)) {
                        try {
                            EmployeeJobTitle::insert($jobData);
                        } catch (\Exception $e) {
                            Log::error('Error inserting job data: ' . $e->getMessage());
                        }
                    }
                                                
                    //Update and Create Employee Family Info. ---------------------------------------
                    $employee_Fam = [];
                    if (isset($employeeFam) && !empty($employeeFam)) {
                        foreach ($employeeFam as $val) {
                            if (isset($val['is_old']) && $val['is_old']) {
                                // Update existing family information
                                EmployeeFamily::where('id', $val['id'])->update([
                                    'lname' => $val['lname'] ?? null,
                                    'fname' => $val['fname'] ?? null,
                                    'mname' => $val['mname'] ?? null,
                                    'birthdate' => $val['birthdate'] ?? null,
                                    'relation' => $val['relation'] ?? null,
                                    'updated_at' => now(),
                                ]);
                            } else {
                                // Prepare new family information for insertion
                                $employee_Fam[] = [
                                    'employee_id' => $id,
                                    'lname' => $val['lname'] ?? null,
                                    'fname' => $val['fname'] ?? null,
                                    'mname' => $val['mname'] ?? null,
                                    'birthdate' => $val['birthdate'] ?? null,
                                    'relation' => $val['relation'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                    
                        // Insert new family records
                        if (!empty($employee_Fam)) {
                            try {
                               $result = EmployeeFamily::insert($employee_Fam);
                            } catch (\Exception $e) {
                                Log::error('Error inserting employee family data: ' . $e->getMessage());
                            }
                        }
                    }
                
                    //update and create Emp. IDs. info.
                    $employeeID_Data = [];
                    if (isset($employeeIDs) && !empty($employeeIDs)) {
                        foreach ($employeeIDs as $val) {
                            if (isset($val['is_old']) && $val['is_old']) {
                                // Update existing employee ID information
                                EmployeeIdentification::where('id', $val['id'])->update([
                                    'id_type_id' => $val['id_type_id'] ?? null,
                                    'id_no' => $val['id_no'] ?? null,
                                    'updated_at' => now(),
                                ]);
                            } else {
                                // Prepare new employee ID information for insertion
                                $employeeID_Data[] = [
                                    'employee_id' => $id,
                                    'id_type_id' => $val['id_type_id'] ?? null,
                                    'id_no' => $val['id_no'] ?? null,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                    
                        // Insert new employee IDs
                        if (!empty($employeeID_Data)) {
                            try {
                               $result = EmployeeIdentification::insert($employeeID_Data);
                            } catch (\Exception $e) {
                                Log::error('Error inserting employee ID data: ' . $e->getMessage());
                            }
                        }
                    }

            } 
                        
            //Get Emp. code
            $existingEmpcode = $updatedEmployee['emp_code'];
                   
            //Inserting Image to--------------------
             $distPath = 'uploads/employee/' . $existingEmpcode;
             
            if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                
                // Get the existing image file name from the database
                $existingImage = $updatedEmployee['img'];

                // Create the filename using Employee code and datetime
                $fileExtension = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
                $fileName = $existingEmpcode . '_' . date('YmdHis') . '.' . $fileExtension;

                
                // Define the destination path
                $distPath = 'uploads/employee';
                $filePath = $distPath . '/' . $fileName;

                // Check if the new file name matches the existing one
                if ($existingImage !== $fileName) {
                    // Check if the directory exists; if not, create it
                    if (!Storage::disk('public')->exists($distPath)) {
                        Storage::disk('public')->makeDirectory($distPath, 0755, true); // Create directory with permissions
                    }
                
                    // Store the image file in the created directory
                    Storage::disk('public')->put($filePath, file_get_contents($_FILES['img']['tmp_name']));
                
                    // Update the database with the new image file name
                    $updatedEmployee->img = $fileName;
                    $updatedEmployee->save();
                } else {
                    // If the image is the same, no update needed
                    // dd('Image is the same; no update required.');
                }
            } else {
                // Handle the case when 'img' is not present
                // dd('No image file uploaded');
            }

            if($result){
                $data = $this->generateNotifications(2);
            }else{
                $data = $this->generateNotifications("");
            }

            return response()->json($data);
        }

    // Handle unsupported HTTP methods
    return response()->json(['error' => 'Unsupported request method'], 405);

   }


    public function register(Request $request){       
        $newEmpCode = Employee::generateEmpCode();
        $empStatusDefault =1;

        //Insert New Employee --------------------------------------------------
        if ($request->isMethod('post')) {
            // Validate input data         
            $inputData = $request->post();
          
            $EmpTab = new Employee();

            $employee = isset($inputData['employee_userinfo']) && $inputData['employee_userinfo']  ? $inputData['employee_userinfo'] : [];
            $employeeJob = isset($inputData['emp_job']) && $inputData['emp_job']  ? $inputData['emp_job'] : [];
            $employee['password'] = isset($employee['password']) && $employee['password'] ? Hash::make($employee['password']) : '';
       
            $employeeDetails = isset($inputData['employee_details']) && $inputData['employee_details']  ? $inputData['employee_details'] : [];
                     
            $employeeEduc = isset($inputData['employee_educ']) && $inputData['employee_educ']  ? $inputData['employee_educ'] : [];
            $employeeFam = isset($inputData['emp_fam']) && $inputData['emp_fam']  ? $inputData['emp_fam'] : [];
            $employeeIDs= isset($inputData['emp_ids']) && $inputData['emp_ids']  ? $inputData['emp_ids'] : [];
                
            // dd($employeeFam, $employeeJob);

            //check first duplicate
            $checkDuplicate = Employee::getEmployee([
                'username' => $employee['username']
            ]);

            if($checkDuplicate){
                $data = $this->generateNotifications(3);
                return response()->json($data);
            }

            $employee['emp_code'] = $newEmpCode;
            $checkEmpCode = Employee::getEmployee([
                'emp_code' => $employee['emp_code']
            ]);    
            
            //If Empcode Exist generate new -----------
            if($checkEmpCode){
                $newEmpCode = Employee::generateEmpCode();
            }
          
            //insert to employee -------------------------------------------------
            $employeeRecord = Employee::create($employee);
            $lastId = $employeeRecord->id;   
            $empEmail = $employeeRecord->username;

            // Access the display_name field
            //  echo $employeeRecord->display_name;
          

            // // Fetch users with specific user_type
            // $employeeToNotify = Employee::where('user_type', '2')->get();

            // // Notify each user
            // foreach ($employeeToNotify as $empUser) {
            //     $empUser->notify(new NewEmployeeNotification($employeeRecord->display_name, $lastId));
            // }
            //--------------------------------------------------------------

            // Send notification to all users (or specific users)
            // $empUsers = Employee::all(); // all users or youcan specify  //$empUsers = Employee::where('role', 'admin')->get();     
                       
            // foreach ($empUsers as $user) {             
            //     Notification::send($user, new NewEmployeeNotification($employeeRecord->display_name, $employeeRecord->id));
            // }           

            // //  Trigger the event after employee is created
            // event(new NewEmployee_Notification(
            //     displayName: $employeeRecord->display_name, 
            //     employeeId: $employeeRecord->id, 
            //     message: 'A new employee has joined the team!'
            // ));
            
            // Fetch the newly inserted employee record
            $EmpTab = Employee::findOrFail($lastId);
            $latestEmpcode = $employeeRecord->emp_code;
                       
            // Assign the created employee's ID as a foreign key
            $employeeDetails['employee_id'] = $employeeRecord->id;  
            $employeeDetails['email']  = $empEmail;
            $result = EmployeeDetail::create($employeeDetails);
                        
            //dd($employeeDetails);
           
            //Inserting Image to--------------------
             $distPath = 'uploads/employee/' . $latestEmpcode;
             
            if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {

                // Create the filename using Employee code and datetime
                 $fileExtension = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
                 $fileName = $latestEmpcode . '_' . date('YmdHis') . '.' . $fileExtension;
                        
                 // Define the destination path (uploads/employee folder)
                 $distPath = 'uploads/employee';
                 $filePath = $distPath . '/' . $fileName; // Complete file path

                 // Check if the directory exists; if not, create it
                 if (!Storage::disk('public')->exists($distPath)) {
                     Storage::disk('public')->makeDirectory($distPath, 0755, true); // Create directory with permissions
                 }

                 // Store the image file in the created directory
                 Storage::disk('public')->put($filePath, file_get_contents($_FILES['img']['tmp_name']));
                        
                 // Save only the filename in the database (Employee code with datetime)
                 $EmpTab->img = $fileName;
                 $EmpTab->save();
            
            } else {
                   // Handle the case when 'img' is not present
                //    dd('No image file uploaded');
            }
                                           
            //Employee Educ
            $employeeEduc['employee_id'] = $lastId; //$createdEmployee->id; 
            $result = EmployeeEducation::create( $employeeEduc);

            $jobData = [];
            if (isset($employeeJob) && !empty($employeeJob)) {
                foreach ($employeeJob as $val) {
                    $jobData[] = [
                        'employee_id' => $lastId,
                        'job_id' => $val['job_id'],
                        'effective_date' => $val['effective_date'],
                        'salary' => $val['salary'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($jobData)) {
                  $result = EmployeeJobTitle::insert($jobData);
                }
            }
           
            
            //Inserting Fam Information -------------------
            $employee_Fam = [];
            if (isset($employeeFam) && !empty($employeeFam)) {
                foreach ($employeeFam as $val) {
                    $employee_Fam[] = [
                        'employee_id' => $lastId,
                        'lname' => isset($val['lname']) ? $val['lname'] : null,
                        'fname' => isset($val['fname']) ? $val['fname'] : null,
                        'mname' => isset($val['mname']) ? $val['mname'] : null,
                        'birthdate' => isset($val['birthdate']) ? $val['birthdate'] : null,
                        'relation' => isset($val['relation']) ? $val['relation'] : null,                   
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (!empty($employee_Fam)) {
                    $result =  EmployeeFamily::insert($employee_Fam);
                }
            }

            //Inserting Ids Information -------------------
            $employeeID_Data = [];
            if (isset($employeeIDs) && !empty($employeeIDs)) {
                 foreach ($employeeIDs as $val) {
                     $employeeID_Data[] = [
                         'employee_id' => $lastId,
                         'id_type_id' => $val['id_type_id'],
                         'id_no' => $val['id_no'],                        
                         'created_at' => now(),
                         'updated_at' => now(),
                     ];
                 }
                 if (!empty($employeeID_Data)) {
                     $result =  EmployeeIdentification::insert($employeeID_Data);
                 }
            }

            if($result){
                $data = $this->generateNotifications(1);
            }else{
                $data = $this->generateNotifications("");
            }

        return response()->json($data);            
        }
       
        return view('employee.register.index', [
            'header_title' => 'Register Employee',
            'jobs_data' => $this->jobTitles,
            'branch_data' => $this->branch,  // This is passed directly
            'province_data' => $this->provinceData,
            'cityData' => $this->cityData,
            'department_data' => $this->departmentData,
            'newEmpCode' => $newEmpCode,
            'identification_Data'=> $this->identification_Data,
            'empStatus' =>  $empStatusDefault
        ]);

    }

    public function index(){
     
        // Check if user is logged in
    // if ($this->isLogin() == 0) {
    //     return redirect('login');
    // }

    try {
        // Set parameters for fetching employee data
        $params = [
            'is_multiple' => true,  // Set to true to fetch multiple records
            'all_fields' => true,   // Set to true to fetch all fields
            'status' => 1           // Example condition for active status
        ];

        // Fetch all employees based on the provided parameters
        $allEmployees = Employee::paginate(12); // 12 employees per page
        // $allEmployees = Employee::getEmployee($params);

    } catch (\Exception $e) {
        // Handle potential errors (e.g., database errors)
        return redirect()->back()->with('error', 'Error fetching employee data');
    }

    // Pass the employees data to the view    
    return view('employee.list.index', compact('allEmployees'));
      
    }

    public function generateNotifications($type){
        //default error
        $data = [
            'has_error' => 1,
            'title' => 'Error',
            'message' => 'An error occurred while trying to add the new department. Please try again.',
            'type' => 'error'
        ];

        if($type && $type == 1){
            //add notification success
            $data = [
                'has_error' => 0,
                'title' => 'Success',
                'message' => 'The new Employee has been successfully added.',
                'type' => 'success'
            ];
        }

        if($type && $type == 2){
            //update notification success
            $data = [
                'has_error' => 0,
                'title' => 'Update Successful',
                'message' => 'The Employee details have been successfully updated.',
                'type' => 'success'
            ];
        }

        if($type && $type == 3){
            //update notification success
            $data = [
                'has_error' => 1,
                'title' => 'Duplicate Record',
                'message' => 'A Employee with these details already exists.',
                'type' => 'error'
            ];
        }


        return $data;
    }


   

}