<?php

namespace App\Http\Controllers;
use App\Models\Doctor;
use App\Models\Specialziation;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SplDoublyLinkedList;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
      $query=Doctor::query();
      if (request()->has('name')) {
        $query->where('name', 'like', '%' . request()->get('name') . '%');
      }
      if (request()->has('specialization')) {
        $query->where('specialization_id', request()->get('specialization'));
      }
      if (request()->has('phone')) {
        $query->where('phone', 'like', '%' . request()->get('phone') . '%');
      }
    //   $doctor=Doctor::paginate(10);
      $doctor=$query->all();
       return response()->json([
        'user'=>$doctor,
       ]);
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
    public function store(Request $request)
    {
        //
     
       $validated=Validator::make($request->all(),[
            'name'=>'required|string|max:255',
       'specialization'=>'required',
            'phone'=>'string',
           
        ]);
      
           
        if($validated->fails()){
            return response()->json([
                'errors'=>$validated->errors(),
            ]);
        }
       
        $user_id=Auth::user()->id;
        $doctror=Doctor::where('user_id',$user_id)->first();
        if($doctror){
            return response()->json([
                'message'=>'you have already doctor specialization'
            ]);
        }
        $specization=Specialziation::find($request->specialization);
        if(!$specization){
            return response()->json([
                'message'=>'specialization not found'
            ]);
        }
        $data=Doctor::create([
            'name'=>$request->name,
            'specialization_id'=>$request->specialization,
            'phone'=>$request->phone,
            'user_id'=>$user_id
        ]);

        return response()->json([
            'message'=>'inserted success',
            'doctor'=>$data
        ]);
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
    $validated = Validator::make($request->all(), [
        'name' => 'nullable|string|max:255',
        'specialization' => 'nullable|integer|exists:specializations,id',
        'phone' => 'nullable|string',
    ]);

    if ($validated->fails()) {
        return response()->json([
            'errors' => $validated->errors(),
        ]);
    }

    $doctor = Doctor::find($id);

    if (!$doctor) {
        return response()->json([
            'message' => 'doctor not found'
        ]);
    }

    
    $data = [];

    if ($request->has('name')) {
        $data['name'] = $request->name;
    }

    if ($request->has('specialization')) {
        $data['specialization_id'] = $request->specialization;
    }

    if ($request->has('phone')) {
        $data['phone'] = $request->phone;
    }

    $doctor->update($data);

    return response()->json([
        'message' => 'updated success',
        'doctor' => $doctor
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $doctor=Doctor::find($id);
        $doctor->delete();
        return response()->json([
            'message'=>'success'
        ]);
    }
}
