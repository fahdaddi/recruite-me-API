<?php

namespace App\Http\Controllers;

use App\User;
use App\Job;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{


    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobs = Job::paginate(env('PER_PAGE' , 15));

        return response()->json($jobs);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'client_id' => ['required', 'exists:clients,id'],
            'due_date' => ['nullable', 'date', 'after:tomorrow'],
            'title' => ['required', 'string', 'min:10', 'max:255'],
            'description' => ['required', 'json'],
            'salary' => ['nullable', 'integer'],
            'contract_type' => ['required', Rule::in(['CDI', 'CDD', 'REMOTE', 'PART_TIME'])],
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ],422);
        }

        $job = Job::create([
            'client_id' => $request->client_id,
            'due_date' => $request->due_date,
            'title' => $request->title,
            'description' => $request->description,
            'salary' => $request->salary,
            'contract_type' => $request->contract_type,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'job created successfully!',
            'job' => $job
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function show(Job $job)
    {
        $job->load('client');

        return response()->json([
            'job' => $job
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Job $job)
    {
        $job->load('client');
        
        $user = User::where('id',$job->client->user_id)->first();

        if(auth()->user()['id'] != $user->id){
            return response()->json([
                'status' => 'failed',
                'message' => 'you don\'t have the permision to update the job'
            ],403);
        }

        
        $validator = Validator::make($request->all(),[
            'due_date' => ['nullable', 'date', 'after:tomorrow'],
            'title' => ['required', 'string', 'min:10', 'max:255'],
            'description' => ['required', 'json'],
            'salary' => ['nullable', 'integer'],
            'contract_type' => ['required', Rule::in(['CDI', 'CDD', 'REMOTE', 'PART_TIME'])],
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ],422);
        }


        $job->due_date = $request->due_date;
        $job->title = $request->title;
        $job->description = $request->description;
        $job->salary = $request->salary;
        $job->contract_type = $request->contract_type;

        $job->save();

        return response()->json([
            'status' => 'success',
            'message' => 'job updated successfully!',
            'job' => Job::where('id', $job->id )->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        Job::destroy($job->id);

        return response()->json([
            'status' => 'success',
            'message' => 'job deleted successfully!'
        ]);
    }
}
