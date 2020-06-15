<?php

namespace App\Http\Controllers;

use App\Job;
use App\Postulation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PostulationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->client_id) {
            $postulations = Postulation::where('client_id', $request->client_id)->paginate(env('PER_PAGE', 15));
        } else if ($request->job_id) {
            $postulations = Postulation::where('job_id', $request->job_id)->paginate(env('PER_PAGE', 15));
        } else {
            $postulations = Postulation::where('user_id', auth()->user()['id'])->with('client')->paginate(env('PER_PAGE', 15));
        }

        return response()->json([
            'postulations' => $postulations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resume_id' => ['required', 'exists:resumes,id'],
            'client_id' => [Rule::requiredIf(!$request->job_id), 'exists:clients,id'],
            'job_id' => [Rule::requiredIf(!$request->client_id), 'exists:jobs,id'],
            'cover_letter' => ['required', 'json']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $client_id = null;
        if($request->client_id){
            $client_id = $request->client_id;
        }else {
            $job = Job::where('id', $request->job_id)->first();
            $client_id = strval($job->client_id);

        }
        $postulation = Postulation::create([
            'user_id' => auth()->user()['id'],
            'resume_id' => $request->resume_id,
            'cover_letter' => $request->cover_letter,
            'job_id' => $request->job_id,
            'client_id' => $client_id
        ]);

        return response()->json([
            'status' => "success",
            'message' => "your postulation was added successfully",
            'postulation' => $postulation
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Postulation  $postulation
     * @return \Illuminate\Http\Response
     */
    public function show(Postulation $postulation)
    {
        $postulation->load('client');
        $postulation->load('job');
        return response()->json([
            'postulation' => $postulation
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Postulation  $postulation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Postulation $postulation)
    {
        $validator = Validator::make($request->all(), [
            'cover_letter' => ['required', 'json']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $postulation->cover_letter = $request->cover_letter;
        $postulation->save();


        return response()->json([
            'status' => "success",
            'message' => "your postulation was updated successfully",
            'postulation' => $postulation
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Postulation  $postulation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Postulation $postulation)
    {
        Postulation::destroy($postulation->id);

        return response()->json([
            'status' => 'success',
            'message' => "your postulation was deleted successfully"
        ]);
    }
}
