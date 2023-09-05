<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
 
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\User;
use App\EducationEnhancements;
use App\Education;
use App\Position;
use App\Resume;
use App\Minor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use DateTime;
use Mail;
use App\ResetPassword;
use Illuminate\Support\Facades\Hash;

class MyResumeController extends Controller
{
    public function createPassword(Request $request)
    {
        $user = User::find($request->id);
        if($request->skip == "false"){
            $user->password = Hash::make($request->password);
            $user->email = $request->EmailAddress;
            $user->save();
            $data = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $request->EmailAddress,
                'year' => date('Y')
            ];
            Mail::send('Email.welcome', $data, function($message) use($request) {
                $message->to($request->EmailAddress)->subject('Welcome to the Best Online Resume Builder');
                $message->from('jseiter@resumeyak.com','Resume Yak');
            });
            return response()->json([
                'success' => true,
                'status' => 1
            ]);
            // return response()->json(200);
        }
        else{
            $ResetPassword = new ResetPassword;
            $ResetPassword->email = $request->email;
            $ResetPassword->token = md5($request->email);
            $ResetPassword->save();
            $data = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'token'=>$ResetPassword->token,
                'year' => date('Y')
            ];
            Mail::send('Email.skip_create_password', $data, function($message) use($user) {
                $message->to($user->email)->subject('Welcome to the Best Online Resume Builder');
                $message->from('jseiter@resumeyak.com','Resume Yak');
            });
            return response()->json([
                'success' => true,
                'status' => 1
            ]);
        }
    }

    public function resetPassword(Request $request)
    {
        $user = User::find($request->id);
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'success' => true,
            'status' => 1
        ]);
    }

    public function getPosition(Request $request)
    {
        $position = Position::where('pos_name', 'like', '%' . $request->position . '%')->get();
        return response()->json([
            'success' => true,
            'status' => 1,
            'data' => $position
        ]);
    }

    public function saveResume(Request $request)
    {
        $data = [];
        $Resume_id = '';
        $new = '';
        $Added_id = '';
        foreach ($request->position_id as $key => $value) {
            if($value !== false){
            if($key < count($request->id)){
                if($request->id[$key] != null) {
                    $resume = Resume::find($request->id[$key]);
                    $Resume_id = $request->id[$key];
                    $new = '';
                }
            } else {
                $resume = new Resume;
                $new = 1;
            }
            $resume->position_id = $value;
            $resume->name = $request->name[$key];
            $resume->user_id = $request->session()->get('yak_user_id');
            $resume->middle_initial = $request->middle_initial;
            $resume->address_display = $request->address_display;
            $resume->email = $request->email;
            $resume->address = $request->address;
            $resume->phone = $request->phone;
            $resume->profile = $request->profile;
            $resume->website = $request->website;
            $resume->hide_headline = $request->hide_headline;
            $resume->employment_date = $request->employment_date;
            $resume->employment_location = $request->employment_location;
            $resume->employment_description = $request->employment_description;
            $resume->employment_emphasis = $request->employment_emphasis;
            $resume->education_date = $request->education_date;
            $resume->education_location = $request->education_location;
            $resume->education_emphasis = $request->education_emphasis;
            $resume->last_step = 5;
            $resume->save();
            if($new == 1){
                $Added_id = $resume->id;
            }
        }
        }
        $id = $request->session()->get('yak_user_id');
        if($id != '') {
            $user = User::find($id);
            $user->page = $request->page;
            $user->save();
        }
        if($Resume_id  == ''){
            $Resume_id = $resume->id;
        }
        if( $Added_id != ''){
            return response()->json([
                'success' => true,
                'status' => 1,
                'resume_id' => $Added_id
            ]);
        }
        else
        {
            return response()->json([
                'success' => true,
                'status' => 1,
                'resume_id' => ''
            ]);
        }
    }

    public function getResume(Request $request)
    {
        $id = $request->session()->get('yak_user_id');
        $resumeData = Resume::select('resumes.id as resume_id', 'position.id as pos_id', 'resumes.*', 'position.*')
        ->leftJoin('position', function ($join) {
            $join->on('resumes.position_id', '=', 'position.id');
        })
        ->where('user_id', $id)
        ->get();

        if(count($resumeData) > 0){
            return response()->json([
                'success' => true,
                'status' => 1,
                'data' => $resumeData
            ]);
        }
        else 
        {
            return response()->json([
                'success' => true,
                'status' => 0,
            ]);
        }
    }

    public function deleteResume(Request $request)
    {
        $ResumeId = $request->id;
        $resumeData = Resume::find($ResumeId);
        $resumeData->delete();
        
        $id = $request->session()->get('yak_user_id');
        $resumeData = Resume::select('resumes.id as resume_id', 'position.id as pos_id', 'resumes.*', 'position.*')
        ->leftJoin('position', function ($join) {
            $join->on('resumes.position_id', '=', 'position.id');
        })
        ->where('user_id', $id)
        ->get();

        return response()->json([
            'success' => true,
            'status' => 1,
            'data' => $resumeData
        ]);
    }


}