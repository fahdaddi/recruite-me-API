<?php

namespace App\Http\Controllers;

use App\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ResumeController extends Controller
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
            $resumes = Resume::where('client_id', $request->client_id )->paginate(env('PER_PAGE' , 15));
        }else{
            $resumes = Resume::where('user_id', auth()->user()['id'] )->paginate(env('PER_PAGE' , 15));
        }

        return response()->json([
            'resumes' => $resumes
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
            'file' => ['required', 'file', 'between:10,250000', 'mimes:pdf'],
            'client_id' => ['nullable', 'exists:clients,id']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $path = $request->file->store('/public/resumes');

        $attach = new Resume();

        $attach->name = $request->file->getClientOriginalName();
        $attach->size = $request->file->getSize();
        $attach->url = $path;

        $attach->user_id = auth()->user()['id'];
        $attach->client_id = $request->client_id;

        $attach->save();

        return response()->json([
            'success' => true,
            'message' => 'your resume has been uploaded successfuly',
            'resume' => $attach
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Resume $resume)
    {
        if ($request->user_id) {
            $resume->load('user');
        }
        return response()->json([
            'resume' => $resume
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Resume $resume)
    {

        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'between:10,250000', 'mimes:pdf']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 409);
        }

        Storage::delete($resume->url['path']);
        $path = $request->file->store('/public/resumes');

        $resume->url = $path;
        $resume->name = $request->file->getClientOriginalName();
        $resume->size = $request->file->getSize();

        $resume->save();

        return response()->json([
            'success' => true,
            'message' => 'your resume has been updated successfully',
            'resume' => $resume
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Resume  $resume
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resume $resume)
    {
        Storage::delete($resume->url['path']);
        Resume::destroy($resume->id);

        return response()->json([
            'success' => true,
            'message' => 'Resume deleted successfully'
        ], 200);
    }
}
