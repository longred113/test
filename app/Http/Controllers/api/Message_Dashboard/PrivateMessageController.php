<?php

namespace App\Http\Controllers\api\Message_Dashboard;

use App\Events\PrivateMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Pusher\Pusher;

class PrivateMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($studentId)
    {
        // $newAppId = 1567865;
        // $newAppKey = "3fcd7920ae1ad4d51c58";
        // $newAppSecret = "1a1ac3cf49fe66975dde";

        // $newPusher = new Pusher($newAppKey, $newAppSecret, $newAppId, [
        //     'cluster' => 'ap1',
        //     'useTLS' => true
        // ]);

        // $channelName = 'private-user-' . $studentId; // replace with the user's ID
        // $newPusher->trigger($channelName, 'client-announcement', [
        //     'message' => 'New announcement for you!'
        // ]);
        event(new PrivateMessage('New announcement for you!'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
