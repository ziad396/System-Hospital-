<?php

namespace App\Http\Controllers;
use App\Models\Appiontment;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Notifications\NewAppointmentNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
class BookConroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $id_doctor=Auth::user()->doctor->id;
    //    $request->day='saturday';
    // echo $id_doctor;
        //  $schedule=Appiontment::where('doctor_id',$id_doctor)->where('day','Monday')->get();
        //     $schedule_doctor=Schedule::where('doctor_id',$id_doctor)->get();
        //     $day=[];
        //     echo $schedule_doctor;
          $book_user=Appiontment::where('doctor_id',$id_doctor)->where('day','Monday')->get();
    // if($book_user){
 
    // // }
    // // dd($book_user);
    // foreach($book_user as $item){
    // if($item->time=='13:00'){
    //     return response()->json([
    //         'message'=>'this time is booking',
    //     ],422);
    // }
    // } 
    $doctor=Doctor::find(2);
    echo $doctor;
    // $doctor->user->notify(new NewAppointmentNotification('hello in my website'));
        //  foreach($schedule_doctor as $item){
        //         $day[]=$item->start_time; 
        //  }
        //    print_r($day);
        // dd($schedule);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


public function store(Request $request, $id_doctor)
{
    //  Validation
    $validated = Validator::make($request->all(), [
        'time' => 'required|date_format:H:i',
        'day'  => 'required|string',
    ]);

    if ($validated->fails()) {
        return response()->json([
            'errors' => $validated->errors(),
        ], 422);
    }

    //  Check doctor schedule for this day
    $schedule = Schedule::where('doctor_id', $id_doctor)
        ->where('day', $request->day)
        ->first();

    if (!$schedule) {
        return response()->json([
            'message' => 'this day is not available',
        ], 422);
    }

    //  Convert time using Carbon
    $requestTime = Carbon::createFromFormat('H:i', $request->time);
    $startTime   = Carbon::createFromFormat('H:i:s', $schedule->start_time);
    $endTime     = Carbon::createFromFormat('H:i:s', $schedule->end_time);
    $minutes = Carbon::createFromFormat('H:i', $request->time)->format('i');

if (!in_array($minutes, ['00', '30'])) {
    return response()->json([
        'message' => 'time must be in 30-minute intervals (e.g. 10:00 or 10:30)',
    ], 422);
}
    //  Check time within range
    if ($requestTime->lt($startTime) || $requestTime->gt($endTime)) {
        return response()->json([
            'message' => 'this time is not available',
        ], 422);
    }

    //  Check if time already booked (بدون loop)
    $isBooked = Appiontment::where('doctor_id', $id_doctor)
        ->where('day', $request->day)
        ->where('time', $request->time)
        ->exists();

    if ($isBooked) {
        return response()->json([
            'message' => 'this time is booking',
        ], 422);
    }

    // Check authenticated user
    $user = Auth::user();
    if (!$user) {
        return response()->json([
            'message' => 'unauthorized',
        ], 401);
    }

    //  Create booking
    $book = Appiontment::create([
        'time'      => $request->time,
        'day'       => $request->day,
        'doctor_id' => $id_doctor,
        'user_id'   => $user->id,
    ]);
    $doctor=Doctor::find($id_doctor);
    $doctor->user->notify(new NewAppointmentNotification($book));


    return response()->json([
        'message' => 'book created successfully',
        'data'    => $book,
        
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
        $book=Appiontment::find($id);
        if(!$book){
            return response()->json([
                'message'=>'book not found',
            ],404);
        }
        $schedule = Schedule::where('doctor_id', $book->doctor_id)
        ->where('day', $request->day)->first();
        if(!$schedule){
            return response()->json([
                'message'=>'this day is not available',
            ],422);
        }
            $requestTime = Carbon::createFromFormat('H:i', $request->time); 
            $startTime = Carbon::createFromFormat('H:i:s', $schedule->start_time);
            $endTime = Carbon::createFromFormat('H:i:s', $schedule->end_time);
            $minutes = Carbon::createFromFormat('H:i', $request->time)->format('i');
if (!in_array($minutes, ['00', '30'])) {
    return response()->json([
        'message' => 'time must be in 30-minute intervals (e.g. 10:00 or 10:30)',
    ], 422);
}
            if ($requestTime->lt($startTime) || $requestTime->gt($endTime)) {
                return response()->json([
                    'message' => 'this time is not available',
                ], 422);
            }
        $isBooked = Appiontment::where('doctor_id', $book->doctor_id)->first();
        if($isBooked){
            return response()->json([
                'message'=>'this time is booking',
            ],422);
        }
        
        $book->update([
            'time'=>$request->time,
            'day'=>$request->day,
        ]);
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
            //
        $book=Appiontment::find($id);
        if(!$book){
            return response()->json([
                'message'=>'book not found',
            ],404);
        }
        $book->delete();
        return response()->json([
            'message'=>'book deleted successfully',
        ],200);
    }
}
