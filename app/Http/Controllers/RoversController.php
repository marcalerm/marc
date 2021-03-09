<?php

namespace App\Http\Controllers;


class RoversController extends Controller
{

    protected $positionMatrix;

    public function __construct()
    {
        $this->fillMatrix();
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

    public function moveRove($planet, $actualPosition, $commands)
    {
        $lastPosition = [];

        if( $this->checkPositionOffset($planet, $actualPosition) ){
            return ["error"=>"position out of the planet"];
        }

        foreach (str_split($commands,1) as $command) {
            $nextPosition = $this->getNextPosition( empty($lastPosition)?$actualPosition:$lastPosition, $command);
            //check obstacle before move
            if ( $this->canMove($planet, $nextPosition, $command)){ //if con move update position and continue
                $lastPosition = $nextPosition;
            }
            else{ //can't move end and return last position
                break;
            }
        }

        return empty($lastPosition)?$actualPosition:$lastPosition;
    }

    private function getNextPosition($actualPosition, $command)
    {
        $nextOperation = $this->positionMatrix[$actualPosition->direction][$command];

        if($nextOperation->coordinate == 'x'){
            $x= $actualPosition->x + $nextOperation->operation;
            $y = $actualPosition->y;
        }
        else{
            $x= $actualPosition->x;
            $y= $actualPosition->y + $nextOperation->operation;
        }

        return $nextPosition = (object) [
            'x' => $x,
            'y' => $y,
            'direction' => $nextOperation->direction,
        ];
    }

    private function canMove($planet, $position)
    {
        //out of map
        if( $position->x <= 0  || $position->y <= 0 )
            return false;

        //obstacle in front
        foreach ($planet->obstacles as $obstacle){
            if( $position->x == $obstacle['x'] && $position->y == $obstacle['y'] )
                return false;
        }

        return true;
    }

    private function checkPositionOffset($planet, $position)
    {
        if( $planet->x < $position->x  || $planet->y < $position->y )
        return true;

        return false;
    }

    private function fillMatrix()
    {
        $this->positionMatrix['N']['L'] = (object) [ 'direction' => 'E', 'coordinate' => 'x', 'operation' => -1 ];
        $this->positionMatrix['N']['R'] = (object) [ 'direction' => 'W', 'coordinate' => 'x', 'operation' => 1 ];
        $this->positionMatrix['N']['F'] = (object) [ 'direction' => 'N', 'coordinate' => 'y', 'operation' => -1 ];

        $this->positionMatrix['S']['L'] = (object) [ 'direction' => 'W', 'coordinate' => 'x', 'operation' => 1 ];
        $this->positionMatrix['S']['R'] = (object) [ 'direction' => 'E', 'coordinate' => 'x', 'operation' => -1 ];
        $this->positionMatrix['S']['F'] = (object) [ 'direction' => 'S', 'coordinate' => 'y', 'operation' => 1 ];

        $this->positionMatrix['E']['L'] = (object) [ 'direction' => 'S', 'coordinate' => 'y', 'operation' => 1 ];
        $this->positionMatrix['E']['R'] = (object) [ 'direction' => 'N', 'coordinate' => 'y', 'operation' => -1 ];
        $this->positionMatrix['E']['F'] = (object) [ 'direction' => 'E', 'coordinate' => 'x', 'operation' => -1 ];

        $this->positionMatrix['W']['L'] = (object) [ 'direction' => 'N', 'coordinate' => 'y', 'operation' => -1 ];
        $this->positionMatrix['W']['R'] = (object) [ 'direction' => 'S', 'coordinate' => 'y', 'operation' => 1 ];
        $this->positionMatrix['W']['F'] = (object) [ 'direction' => 'W', 'coordinate' => 'x', 'operation' => 1 ];
    }
}
