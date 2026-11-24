<?php

namespace App\Http\Controllers;

use Request;
use DateTime;
use App\User as EMT;


use App\Http\ORM\Comment;
use App\Responses\Response;

class UpdateCommentController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function store()
    {


        date_default_timezone_set("Asia/Beirut");

        $newContent = Request::get('form');
        $date = Request::get('date');

        if(is_null($newContent) || $newContent == ""){
            $response = New Response;
            $response->Status = 1;
            return json_encode($response);
        }else{

            $comment= comment::where('date', $date)->first();
            if(is_null($comment)){
                $comm = new comment;
                $comm->content = $newContent;
                $comm->date = $date;
                $comm->save();
            }else{
                $comment->content = $comment->content . "\n" . $newContent;
                $comment->save();
            }


        }


        $response = New Response;
        $response->Status = 1;
        return json_encode($response);
    }

    public function to_from()
    {


        date_default_timezone_set("Asia/Beirut");

        $to = Request::get('to');
        $from = Request::get('from');
        $date = Request::get('date');

        if(is_null($from) || $from == "" || is_null($to) || $to == "" ||is_null($date) || $date == ""){
            $response = New Response;
            $response->Status = 0;
            return json_encode($response);
        }else{

            $comment= comment::where('date', $date)->first();
            if(is_null($comment)){
                $comm = new comment;
                $comm->from = $from;
                $comm->to = $to;
                $comm->date = $date;
                $comm->save();
            }else{
                $comment->from = $from;
                $comment->to = $to;
                $comment->save();
            }


        }


        $response = New Response;
        $response->Status = 1;
        $from = EMT::where('id',$from)->first();
        if(!is_null($from)){
            if($from->nickname == ""){
                $response->from = $from->name;
            }else{
                $response->from = $from->nickname . "(" . $from->name . ")";
            }
        }else{
            $response->from = "";
        }
        return json_encode($response);
    }


}
