<?php

namespace App\Http\Controllers;

use App\Client;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','store', 'show']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Clients = Client::paginate(env('PER_PAGE' , 15));

        return response()->json($Clients);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // register user first

        $data = $request->all();

        $validator = Validator::make($data,[
            'name' => ['required', 'max:255'],
            'email' => ['required', 'unique:users,email', 'email:rfc,dns'],
            'password' => ['required', 'confirmed', 'min:8'],
            'avatar' => ['nullable', 'file', 'mimes:jpeg,bmp,png','between:100,250000'],
            'client_name' => ['required', 'max:255', 'unique:clients,name'],
            'description' => ['nullable','json'],
            'slug' => ['required', 'alpha'],
            'logo' => ['nullable', 'file','mimes:jpeg,bmp,png','between:0,250000'],
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ],422);
        }

        
        $path = null;
        if($request->avatar){
            $path = $request->avatar->store('/public/users/avatar');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'avatar' => $path,
        ]);

        
        $path = null;
        if($request->logo){
            $path = $request->logo->store('/public/clients');
        }


        Client::create([
            'user_id' => $user->id,
            'name' => $data['client_name'],
            'description' => $data['description'],
            'slug' => $data['slug'],
            'logo' =>  $path
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'client created succesfully!'
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        return response()->json([
            'client' => $client
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        $data = $request->all();

        $validator = Validator::make($data,[
            'description' => ['nullable','json'],
            'slug' => ['nullable', 'alpha'],
            'logo' => ['nullable', 'file','mimes:jpeg,bmp,png','between:0,250000'],
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ],422);
        }

        
        $path = null;
        if($request->logo){
            Storage::delete($client->logo['path']);
            $path = $request->logo->store('/public/clients');
            $client->logo = $path;
        }
        if($request->client_name){
            $client->name = $request->client_name;
        }
        if($request->description){
            $client->description = $request->description;
        }

        if($request->slug){
            $client->slug = $request->slug;
        }

        $client->save();
        
 
        return response()->json([
            'status' => 'success',
            'message' => 'client updated succesfully!'
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        Storage::delete($client->logo['path']);
        Client::destroy($client->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Client deleted successfully!'
        ]);
    }


     /**
     * Display the specified client by user.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    // public function me(Request $request)
    // {
    //     auth()->user()->id

    //     return response()->json([
    //         'client' => $client
    //     ]);
    // }
}
