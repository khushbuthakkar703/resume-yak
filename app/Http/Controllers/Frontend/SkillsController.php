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
use App\Skills;
use App\Minor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use DateTime;
use Mail;
use App\ResetPassword;
use Illuminate\Support\Facades\Hash;

class SkillsController extends Controller
{
    public function getSkills(Request $request)
    {
        $id = $request->session()->get('yak_user_id');
        $resume = Resume::where('id','=',$request->resume_id)->first();
        if($id != '') {
            $resume = Resume::where('user_id','=',$id)->where('id','=',$request->resume_id)->first();
        }
        
        $skillsData = [];
        if(!empty($resume)) {
            $resume_id = $request->resume_id;
            $position_id = $resume->position_id;
            $skillsData = Skills::select('skills.id','skills.name')
                ->leftJoin('position_skill', 'position_skill.skill_id', '=', 'skills.id')
                ->where('position_skill.position_id', $position_id)
                ->whereNotIN('skills.id', function($query) use ($resume_id)
                {
                    $query->select(DB::raw('skill_id'))
                        ->from('user_skills')
                        ->whereRaw("user_skills.skill_id = skills.id")
                        ->whereRaw("user_skills.resume_id = $resume_id");
                })
                ->where('skills.is_active', '1')
                //->groupBy('skills.id')
                ->orderBy('skills.weak', 'ASC')
                ->orderBy('position_skill.user_rating', 'DESC')
                ->orderBy('skills.id', 'ASC')
                ->get()->toArray();
        }
        return response()->json([
            'success' => true,
            'status' => 1,
            'data' => $skillsData
        ]);
    }
    
    public function getUserSkills(Request $request) {
        $id = ($request->session()->get('yak_user_id')) ? $request->session()->get('yak_user_id') : 0;
        $resume_id = $request->resume_id;
        $UserSkillData = Skills::select('skills.id','user_skills.custom_skill as name','user_skills.id as user_skill_id')
            ->rightJoin('user_skills', 'user_skills.skill_id', '=', 'skills.id')
            ->where(function($query) use ($id,$resume_id)
            {
                $query->Where(function($query) use ($id,$resume_id) {
                    $query->where('user_skills.user_id', $id);
                    $query->where('user_skills.resume_id', $resume_id);
                    $query->where('skills.is_active', '1');
                });
                $query->orWhere('user_skills.skill_id', null);
            })
            ->orderBy('skills.id', 'ASC')
            //->groupBy('skills.id')
            ->get()->toArray();

        return response()->json([
            'success' => true,
            'status' => 1,
            'data' => $UserSkillData
        ]);
    }

    public function addUserSkill(Request $request) {
        $id = ($request->session()->get('yak_user_id')) ? $request->session()->get('yak_user_id') : 0;
        $skill_name = Skills::where('id','=',$request->skill_id)->where('is_active', '=', '1')->first()->name;

        $userSkillID = DB::table('user_skills')->insertGetId(['user_id' => $id, 'resume_id' => $request->resume_id, 'skill_id' => $request->skill_id, 'custom_skill' => $skill_name, 'date_added' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        $user_rating = DB::table('position_skill')->where('skill_id', $request->skill_id)->get()->first()->user_rating;
        DB::table('position_skill')->where('skill_id',$request->skill_id)->update(['user_rating' => $user_rating + 1]);

        // Added data in resumesection 
        $resumesection = DB::table('resumesection')->where('resume_id', $request->resume_id)->get()->first();
        $resumeSection = DB::table('resumesection')->select([DB::raw('MAX(resumesection.order) AS max_order')])->get()->first();
        if(empty($resumesection)) {
            $order = !empty($resumeSection) ? $resumeSection->max_order + 1 : 1;
            $resumesectionID = DB::table('resumesection')->insertGetId(['resume_id' => $request->resume_id, 'title' => 'Skills', 'type' => 'skills', 'column' => 1, 'order' => $order, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            
            DB::table('resumesubsection')->insertGetId(['resumesection_id' => $resumesectionID, 'list_style' => 'Horizontal Bullets', 'order' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        }
        $resumesectionID = !empty($resumesection) ? $resumesection->id : $resumesectionID;
        $resumeSubsectionID = DB::table('resumesubsection')->where('resumesection_id', $resumesectionID)->get()->first()->id;
        DB::table('resume_userskills')->insertGetId(['resumesubsection_id' => $resumeSubsectionID, 'userskill_id' => $userSkillID, 'order' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        // End
        
        return response()->json([
            'success' => true,
            'status' => 1,
            'user_skill_id' => $userSkillID
        ]);
    }

    public function deleteUserSkill(Request $request) {
        $id = ($request->session()->get('yak_user_id')) ? $request->session()->get('yak_user_id') : 0;

        DB::table('user_skills')->where('resume_id', $request->resume_id)->where('skill_id', $request->skill_id)->where('id', $request->user_skill_id)->delete();

        if($request->skill_id != null) {
            $user_rating = DB::table('position_skill')->where('skill_id', $request->skill_id)->get()->first()->user_rating;
            DB::table('position_skill')->where('skill_id',$request->skill_id)->update(['user_rating' => ($user_rating != 0) ? $user_rating - 1 : 0]);
        }

        return response()->json([
            'success' => true,
            'status' => 1,
            'message' => 'User skill deleted successfully.'
        ]);
    }

    public function addCustomSkill(Request $request) {
        $id = ($request->session()->get('yak_user_id')) ? $request->session()->get('yak_user_id') : 0;

        $userSkillID = DB::table('user_skills')->insertGetId(['user_id' => $id, 'resume_id' => $request->resume_id, 'skill_id' => $request->skill_id, 'custom_skill' => $request->custom_skill, 'date_added' => date('Y-m-d'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);

        // Added data in resumesection 
        $resumesection = DB::table('resumesection')->where('resume_id', $request->resume_id)->get()->first();
        $resumeSection = DB::table('resumesection')->select([DB::raw('MAX(resumesection.order) AS max_order')])->get()->first();
        if(empty($resumesection)) {
            $order = !empty($resumeSection) ? $resumeSection->max_order + 1 : 1;
            $resumesectionID = DB::table('resumesection')->insertGetId(['resume_id' => $request->resume_id, 'title' => 'Skills', 'type' => 'skills', 'column' => 1, 'order' => $order, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            
            DB::table('resumesubsection')->insertGetId(['resumesection_id' => $resumesectionID, 'list_style' => 'Horizontal Bullets', 'order' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        }
        $resumeSubsectionID = DB::table('resumesubsection')->where('resumesection_id', $resumesectionID)->get()->first()->id;
        DB::table('resume_userskills')->insertGetId(['resumesubsection_id' => $resumeSubsectionID, 'userskill_id' => $userSkillID, 'order' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        // End

        return response()->json([
            'success' => true,
            'status' => 1,
            'user_skill_id' => $userSkillID
        ]);
    }
}