<?php

namespace App\Http\Controllers;

use App\Libraries\Curlcall;
use App\Libraries\MultiCurlCall;
use App\Models\dashboard;
use App\Models\Master_brand;
use App\Models\Master_program;
use Google\Service\MyBusinessLodging\SustainableSourcing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //

   public function dashboard_details(Request $request, $code='')
   {
      $aReturn = [];//$this-> common_data();

      return view('dashboard.admin_dashboard', $aReturn);
   }

   // public function dashboard_overview_details(Request $request){
   //    $aReturn = $this->common_data();

   //    $aPost = array();
   //    $aPost['season'] = env('CURRENT_SEASON');
   //    $aPost['country'] = env('CURRENT_COUNTRY');
   //    $aPost['state'] = 0;
   //    $aPost['district'] = 0;
   //    $aPost['block'] = 0;
   //    $aPost['village'] = 0;
   //    $aPost['brand'] = 0;
   //    $aPost['program'] = 1;

   //    if($request->input('submit')=='search'){
   //       $aPost = $request->input();
   //    }

   //    $aPost['kpi_id'] = 1;
   //    $aPost['kpi_has_fields'] = 1;
   //    $aReturn['inputCost']['project'] = $this->dashboard_overview_data((object)$aPost, 'project');
   //    $aReturn['inputCost']['control'] = $this->dashboard_overview_data((object)$aPost, 'control');
   //    $aPost['kpi_has_fields'] = 0;
   //    // Profit
   //    $aPost['kpi_id'] = 8;
   //    $aReturn['profit']['project'] = $this->dashboard_overview_data((object)$aPost, 'project');
   //    $aReturn['profit']['control'] = $this->dashboard_overview_data((object)$aPost, 'control');

   //    //Yeild
   //    $aPost['kpi_id'] = 21;
   //    $aReturn['yeild']['project'] = $this->dashboard_overview_data((object)$aPost, 'project');
   //    $aReturn['yeild']['control'] = $this->dashboard_overview_data((object)$aPost, 'control');

   //    // Chemical pesticide
   //    $aPost['kpi_id'] = 3;
   //    $aReturn['chemicalPesticide']['project'] = $this->dashboard_overview_data((object)$aPost, 'project');
   //    $aReturn['chemicalPesticide']['control'] = $this->dashboard_overview_data((object)$aPost, 'control');

   //    // Natural pesticide
   //    $aPost['kpi_id'] = 4;
   //    $aReturn['naturalPesticide']['project'] = $this->dashboard_overview_data((object)$aPost, 'project');
   //    $aReturn['naturalPesticide']['control'] = $this->dashboard_overview_data((object)$aPost, 'control');

   //    // Water usage
   //    $aPost['kpi_id'] = 7;
   //    $aReturn['waterUsage']['project'] = $this->dashboard_overview_data((object)$aPost, 'project');
   //    $aReturn['waterUsage']['control'] = $this->dashboard_overview_data((object)$aPost, 'control');

   //    // Chemical Fertilizer
   //    $aPost['kpi_id'] = 5;
   //    $aReturn['chemicalFertilizer']['project'] = $this->dashboard_overview_data((object)$aPost, 'project');
   //    $aReturn['chemicalFertilizer']['control'] = $this->dashboard_overview_data((object)$aPost, 'control');

   //    // Natural Fertilizer
   //    $aPost['kpi_id'] = 6;
   //    $aReturn['naturalFertilizer']['project'] = $this->dashboard_overview_data((object)$aPost, 'project');
   //    $aReturn['naturalFertilizer']['control'] = $this->dashboard_overview_data((object)$aPost, 'control');

   //    // Input cost


   //    // Number of Farmers & cotton area
   //    $apicall = new Curlcall();
   //    $response = $apicall->get_api_call($aPost['country'],'api/dashboard_overview_data',$aPost,'POST');
   //    // dd($response);
   //    $aReturn['numberOfFarmers']['project'] = !empty($response['numberOfFarmers']['project']) ? $response['numberOfFarmers']['project'] : 0;
   //    $aReturn['numberOfFarmers']['control'] = !empty($response['numberOfFarmers']['control']) ? $response['numberOfFarmers']['control'] : 0;

   //    $aReturn['numberOfGenderFarmers']['Female'] = !empty($response['numberOfGenderFarmers']['Female']) ? $response['numberOfGenderFarmers']['Female'] : 0;
   //    $aReturn['numberOfGenderFarmers']['Male'] = !empty($response['numberOfGenderFarmers']['Male']) ? $response['numberOfGenderFarmers']['Male'] : 0;

   //    $aReturn['cottonArea']['project'] = !empty($response['cottonArea']['project']) ? $response['cottonArea']['project'] : 0;
   //    $aReturn['cottonArea']['control'] = !empty($response['cottonArea']['control']) ? $response['cottonArea']['control'] : 0;
   //    $aReturn['cottonArea']['cotton_irrigated'] = !empty($response['cottonArea']['cotton_irrigated']) ? $response['cottonArea']['cotton_irrigated'] : 0;
   //    $aReturn['cottonArea']['cotton_rainfed'] = !empty($response['cottonArea']['cotton_rainfed']) ? $response['cottonArea']['cotton_rainfed'] : 0;

   //    $aReturn['aPost'] = $aPost;
   //    return view('dashboard.dashboard',$aReturn);
   // }

   // public function dashboard_overview_data($Request, $project_control = 'project'){

   //    $response['data'] = [];
   //    $response['message'] = [];
   //    $response['error'] = 0;
   //    $ResposneCode = 200;

   //    $sql = 'SELECT mq.id,mq.form_id,mq.`type`,mq.name_key,(select form_table_name FROM master_forms WHERE id = mq.form_id) as data_table_name  FROM kpi_fields kf LEFT JOIN master_questions mq ON kf.field_id=mq.id WHERE kf.kpi_id=:kpi_id';

   //    $a_questions = DB::select($sql, array("kpi_id"=>$Request->kpi_id));

   //    $post['season'] = $Request->season;
   //    $post['state_id'] = $Request->state;
   //    $post['district_id'] = $Request->district;
   //    $post['block_id'] = $Request->block;
   //    $post['village_id'] = $Request->village;
   //    $post['brand_id'] = $Request->brand;
   //    $post['project_control'] = $project_control;

   //    $post['program_id'] = $Request->program;
   //    $post['xaxis'] = 'country';
   //    $post['kpi_id'] = $Request->kpi_id;
   //    $post['yaxis_id'] = 0;
   //    $post['kpi_has_fields'] = $Request->kpi_has_fields;
   //    //$post['a_questions'] = $a_questions;
   //    $post['a_questions'] = [];
   //    $post['selling_cost_questions'] = [];
   //    if($Request->kpi_has_fields == 1){
   //       $sql = 'SELECT mq.id,mq.form_id,mq.`type`,mq.name_key,(select form_table_name FROM master_forms WHERE id = mq.form_id) as data_table_name  FROM kpi_fields kf LEFT JOIN master_questions mq ON kf.field_id=mq.id WHERE kf.kpi_id=:kpi_id';
   //       if(!empty($Request->yaxis_id)){
   //           $sql .= ' AND mq.id='.$Request->yaxis_id;
   //       }
   //       $a_questions = DB::select($sql, array("kpi_id"=>$Request->kpi_id));
   //       $post['a_questions'] = $a_questions;
   //    }

   //    if($Request->kpi_id == 8){
   //       $sql = 'SELECT mq.id,mq.form_id,mq.`type`,mq.name_key,(select form_table_name FROM master_forms WHERE id = mq.form_id) as data_table_name  FROM kpi_fields kf LEFT JOIN master_questions mq ON kf.field_id=mq.id WHERE kf.kpi_id=:kpi_id';
   //       if(!empty($Request->yaxis_id)){
   //           $sql .= ' AND mq.id='.$Request->yaxis_id;
   //       }
   //       $a_questions = DB::select($sql, array("kpi_id"=>1));
   //       $post['a_questions'] = $a_questions;

   //       $sql = 'SELECT mq.id,mq.form_id,mq.`type`,mq.name_key,(select form_table_name FROM master_forms WHERE id = mq.form_id) as data_table_name  FROM kpi_fields kf LEFT JOIN master_questions mq ON kf.field_id=mq.id WHERE kf.kpi_id=:kpi_id';
   //       if(!empty($Request->yaxis_id)){
   //           $sql .= ' AND mq.id='.$Request->yaxis_id;
   //       }
   //       $a_questions = DB::select($sql, array("kpi_id"=>20));
   //       $post['selling_cost_questions'] = $a_questions;
   //   }

   //     dd(json_encode($post));
   //    $apicall = new Curlcall();
   //    $response = $apicall->get_api_call($Request->country,'api/get_comparison_chart_data',$post,'POST');
   //   dd($response);
   //    return !empty($response['data']['series1'][0]) ? $response['data']['series1'][0] : 0;
   // }

   public function dashboard_overview_details(Request $request){
     // $aReturn = $this->common_data();
      //dd($aReturn);
      $post=array();
      $aPost = array();
      if($request->input('submit')=='search'){
         $aPost = $request->input();
      }
      $post['country'] = !empty($aPost['country'])?$aPost['country']:env('CURRENT_COUNTRY') ;
      $post['season'] = env('CURRENT_SEASON');
      $post['state_id'] = !empty($aPost['state_id'])?$aPost['state_id']:0;
      $post['district_id'] = !empty($aPost['district_id'])?$aPost['district_id']:0;
      $post['block_id'] =!empty( $aPost['block_id'])?  $aPost['block_id']:0;
      $post['village_id'] = !empty($aPost['village_id'])?$aPost['village_id']:0;
      $post['brand_id'] = !empty($aPost['brand_id'])?$aPost['brand_id']:0;
      $post['program_id'] = !empty($aPost['program_id'])?$aPost['program_id']:0;
      $post['xaxis'] = 'country';
      $post['yaxis_id'] = 0;

      $a_kpis[] = array("kpi_id"=>1,"class_name"=>"col-sm-4","kpi_has_fields"=>1,"kpi_name"=>"Input Cost","chart_type"=>"bar");
      $a_kpis[] = array("kpi_id"=>2,"class_name"=>"col-sm-4","kpi_has_fields"=>0,"kpi_name"=>"Yeild","chart_type"=>"bar");
      $a_kpis[] = array("kpi_id"=>9,"class_name"=>"col-sm-4","kpi_has_fields"=>0,"kpi_name"=>"Chemical Pesticide","chart_type"=>"bar");
      $a_kpis[] = array("kpi_id"=>10,"class_name"=>"col-sm-4","kpi_has_fields"=>0,"kpi_name"=>"Natural Pesticide","chart_type"=>"bar");
      $a_kpis[] = array("kpi_id"=>11,"class_name"=>"col-sm-4","kpi_has_fields"=>0,"kpi_name"=>"Chemical Fertilizer","chart_type"=>"bar");
      $a_kpis[] = array("kpi_id"=>12,"class_name"=>"col-sm-4","kpi_has_fields"=>0,"kpi_name"=>"Natural Fertilizer","chart_type"=>"bar");
      $a_kpis[] = array("kpi_id"=>7,"class_name"=>"col-sm-4","kpi_has_fields"=>0,"kpi_name"=>"Water Usage","chart_type"=>"bar");
      $a_kpis[] = array("kpi_id"=>8,"class_name"=>"col-sm-4","kpi_has_fields"=>0,"kpi_name"=>"Profit","chart_type"=>"bar");
      $a_options[] = 'project';
      $a_options[] = 'control';

     foreach($a_kpis as $val)
     {
         foreach($a_options as $farmer_type)
         {
            $post['kpi_id'] = $val['kpi_id'];
            $post['kpi_has_fields']=$val['kpi_has_fields'];

            $post['project_control']=$farmer_type;
            if($val['kpi_has_fields'] == 1){
               $sql = 'SELECT mq.id,mq.form_id,mq.`type`,mq.name_key,(select form_table_name FROM master_forms WHERE id = mq.form_id) as data_table_name  FROM kpi_fields kf LEFT JOIN master_questions mq ON kf.field_id=mq.id WHERE kf.kpi_id=:kpi_id';
               if(!empty( $post['yaxis_id'])){
                   $sql .= ' AND mq.id='. $post['yaxis_id'];
               }
               $a_questions = DB::select($sql, array("kpi_id"=>$val['kpi_id']));
               $post['a_questions'] = $a_questions;
            }

            if($val['kpi_id'] == 8){
               $sql = 'SELECT mq.id,mq.form_id,mq.`type`,mq.name_key,(select form_table_name FROM master_forms WHERE id = mq.form_id) as data_table_name  FROM kpi_fields kf LEFT JOIN master_questions mq ON kf.field_id=mq.id WHERE kf.kpi_id=:kpi_id';
               if(!empty( $post['yaxis_id'])){
                   $sql .= ' AND mq.id='. $post['yaxis_id'];
               }
               $a_questions = DB::select($sql, array("kpi_id"=>1));
               $post['a_questions'] = $a_questions;

               $sql = 'SELECT mq.id,mq.form_id,mq.`type`,mq.name_key,(select form_table_name FROM master_forms WHERE id = mq.form_id) as data_table_name  FROM kpi_fields kf LEFT JOIN master_questions mq ON kf.field_id=mq.id WHERE kf.kpi_id=:kpi_id';
               if(!empty( $post['yaxis_id'])){
                   $sql .= ' AND mq.id='. $post['yaxis_id'];
               }
               $a_questions = DB::select($sql, array("kpi_id"=>20));
               $post['selling_cost_questions'] = $a_questions;

            }
           //  dd(json_encode($post));
            $apicall = new Curlcall();
            $response = $apicall->get_api_call($post['country'],'api/get_comparison_chart_data',$post,'POST');
            $a_chart[$val['kpi_id']][$farmer_type]= !empty($response['data']['series1']) ? $response['data']['series1'] : 0;

         //
         }
         $a_chart[$val['kpi_id']]['class_name']=$val['class_name'];
         $a_chart[$val['kpi_id']]['kpi_name']=$val['kpi_name'];
         $a_chart[$val['kpi_id']]['chart_type']=$val['chart_type'];
         $a_chart[$val['kpi_id']]['label']=['Project','Control'];
     }

      // Number of Farmers & cotton area
      $apicall = new Curlcall();
      $response = $apicall->get_api_call($post['country'],'api/dashboard_overview_data',$post,'POST');
      // dd($response);
      $p_chart['numberOfFarmers']['project'] = !empty($response['numberOfFarmers']['project']) ? $response['numberOfFarmers']['project'] : 0;
      $p_chart['numberOfFarmers']['control'] = !empty($response['numberOfFarmers']['control']) ? $response['numberOfFarmers']['control'] : 0;
      $p_chart['numberOfFarmers']['chart_name']='number_farmers_chart';

      $p_chart['numberOfFarmers']['Female'] = !empty($response['numberOfGenderFarmers']['Female']) ? $response['numberOfGenderFarmers']['Female'] : 0;
      $p_chart['numberOfFarmers']['Male'] = !empty($response['numberOfGenderFarmers']['Male']) ? $response['numberOfGenderFarmers']['Male'] : 0;

      $p_chart['cottonArea']['project'] = !empty($response['cottonArea']['project']) ? $response['cottonArea']['project'] : 0;
      $p_chart['cottonArea']['control'] = !empty($response['cottonArea']['control']) ? $response['cottonArea']['control'] : 0;
      $p_chart['cottonArea']['chart_name']='cotton_area_chart';
      $p_chart['cottonArea']['cotton_irrigated'] = !empty($response['cottonArea']['cotton_irrigated']) ? $response['cottonArea']['cotton_irrigated'] : 0;
      $p_chart['cottonArea']['cotton_rainfed'] = !empty($response['cottonArea']['cotton_rainfed']) ? $response['cottonArea']['cotton_rainfed'] : 0;

      $html='';
      $html.='<div class="row flex ">';
            $html.='<div class="col-sm-4">
            <div class="card m-1" style="border-radius:15px">
               <div class="card-body" >
                  <h5>Number of Farmers</h5>
                     <div class="row">
                        <div class="col-xm-4 ">
                           <div class="float-lg-left m-1">
                              <h6>'.$p_chart['numberOfFarmers']['Female'].' + '.$p_chart['numberOfFarmers']['Male'].'</h6>
                              <span>Total Project Farmers</span>
                           </div>
                           <div class="float-lg-left m-1">
                              <h6>'.$p_chart['numberOfFarmers']['Female'].'</h6>
                              <span>Total Female Farmers</span>
                           </div>
                           <div class="float-lg-right m-1">
                              <h6>'.$p_chart['numberOfFarmers']['Male'].'</h6>
                              <span>Total Male Farmers</span>
                           </div>
                        </div>
                        <div class="col-xm-12">
                           <div class="canvas1" id="number_farmers_chart">
                         </div>
                     </div>

                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm-4">
               <div class="card m-1" style="border-radius:15px">
                  <div class="card-body" >
                     <h5>Cotton area</h1>
                        <div class="row">
                           <div class="col-xm-4 ">
                              <div class="float-lg-left m-1">
                                 <h6>'. $p_chart['cottonArea']['cotton_irrigated'].' + '. $p_chart['cottonArea']['cotton_rainfed'].'</h6>
                                 <span>Total Cotton area</span>
                              </div>
                              <div class="float-lg-left m-1">
                                 <h6>'. $p_chart['cottonArea']['cotton_irrigated'] .'</h6>
                                 <span>Total Irrigaated area</span>
                              </div>
                              <div class="float-lg-right m-1">
                                 <h6>'.$p_chart['cottonArea']['cotton_rainfed'].'</h6>
                                 <span>Total Rainfed Farmers</span>
                              </div>
                           </div>
                           <div class="col-xm-12">
                              <div class="canvas1" id="cotton_area_chart">
                           </div>
                        </div>

                     </div>
                  </div>
               </div>
            </div>   ';
      foreach($a_chart as $chart_id=>$chart){

      $html.='<div class="col-sm-4"><div class="card m-1" style="border-radius:15px">
			<div class="card-body" >
				<h5>'.$chart['kpi_name'].'<span>(Kg/Acre)</span>
				</h1>
				<div class="canvas" id="chart_'.$chart_id.'">

				</div>
			</div>
		</div></div>';

      }
	   $html.=' </div>';
      $aReturn['aPost'] = $post;
      $aReturn['a_chart']=$a_chart;
      $aReturn['p_chart']=$p_chart;
      $aReturn['html']=$html;

      // dd($aReturn);
      return $aReturn;
   }


   public function dashboard_health_safty_details(Request $Request){
      $aPost = array();
      $post=array();
      if(!empty($Request->all()))
      {
         $aPost = $Request->all();
      }

      $a_questions=array();
      $post['country'] = !empty($aPost['country'])?$aPost['country']:env('CURRENT_COUNTRY') ;
      $post['season'] = env('CURRENT_SEASON');
      $post['state_id'] = !empty($aPost['state_id'])?$aPost['state_id']:0;
      $post['district_id'] = !empty($aPost['district_id'])?$aPost['district_id']:0;
      $post['block_id'] =!empty( $aPost['block_id'])?  $aPost['block_id']:0;
      $post['village_id'] = !empty($aPost['village_id'])?$aPost['village_id']:0;
      $post['brand_id'] = !empty($aPost['brand_id'])?$aPost['brand_id']:0;
      $post['form_id']=18;
      $post['program_id'] = !empty($aPost['program_id'])?$aPost['program_id']:0;
      $post['xaxis'] = 'country';
      $post['yaxis_id'] = 0;
      $aReturn['a_chart'] = [];

      $sql = 'SELECT mq.id,mq.name_description,mq.options_inline,(select form_table_name FROM master_forms WHERE id = mq.form_id) as data_table_name  FROM  master_questions mq WHERE mq.form_id=:form_id';
      $questions = DB::select($sql, array("form_id"=>18));

      $a_que=array(74,76,77);
      foreach($questions as $value)
      {
          if(in_array($value->id,$a_que))
         {

            if(count((array)json_decode($value->options_inline))>4){
               $value->class_name="col-sm-6";
            }else{
               $value->class_name="col-sm-4";
            }
            $value->chart_type='bar';
            $value->options_inline=array_keys((array)json_decode($value->options_inline));
            $a_questions[]=$value;
         }

      }
      // $post['farmer_type']='project';
            // dd($aReturn['option']);
            $a_options[] = 'project';
            $a_options[] = 'control';
      foreach($a_questions as $que){

         foreach($a_options as $farmer_type)
         {

            $post['a_question']=$que;
            $post['farmer_type']=$farmer_type;

            $apicall = new Curlcall();
            // dd(json_encode($post));
            $response[$farmer_type][$que->id] = $apicall->get_api_call( $post['country'],'api/get_health_and_safty_chart_data',$post,'POST');
            // dd($response);
            $a_chart[$que->id][$farmer_type]=isset($response[$farmer_type][$que->id]['data'])?$response[$farmer_type][$que->id]['data']:[];
            $a_chart[$que->id]['label']=isset($a_chart[$que->id][$farmer_type]['label'])?$a_chart[$que->id][$farmer_type]['label']:[];
            $a_chart[$que->id][$farmer_type]['series']=isset($a_chart[$que->id][$farmer_type]['series'])?implode(",",$a_chart[$que->id][$farmer_type]['series']):0;

            $a_chart[$que->id][$farmer_type]=explode(",",$a_chart[$que->id][$farmer_type]['series']);
            $a_chart[$que->id]['name_description']=$que->name_description;

         }
         $a_chart[$que->id]['class_name']=$que->class_name;
         $a_chart[$que->id]['chart_type']=$que->chart_type;


      }

      $html='';
      $html.='<div class="row flex ">';
      foreach($a_chart as $chart_id=>$chart){
         $html.='<div class="'.$chart['class_name'].'">
             <div class="card" style="border-radius:15px">
                <div class="card-body" >
                   <h5>'.$chart['name_description'].'</h1>
                   <div class="canvas" id="chart_'.$chart_id.'">

                </div>

                   </div>
                </div>
             </div>';
      }

      $html.='</div>';
      $aReturn['html']=$html;

     $aReturn['a_chart']=$a_chart;
      $aReturn['aPost']=$post;
      $aReturn['aPost']=$post;
      //  dd($aReturn);
     $aReturn['aPost']=$post;
      //  dd($aReturn);

      return $aReturn;
   }

   public function dashboard_sustainable_practices_details(Request $Request){
      // dd($aReturn);
      $post=array();
      $aPost=array();
      if(!empty($Request->all()))
      {
         $aPost = $Request->all();
      }

      $a_questions=array();
      $post['country'] = !empty($aPost['country'])?$aPost['country']:env('CURRENT_COUNTRY') ;
      $post['season'] = env('CURRENT_SEASON');
      $post['state_id'] = !empty($aPost['state_id'])?$aPost['state_id']:0;
      $post['district_id'] = !empty($aPost['district_id'])?$aPost['district_id']:0;
      $post['block_id'] =!empty( $aPost['block_id'])?  $aPost['block_id']:0;
      $post['village_id'] = !empty($aPost['village_id'])?$aPost['village_id']:0;
      $post['brand_id'] = !empty($aPost['brand_id'])?$aPost['brand_id']:0;
      $post['program_id'] = !empty($aPost['program_id'])?$aPost['program_id']:0;
      $a_forms[] = array("id"=>1,"class_name"=>"col-sm-12");
      $a_forms[] = array("id"=>11,"class_name"=>"col-sm-4");
      $a_forms[] = array("id"=>12,"class_name"=>"col-sm-8");
      $a_forms[] = array("id"=>13,"class_name"=>"col-sm-4");
      $a_forms[] = array("id"=>14,"class_name"=>"col-sm-8");

      $post['xaxis'] = 'country';
      $post['yaxis_id'] = 0;

      $aReturn['a_charts'] = [];
      foreach($a_forms as $key=>$a_form){

         $sql = 'SELECT mq.id,mq.name_description,mf.form_table_name as data_table_name,mf.form_name  FROM  master_questions mq LEFT JOIN master_forms mf ON mq.form_id = mf.id WHERE mq.form_id=:form_id';

         $post['a_questions'] = DB::select($sql, array("form_id"=>$a_form['id']));


         $apicall = new Curlcall();
         //dd(json_encode($post));

         $response[$post['a_questions'][0]->data_table_name] = $apicall->get_api_call( $post['country'],'api/get_sustainable_practices_chart_data',$post,'POST');
         $response[$post['a_questions'][0]->data_table_name]=$response[$post['a_questions'][0]->data_table_name]['data'];
         $response[$post['a_questions'][0]->data_table_name]['class_name'] = $a_form['class_name'];
         $response[$post['a_questions'][0]->data_table_name]['chart_type'] = "bar";
         $response[$post['a_questions'][0]->data_table_name]['form_title'] = $post['a_questions'][0]->form_name;


      }

      $html='';
      $html='<div class="row flex ">';
        foreach($response as $chart_id=>$chart){
        $html.=' <div class="'.$chart['class_name'].'">
            <div class="card m-1" style="border-radius:15px; height:450px;">
               <div class="card-body" >
                  <h5>'.  $chart['form_title'].'</h1>

                  <div class="canvas" id="chart_'. $chart_id.'">

                  </div>
               </div>
            </div>
         </div> ';
        }
    $html.=' </div>';
      $aReturn['html']=$html;
      $aReturn['aPost']=$post;
      $aReturn['a_chart']=$response;
      // dd($aReturn);
      return $aReturn;
   }


   public function dashboard_women_empowerment_details(Request $request){
      $aReturn = $this->common_data();


      return view('dashboard.dashboard',$aReturn);
   }

   public function dashboard_decision_making_details(){
      $aReturn = $this->common_data();

      return view('dashboard.dashboard',$aReturn);
   }

   public function dashboard_social_fairness_details(){
      $aReturn = $this->common_data();

      return view('dashboard.dashboard',$aReturn);
   }

   public function dashboard_trust_level_details(Request $Request){

      $post = array();
      $a_questions=array();
      $aPost=array();
      if(!empty($Request->all()))
      {
         $aPost = $Request->all();
      }

      $a_questions=array();
      $post['country'] = !empty($aPost['country'])?$aPost['country']:env('CURRENT_COUNTRY') ;
      $post['season'] = env('CURRENT_SEASON');
      $post['state_id'] = !empty($aPost['state_id'])?$aPost['state_id']:0;
      $post['district_id'] = !empty($aPost['district_id'])?$aPost['district_id']:0;
      $post['block_id'] =!empty( $aPost['block_id'])?  $aPost['block_id']:0;
      $post['village_id'] = !empty($aPost['village_id'])?$aPost['village_id']:0;
      $post['brand_id'] = !empty($aPost['brand_id'])?$aPost['brand_id']:0;
      $post['program_id'] = !empty($aPost['program_id'])?$aPost['program_id']:0;
      $post['form_id']=24;
      $post['xaxis'] = 'country';
      $post['yaxis_id'] = 0;
      $aReturn['a_chart'] = [];

      $sql = 'SELECT mq.id,mq.name_description,mq.options_inline,(select form_table_name FROM master_forms WHERE id = mq.form_id) as data_table_name  FROM  master_questions mq WHERE mq.form_id=:form_id';
      $questions = DB::select($sql, array("form_id"=>$post['form_id']));

      $a_que=array(130,132,133,134,135);
      foreach($questions as $value)
      {
          if(in_array($value->id,$a_que))
         {

            if(count((array)json_decode($value->options_inline))>4){

               $value->class_name="col-sm-8";
             }else{
               $value->class_name="col-sm-4";
             }
             $value->chart_type="bar";
            $value->options_inline=array_keys((array)json_decode($value->options_inline));
            $a_questions[]=$value;
         }

      }

      foreach($a_questions as $que){

           $post['a_question']=$que;
            $post['farmer_type']='Project';

            $apicall = new Curlcall();
         //    dd(json_encode($post));
            $response[$que->id] = $apicall->get_api_call( $post['country'],'api/get_trust_level_chart_data',$post,'POST');

            $a_chart[$que->id]=isset($response[$que->id]['data'])?$response[$que->id]['data']:[];
         $a_chart[$que->id]['label']=isset($a_chart[$que->id]['label'])?explode(",",(implode(",",$a_chart[$que->id]['label']))):[];
         $a_chart[$que->id]['project']=isset($a_chart[$que->id]['series'])?explode(",",(implode(",",$a_chart[$que->id]['series']))):0;
         $a_chart[$que->id]['name_description']=$que->name_description;
         $a_chart[$que->id]['class_name']=$que->class_name;
         $a_chart[$que->id]['chart_type']=$que->chart_type;

      }
      $aReturn['aPost']=$post;
      $html='';
      $html='<div class="row flex ">';
        foreach($a_chart as $chart_id=>$chart){
        $html.=' <div class="'.$chart['class_name'].'">
            <div class="card m-1" style="border-radius:15px; height:450px;">
               <div class="card-body" >
                  <h5>'. $chart['name_description'].'</h1>

                  <div class="canvas" id="chart_'. $chart_id.'">

                  </div>
               </div>
            </div>
         </div> ';
        }
    $html.=' </div>';
      $aReturn['html']=$html;
      $aReturn['a_chart']=$a_chart;

      return $aReturn;
   }
   Public function common_data(){
      $result = array();
      $aReturn = array("is_active" => 1);
      $sSQL = 'SELECT id,country_name FROM master_countries WHERE active=1';
      $aReturn['country'] = DB::SELECT($sSQL, array());

      $sSQL = 'SELECT season FROM master_season WHERE is_active=1';
      $aReturn['season'] = DB::SELECT($sSQL, array());

      $aReturn['current_season']=env('CURRENT_SEASON');
      $aReturn['state'] = [];

      $url_state = '/api/get_master_state';
      $apicall = new MultiCurlCall();
      $a_states = $apicall->multi_curl_call($url_state);

      if (!empty($a_states)) {
         foreach ($a_states as $key => $state) {
            if (!empty($state->state)) {
               $temp = array($state->state);
               for ($i = 0; $i < count($temp); $i++) {

                  $result = array_merge($result, $temp[$i]);
               }
            }
         }
      }
      // dd($result);

      $url_district = '/api/get_master_district';
      $apicall = new MultiCurlCall();
      $a_district = $apicall->multi_curl_call($url_district);

      $result2 = array();
      if (!empty($a_district)) {
         foreach ($a_district as $key => $district) {
            if (!empty($district->districts)) {
               $temp = array($district->districts);
               for ($i = 0; $i < count($temp); $i++) {

                  $result2 = array_merge($result2, $temp[$i]);
               }
            }
         }
      }

      $url_block = '/api/get_master_block';
      $apicall = new MultiCurlCall();
      $a_block = $apicall->multi_curl_call($url_block);
      $result3 = array();
      if (!empty($a_block)) {
         foreach ($a_block as $key => $block) {
            if (!empty($block->block)) {
               $temp = array($block->block);
               for ($i = 0; $i < count($temp); $i++) {

                  $result3 = array_merge($result3, $temp[$i]);
               }
            }
         }
      }


      $url_village = '/api/get_master_village';
      $apicall = new MultiCurlCall();
      $a_village = $apicall->multi_curl_call($url_village);

      $result4 = array();
      if (!empty($a_village)) {
         foreach ($a_village as $key => $village) {
            if (!empty($village->village)) {
               $temp = array($village->village);
               for ($i = 0; $i < count($temp); $i++) {

                  $result4 = array_merge($result4, $temp[$i]);
               }
            }
         }
      }

      $aReturn['state'] = !empty($result) ? $result : [];
      $aReturn['current_season']= env('CURRENT_SEASON');
      $aReturn['districts'] = !empty($result2) ? $result2 : [];
      $aReturn['village'] = !empty($result4) ? $result4 : [];
      $aReturn['block'] = !empty($result3) ? $result3 : [];
      $aReturn['filter'] = config('custom.filter');
      $aReturn['option'] = config('custom.option');
      $aReturn['brand'] = Master_brand::get_master_brand($aReturn);
      $aReturn['program'] = Master_program::get_master_program($aReturn);
      $aReturn['tab'] = array(
         'overview' => 'Overview',
         'health_and_safety' => 'Health and Safety',
         'sustainable_practices' => 'Sustainable Practices',
         // 'women_empowerment' => 'Women Empowerment',
         // 'decision_making' => 'Decision Making',
         // 'social_fairness' => 'Social Fairness',
         'trust_level' => 'Trust Level'
      );
      $aReturn['action']=['','',''];
      $aReturn['data'] = [160, 220, 200, 300];
      return $aReturn;
   }

}
