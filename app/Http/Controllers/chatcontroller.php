<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class chatcontroller extends Controller
{
    public function index(Request $request)
    {

        $sernder_name = $request->input('sender_name');

        return view('chatroom')->with(compact('sernder_name'));
    }


    // public function send_message(Request $request)
    // {

    //     $data = $request->validate([
    //         'sender_id' => 'required',
    //         'message' => 'required',
    //     ]);
    //     Log::info('Dispatching MessageSent event', $data);

    //     // dd($request->all());
    //     MessageSent::dispatch($request->input('message'), $request->input('sender_id'));
    //     return ['success' => true];
    // }
}
