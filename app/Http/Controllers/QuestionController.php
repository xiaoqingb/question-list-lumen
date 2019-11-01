<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getAllQuestions(Request $request){
        $page=$request->input('page','1');
        $validator = Validator::make($request->all(), [
            'page' => ['numeric'],
        ],[
            'page.numeric'=>'页码必须为数字',

        ]);
        if ($validator->fails()){
            return[
                'code'=>'0001',
                'msg'=>$validator->errors()->first()
            ];
        }
        $pageSize=5;
        $total=DB::table('question-list')->count();
        $pageCount=ceil($total/$pageSize);

        $questions=DB::table('question-list')->skip($pageSize*($page-1))->take($pageSize)->select('id','content as question','user_id as userName','time')->get();
        foreach ($questions as &$question){
            $userId=$question->userName;
            if ($userId===0){
                $question->userName='匿名';
                continue;
            }
            $question->userName=DB::table('users')->where('id',$userId)->first()->name;
        }

        return[
            'code'=>'0000',
            'msg'=>'获取问题列表成功',
            'data'=>[
                'pageCount'=>$pageCount,
                'questions'=>$questions
            ]
        ];
    }
    //
    public function createQuestion(Request $request){
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'isAnonymous'=>'required'
        ],[
            'page.numeric'=>'页码必须为数字',
            'isAnonymous.required'=>'匿名变量只接受布尔值'
        ]);
        if ($validator->fails()){
            return[
                'code'=>'0001',
                'msg'=>$validator->errors()->first()
            ];
        }
        $question=$request->input('question');
        $isAnonymous=$request->input('isAnonymous');
        if ($isAnonymous===false){
            $userId=$_SESSION['id'];
        }else{
            $userId=0;
        }
        $result=DB::table('question-list')->insert(
            [
                'content' => $question,
                'user_id' => $userId,
                'time'=>time(),
            ]
        );
        if (!$result){
            return[
                'code'=>'0001',
                'msg'=>'添加问题失败',
            ];
        }
        return[
            'code'=>'0000',
            'msg'=>'添加问题成功',
        ];
    }

    public function deleteQuestion(Request $request){
        $validator = Validator::make($request->all(), [
            'questionId' => 'numeric',
        ],[
            'questionId.numeric'=>'问题ID必须为数字',
        ]);
        if ($validator->fails()){
            return[
                'code'=>'0001',
                'msg'=>$validator->errors()->first()
            ];
        }
        $result=DB::table('question-list')->where('id', $request->input('questionId'))->delete();
        if (!$result){
            return[
                'code'=>'0001',
                'msg'=>'删除问题时发生了未知的错误'
            ];
        }
        return[
            'code'=>'0000',
            'msg'=>'删除成功'
        ];
    }

}
