<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
 
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\User;
use App\EducationEnhancements;
use App\Education;
use App\Minor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use DateTime;
 
class EducationEnhancementController extends Controller
{
  public function postEducationEnhancement(Request $request)
  {
      $recourd = DB::table('education')->where(array('user_id' => $request->get('user_id'),'id' => $request->get('education_id')))->count(); 
      if($recourd > 0) {
        $tbl_education = Education::where(array('user_id' => $request->get('user_id'),'id' => $request->get('education_id')))->first();
      } else {
        $tbl_education = new Education;
      }
        $tbl_education->user_id = $request->user_id;
      if(!empty($request->get('overallgpa'))){
        $tbl_education->overallgpa = $request->get('overallgpa');
      } 
      else{
        $tbl_education->overallgpa = null;
      }
      if(!empty($request->get('majorgpa'))){
        $tbl_education->majorgpa = $request->get('majorgpa');
      }
      else{
        $tbl_education->majorgpa = null;
      }
      if(!empty($request->get('totaloverallgpa'))){
        $tbl_education->totaloverallgpa = $request->get('totaloverallgpa');
      } 
      else{
        $tbl_education->totaloverallgpa = null;
      }
      if(!empty($request->get('totalmajorgpa'))){
        $tbl_education->totalmajorgpa = $request->get('totalmajorgpa');
      }
      else{
        $tbl_education->totalmajorgpa = null;
      }
      if(!empty($request->get('credits_completed'))){
        $tbl_education->credits_completed = $request->get('credits_completed');
      }
      else{
        $tbl_education->credits_completed = null;
      }
      $tbl_education->save();
      //store in education table end

      // start minor table
      $recourd = DB::table('minor')->where(array('education_id' => $request->get('education_id'),'id' => $request->get('minor_id')))->get(); 

      if(count($recourd) > 0) {
        $tbl_minor = Minor::where(array('education_id' => $request->get('education_id'),'id' => $request->get('minor_id')))->first();
      } else {
        $tbl_minor = new Minor;
      }
      $tbl_minor->education_id =  $tbl_education->id;
      if(!empty($request->get('minor'))){
        $minors = array();
        for ($i = 0; $i < count($request->minor); $i++) {
              $minors[] = $request->minor[$i]['name'];
            $tbl_minor->minor = implode(',', $minors);
          }
        }
        else{
          $tbl_minor->minor = null;
        }
        $tbl_minor->save();
      // end minore table 

      // kaun data insert here
      $recourd = DB::table('kaun')->where('user_id',  $request->get('user_id'))->count(); 
      if($recourd > 0) {
        $tbl_kaun = EducationEnhancements::where('user_id', $request->user_id)->first();
      } else {
        $tbl_kaun = new EducationEnhancements;
      }
      // $tbl_kaun->education_id =  $tbl_education->id;
      $tbl_kaun->type = 'Credits Completed';
      $tbl_kaun->user_id = $request->user_id;
      if(!empty($request->get('academic_achievement'))){
        $academic_achievements = array();
        for ($i = 0; $i < count($request->academic_achievement); $i++) {

          $academic_achievements[] = $request->academic_achievement[$i]['name'];
      }
        $tbl_kaun->academic_achievement = implode(',', $academic_achievements);
      }
      else{
        $tbl_kaun->academic_achievement = null;
      }
      if(!empty($request->get('clubs_organizations'))){
        $clubs_organizationss = array();
        for ($i = 0; $i < count($request->clubs_organizations); $i++) {
          $clubs_organizationss[] = $request->clubs_organizations[$i]['name'];
      }
        $tbl_kaun->clubs_organizations = implode(',', $clubs_organizationss);
      }
      else{
        $tbl_kaun->clubs_organizations = null;
      }
      $tbl_kaun->save();
      //store in kaun table end
      return $this->respondWithEducationEnhancement($request, $request->user_id);
      // return response()->json([
      //    'success' => true
      // ]);
  }
 
  protected function respondWithEducationEnhancement(Request $request, $id='') { 
    if($id !==''){
      $id=$id; 
    }
    else{
      $id = $request->session()->get('yak_user_id');
    }
    $user = User::
      leftJoin('education', function ($join) {
          $join->on('users.id', '=', 'education.user_id');
      })
      ->leftJoin('kaun', function ($join) {
        $join->on('users.id', '=', 'kaun.user_id');
      })
      ->leftJoin('minor', function ($join) {
        $join->on('education.id', '=', 'minor.education_id');
      })
      ->where('users.id', $id)
      ->first();
    $user_id = DB::table('users')->where('id',$id)->first()->id;
    $user['user_id'] = $user_id;
    $user['tips'] = DB::table('field_tips')->where('page', 'education enhancements')->get(); 
    $educationData = DB::table('education')->select('school.name as name','education.*')
    ->leftJoin('school', function ($join) { $join->on('school.id', '=', 'education.school_id'); })->where('user_id', $id)->orderBy('grad_date', 'desc')->get();
    $minorDatePush=array(); 
    foreach ($educationData as $key => $value) {
      $minorDate = DB::table('minor')->where('education_id', $value->id)->get(); 
      array_push($minorDatePush, $minorDate);
    }
    $user['minorDatePush'] = $minorDatePush;
    $user['educationData'] = $educationData;
    $education = DB::table('education')->where('user_id', $id)->get(); 
    $currDate = date('Y-m-d');
    $data=array(); 
    foreach ($education as $key => $value) {
      $fdate = $value->grad_date;
      $tdate = $currDate;
      $datetime1 = new DateTime($fdate);
      $datetime2 = new DateTime($tdate);
      $interval = $datetime1->diff($datetime2);
      $days = $interval->format('%a');
      $years_remaining = intval($days / 365);
      array_push($data, $years_remaining);
    }
    $isGrade = false;
    $i=0;
    $isCheck =array();
    foreach ($data as $key => $val) {
      if($val <= 5) {
        $i++;
        array_push($isCheck, $i);
      }
    }
    if(count($isCheck) == count($data)){
      $isGrade = true;
    }
    if(in_array(5, $data)) {
      $grad_Date = true;
    }
    else{
      $grad_Date = false;
    }

    $user['isGrade'] = $isGrade;
    $user['isgradDate'] = $grad_Date;

    return response()->json([
      'success' => true,
      // 'access_token' => $token,
      'user' => $user,
      'token_type' => 'bearer',
    ]);
  }
}