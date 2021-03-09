<?php

namespace App\Http\Controllers;

use App\Models\Planet;

class PlanetsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
            $data = [
                "project" => $project = new Project(),
                "edit" => false,
                "project_marketing_type" => ['individual'=>'Individual','global'=>'Global'],
                "default_provider" => '',
                "project_type" => $type,
                "company_id"=> BusinessLineCompany::where("business_line_id", MARKETINGS_BUSINESS_LINE_ID)->first()->company_id,
                "venedors" => User::all()->pluck("name","id"),
                "operators" => User::active()->get()->pluck("nick","id")
            ];
            return view("upsertPlanet.blade.php", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if($project = Planet::create(request()->all())){
            return redirect("/planets")->with('success', 'Planet Created');

        }
        else{
            return redirect()->back()->with('error', trans('global.save_ko'));
        }
    }

}
