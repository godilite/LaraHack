<?php

namespace App\Http\Controllers;

use App\Hacker;
use App\Http\Requests\CheckCodeRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\SetDecisionRequest;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class HackerController extends Controller
{


    /**
     * Show the registration page
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('register');
    }

    /**
     * Store the hacker
     *
     * @param RegistrationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegistrationRequest $request)
    {
        try {
            $hacker = new Hacker();
            $hacker->first_name = $request->request->get('first_name');
            $hacker->last_name = $request->request->get('last_name');
            $hacker->email = $request->request->get('email');
            $hacker->sex = $request->request->get('sex');
            $hacker->birthday = $request->request->get('birthday');
            $hacker->phone_number = $request->request->get('phone');
            $hacker->motivation = $request->request->get('motivation');
            $hacker->github = $request->request->get('github');
            $hacker->linked_in = $request->request->get('linked_in');
            $hacker->skills = $request->request->get('skills');
            $hacker->size = $request->request->get('size');

            if ($request->request->get('team_name') != '') { //If the request contains a team's name , so the hacker want to create a team

                $team = new Team();
                $team->name = $request->request->get('team_name');
                $team->code = str_random();
                $team->save();
                $team->hackers()->save($hacker);
                return response()->json(['success' => 'Inscription done', 'code' => $team->code, 'name' => $team->name]);

            }

            elseif ($request->request->get('team_id') != '') {//If the request contains a team's id , so the hacker want to join a team

                $team_id = $request->request->get('team_id');
                $team = Team::find($team_id);
                $team->hackers()->save($hacker);
                return response()->json(['success' => 'Inscription done', 'code' => $team->code, 'name' => $team->name]);

            }

            else {// Else, the hacker have no team

                $hacker->save();
                return response()->json(['success' => 'Inscription done']);
            }

        }

        catch (\Exception $exception) {

            return response()->json(['success' => 'Try again :/ !' . $exception->getMessage()]);// For debug only , You should remove the get message before production
        }

    }

    /**
     * @param Request $request
     * Check if the code is correct , returning the id and the name if correct
     * @return false|string
     */
    public function checkCode(CheckCodeRequest $request)
    {
        $code = $request->request->get('teamCode');
        $team = DB::table('teams')->where('code', '=', $code)->first();
        if ($team != null) return json_encode(['id' => $team->id, 'team_name' => $team->name]);
        else return json_encode(['error' => 'your code is not valid']);
    }




}