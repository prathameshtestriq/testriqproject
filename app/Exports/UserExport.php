<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class UserExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $users;

    public function __construct()
    {
       
    }

    public function array(): array
    {
      

        // Collect session data for filtering
        $user_name = Session::has('name') ? Session::get('name') : '';
        $status = Session::has('status') ? Session::get('status') : '';
        $mobile = Session::has('mobile') ? Session::get('mobile') : '';
        $email = Session::has('email') ? Session::get('email') : '';
        $role = Session::has('role') ? Session::get('role') : '';
        $country = Session::has('country') ? Session::get('country') : '';
        $state = Session::has('state') ? Session::get('state') : '';
        $city = Session::has('city') ? Session::get('city') : '';
        $gender = Session::has('gender') ? Session::get('gender') : '';
      
        

       // Build the SQL query with search criteria
    //    $s_sql = 'SELECT u.id,u.profile_completion_percentage, u.firstname,u.lastname,u.is_active,u.email,u.mobile,u.gender,u.dob,u.nationality,u.pincode,u.emergency_contact_person,u.emergency_contact_no1,u.organization,u.designation,u.id_proof_type,u.id_proof_no,u.id_proof_doc_upload,u.blood_group,u.weight,u.height,u.medical_conditions(SELECT name FROM  states s WHERE u.state = s.id) as state_name,(SELECT name FROM  cities s WHERE u.city = s.id) as city_name,(SELECT name FROM  countries cs WHERE u.country = cs.id) as country_name, (SELECT name FROM role_master WHERE id = u.role) as role_name
    //    FROM users u WHERE 1=1';
    $s_sql = 'SELECT * , (SELECT name FROM states s WHERE u.state = s.id) AS state_name, 
            (SELECT name FROM cities c WHERE u.city = c.id) AS city_name, 
            (SELECT name FROM countries cs WHERE u.country = cs.id) AS country_name,
            (SELECT name FROM states s WHERE u.ca_state = s.id) AS ca_state_name, 
            (SELECT name FROM cities c WHERE u.ca_city = c.id) AS ca_city_name, 
            (SELECT name FROM countries cs WHERE u.ca_country = cs.id) AS ca_country_name,
            (SELECT name FROM role_master rm WHERE rm.id = u.role) AS role_name
        FROM users u LEFT JOIN user_details ud ON u.id = ud.user_id WHERE 1=1';
        // Add conditions based on session data
    
        if(!empty( $user_name)){
            $s_sql .= ' AND (LOWER((CONCAT(u.firstname, " ", u.lastname))) LIKE \'%' . strtolower($user_name) . '%\')';
        } 

        if(!empty(  $email)){
            $s_sql .= ' AND (LOWER(u.email) LIKE \'%' . strtolower( $email) . '%\')';
        } 

        if(!empty(  $mobile)){
            $s_sql .= ' AND (LOWER(u.mobile) LIKE \'%' . strtolower( $mobile) . '%\')';
        } 

        if(isset( $status)){
            $s_sql .= ' AND (LOWER(u.is_active) LIKE \'%' . strtolower($status) . '%\')';
        } 

        if(!empty(  $country)){
            $s_sql .= ' AND u.country = '. $country. ' ';
        } 

        if(!empty( $state)){
            $s_sql .= ' AND u.state = '. $state. ' ';
        } 

        if(!empty( $city)){
            $s_sql .= ' AND u.city = '. $city. ' ';
        } 

        if(isset( $gender)){
            $s_sql .= ' AND (LOWER(u.gender) LIKE \'%' . strtolower($gender) . '%\')';
        }

        if(!empty( $role)){
            $s_sql .= ' AND u.role = '. $role.' ';
        } 

        $user_details = DB::select($s_sql, array());
        //   dd($user_details[0]);
            $excelData = [];
            foreach ($user_details as $val) {
                if ($val->gender == 1) {
                    $Gender = 'Male';
                } elseif ($val->gender == 2) {
                    $Gender = 'Female';
                } else {
                    $Gender = 'Other';
                }

                if ($val->id_proof_type == 1) {
                    $id_proof_type = "Aadhar Card";
                } elseif ($val->id_proof_type == 2) {
                    $id_proof_type = " PAN Card";
                } elseif ($val->id_proof_type == 3) {
                    $id_proof_type = "Driving Licence Card";
                } else {
                    $id_proof_type = 'Passport Card';
                }

                
                $imagePath = !empty($val->id_proof_doc_upload) ? 'uploads/user_documents/' . $val->id_proof_doc_upload : '';
                $fullUrl = !empty($imagePath) ? url($imagePath) : '';
                $url = "=HYPERLINK(\"$fullUrl\", \"" . $val->id_proof_doc_upload . "\")" ;
            
                $excelData[] = array(
                    'User Name' => $val->firstname . ' ' . $val->lastname,
                    'Email ' => $val->email,
                    'Mobile' => $val->mobile,
                    'Gender' => $Gender,
                    'DOB'    => date('d-m-Y',strtotime($val->dob)),
                    'Country' =>$val->country_name,
                    'State' =>$val->state_name,
                    'City' => $val->city_name,
                    'CA_Country' =>$val->ca_country_name,
                    'CA_State' =>$val->ca_state_name,
                    'CA_City' => $val->ca_city_name,
                    'Role Name' => $val->role_name,
                    'Percentage' => !empty($val->profile_completion_percentage) ? number_format($val->profile_completion_percentage, 2) . '%' : '-',
                    'status' =>   $val->is_active == 1 ? 'Active ' : 'Inactive',
                    'emergency_contact_person' => !empty($val->emergency_contact_person) ? $val->emergency_contact_person : ' ',
                    'emergency_contact_no' => !empty($val->emergency_contact_no1) ? $val->emergency_contact_no1 : '',
                    'organization' => !empty($val->organization)  ? $val->organization : '',
                    'designation' => !empty($val->designation) ? $val->designation : ' ',
                    'id_proof_type' => !empty($id_proof_type ) ? $id_proof_type : '',
                    'id_proof_number' => !empty($val->id_proof_no)  ? $val->id_proof_no : '',
                    'id_proof_doc_upload' => !empty($fullUrl) ?  $url : '',
                    'flat_building_no' =>  !empty($val->address1)  ? $val->address1 : '',
                    'street_area_colony' =>  !empty($val->address2)  ? $val->address2 : '',
                    'CA_Address1' =>  !empty($val->ca_address1)  ? $val->ca_address1 : '',
                    'CA_Address2' =>  !empty($val->ca_address2)  ? $val->ca_address2 : '',
                    'pincode' => !empty($val->pincode)  ? $val->pincode : '',
                    'nationality' => !empty($val->nationality)  ? $val->nationality : '',
                    'blood_group' => !empty($val->blood_group)  ? $val->blood_group : ' ',
                    'height' => !empty($val->height)  ? $val->height : '',
                    'weight' => !empty($val->weight)  ? $val->weight : '',
                    'medical_conditions' => !empty($val->medical_conditions) ? $val->medical_conditions : '',
                    'diabetes' => $val->diabetes == 1 ? 'Yes' : 'No',
                    'chest_pain_last_6_weeks' =>  $val->chestpain == 1 ? 'Yes' : 'No',
                    'diagnosed_angina' =>   $val->angina == 1 ? 'Yes' : 'No',
                    'abnormal_heart_rhythms' =>   $val->abnormalheartrhythm == 1 ? 'Yes' : 'No',
                    'pacemaker' =>   $val->pacemaker == 1 ? 'Yes' : 'No',
                    'severe_dehydration_last_4_weeks' =>   $val->dehydrationseverity == 1 ? 'Yes' : 'No',
                    'severe_muscle_cramps_last_4_weeks' =>   $val->musclecramps == 1 ? 'Yes' : 'No',
                    'high_blood_pressure' =>   $val->highbloodpressure == 1 ? 'Yes' : 'No',
                    'low_blood_sugar_last_4_weeks' =>   $val->lowbloodsugar == 1 ? 'Yes' : 'No',
                    'epilepsy' =>   $val->epilepsy == 1 ? 'Yes' : 'No',
                    'bleeding_disorders' =>   $val->bleedingdisorders == 1 ? 'Yes' : 'No',
                    'asthma' =>   $val->asthma == 1 ? 'Yes' : 'No',
                    'anemia' =>   $val->anemia == 1 ? 'Yes' : 'No',
                    'hospitalized_recently' =>   $val->hospitalized == 1 ? 'Yes' : 'No',
                    'hospitalization_details' =>   $val->hospitalization_details == 1 ? 'Yes' : 'No',
                    'current_infections' =>   $val->infections == 1 ? 'Yes' : 'No',
                    'is_pregnant' =>   $val->pregnant == 1 ? 'Yes' : 'No',
                    'stage_of_pregnancy' =>   !empty($val->stage_pregnancy) ? $val->stage_pregnancy : '',
                    'had_covid_19' =>   $val->covidstatus == 1 ? 'Yes' : 'No',
                    'is_under_medication' =>   $val->undermedication == 1 ? 'Yes' : 'No',
                    'medication_details' =>   !empty($val->meditaion_details) ? $val->meditaion_details : ' ',
                    'current_medications' =>   $val->currentmedications == 1 ? 'Yes' : 'No',
                    'current_medication_names' =>   !empty($val->current_medication_names)  ? $val->current_medication_names : '',
                    'allergies' =>  !empty( $val->allergies)  ? $val->allergies : '',
                    'drug_allergies' =>   $val->drugallergy == 1 ? 'Yes' : 'No',
                    'drug_allergy_details' =>   $val->chestpain == 1 ? 'Yes' : 'No',
                    'familydoctorname' =>  !empty($val->familydoctorname)  ? $val->familydoctorname : ' ',
                    'familydoctorcontactno' =>  !empty( $val->familydoctorcontactno)  ? $val->familydoctorcontactno : ' ',
                );
                
            }
            // dd( $excelData);   
        return $excelData;
    }

    public function headings(): array
    {      
        return [
            [
                'User Name',
                'Email',
                'Mobile',
                'Gender',
                'DOB',
                'Country',
                'State',
                'City',
                'CACountry',
                'CAState',
                'CACity',
                'Role Name',
                'Percentage',
                'Status',
                'Emergency Contact Name',
                'Emergency Contact Number',
                'Organisation',
                'Designation',
                'Id Proof Type',
                'Id Proof Number',
                'Id Proof Document',
                'Flat No./Building No.',   
                'Street Name, Area Name/Colony',
                'CA_Address1',   
                'CA_Address2',
                'Pincode',
                'Nationality',
                'What is your blood group?',
                'What is your height',
                'What is your current weight',
                'Medical Conditions',
                'Do you have diabetes?',
                'Have you experienced chest pain in the last 6 weeks?',
                'Have you ever been diagnosed with angina?',
                'Have you experienced abnormal heart rhythms?',
                'Do you have a pacemaker?',
                'Have you suffered from severe dehydration in the last 4 weeks?',
                'Have you experienced severe muscle cramps in the last 4 weeks?',
                'Have you been diagnosed with high blood pressure?',
                'Have you had episodes of low blood sugar in the last 4 weeks?',
                'Have you been diagnosed with epilepsy?',
                'Do you have any bleeding disorders?',
                'Do you suffer from asthma?',
                'Have you been diagnosed with anemia?',
                'Have you been hospitalized recently?',
                'Hospitalization Details',
                'Are you currently experiencing any infections?',
                'Are you pregnant?',
                'Stage Pregnancy',
                'Have you suffered from Covid-19?',
                'Are you currently under any medication?',
                'Medication Details',
                'Current Medications',
                'Medications Name',
                'Do you have any known allergies?',
                'Do you have any known drug allergies?',
                'Drug Allergy Details',
                'Family Doctor Name',
                'Family Doctor Contact Number',
            
            ]
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set horizontal alignment for all cells
                $sheet->getStyle('A1:BH1')->getAlignment()->setHorizontal('left');

                // Apply font styling to header
                $sheet->getStyle('A1:BH1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ]
                ]);
            },
        ];
    }
}
