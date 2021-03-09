<?php

namespace App\Http\Controllers;

use App\Models\Planet;
use App\Models\Rover;
use Illuminate\Http\Request;


class RunsController extends RoversController
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $data = [
                "planets" => Planet::all()->pluck('full_name','id'),
                "rovers" => Rover::all()->pluck('name','id'),
                "orientations" => ['N'=>'Nord','S'=>'South','E'=>'East','W'=>'West']
            ];

            return view("run", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function run(Request $request)
    {
        $initialPosition = (object) [
            'x' => $request->input('xPosition'),
            'y' => $request->input('yPosition'),
            'direction' => $request->input('orientation'),
        ];
        $planet = Planet::find($request->input('planet'));
        $rove = new RoversController();
        $finalPosition = $rove->moveRove($planet, $initialPosition, $request->input('commands') );

        $data = [
            "command" => $request->input('commands'),
            "planet" => $planet,
            "initialPosition" => $initialPosition,
            "finalPosition" => $finalPosition
        ];
        return view("result", $data);

    }

}
