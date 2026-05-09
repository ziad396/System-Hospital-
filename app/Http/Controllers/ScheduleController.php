<?php

namespace App\Http\Controllers;


use App\Models\Appiontment;
use App\Models\Schedule;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // $doctor=Auth::user()->doctor;
        // $user=User::find(1)->doctor;
        // return response()->json([
        //     'schedule'=>$user,
        // ]);
         $schedule=Schedule::where('doctor_id',Auth::user()->doctor->id)->get();
// $time=$schedule->toArray();
// print_r($time);
// $day=[];    
//     for ($i=0; $i < count($time); $i++) { 
//         # code...
//         $day[]=$time[$i]['day'];
        
//     }
foreach ($schedule as $item) {
   
}
    }
public function scheduleValidate($id_doctor)
{

    $schedules = Schedule::where('doctor_id', $id_doctor)->get();

    if ($schedules->isEmpty()) {
        return response()->json([
            'message' => 'schedule not found'
        ], 404);
    }

    $result = [];

    foreach ($schedules as $schedule) {

        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);

        $times = [];

        while ($start < $end) {
            $times[] = $start->format('H:i');
            $start->addMinutes(30);
        }

     
       
        $appointments = Appiontment::where('doctor_id', $id_doctor)
            ->where('day', $schedule->day)
            ->get();
            if ($appointments->isEmpty()) {
               return response()->json([
                'message' => 'no appointments for this day',
                
               ]);
            }
            foreach ($appointments as $book) {
            $book_new=Carbon::parse($book->time)->format('H:i');
            // $bookTime = Carbon::parse($book->time)->format('H:i');
            if(in_array($book_new, $times)){
                $times = array_diff($times, [$book_new]);   

            }
            
        }

       
        $result[$schedule->day] = array_values($times);
    }

    return response()->json([
        'schedule' => $result
    ]);
}
   
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = Validator::make($request->all(), [
            'day' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
        if ($validated->fails()) {
            return response()->json([
                'errors' => $validated->errors(),
            ], 422);
        }
        $doctor=Auth::user()->doctor;
        $check=Schedule::where('doctor_id',$doctor->id)->where('day',$request->day)->first();
        if($check){
            return response()->json([
                'message'=>'you have already schedule for this day'
            ],400);
        }
        $schedule=Schedule::create([
            'day'=>$request->day,
            'start_time'=>$request->start_time,
            'end_time'=>$request->end_time,
            'doctor_id'=>$doctor->id,
        ]);
        return response()->json([
            "message" => "Schedule created successfully",
            "schedule" => $schedule
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validated=Validator::make($request->all(), [
            'day' => 'string',
            'start_time' => 'date_format:H:i',
            'end_time' => 'date_format:H:i|after:start_time',
        ]); 
        if ($validated->fails()) {
            # code...
            return response()->json([
                'errors'=>$validated->errors(),
            ],422);
        }
        $schedule=Schedule::find($id);
        if(!$schedule){
            return response()->json([
                'message'=>'schedule not found'     
            ],404);
        }
        $schedule->day=$request->day;
        $schedule->start_time=$request->start_time;
        $schedule->end_time=$request->end_time;
        $schedule->save();
        return response()->json([
            "message"=>"updated success"
        ]);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $schedule=Schedule::find($id);  
        if(!$schedule){
            return response()->json([
                'message'=>'schedule not found'     
            ],404);
        }
        $schedule->delete();
        return response()->json([       
            "message"=>"deleted success"
        ]);
    }
}
