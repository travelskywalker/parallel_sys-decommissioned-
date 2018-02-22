<?php

namespace App\Http\Controllers;


use App\User;
use App\Access;
use App\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // $user_school_id = Auth::user()->school_id;
        $users = DB::table('users')
            ->select('users.name', 'accesses.name as access_name', 'schools.name as school_name', 'users.id', 'users.email')
            ->leftJoin('accesses', 'users.access_id', '=', 'accesses.id')
            ->leftJoin('schools', 'users.school_id', '=', 'schools.id')
            ->orderBy('users.name', 'asc')
            // ->when(Auth::user()->access_id != 0, function ($query) use ($user_school_id) {
            //     return $query->where('users.school_id', $user_school_id);
            // })
            ->get();


        // $users = DB::table('users')->get();
        return view('pages.user.users')->with(['users'=>$users]);
    }

    public function adduserview(){

        $user_school_id = Auth::user()->school_id;

        $accesses = Access::when(Auth::user()->access_id != 0, function ($query) use ($user_school_id) {
                        return $query->where('accesses.id', '>=', $user_school_id);
                    })->get();

        $schools = School::when(Auth::user()->access_id != 0, function ($query) use ($user_school_id) {
                            return $query->where('schools.id', $user_school_id);
                        })->get();

        return view('pages.user.adduser')->with(['schools'=>$schools, 'accesses'=>$accesses]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        /*validate input*/
        if($request->access_id != 0 && $request->access_id != null){
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|unique:users|email',
                'access_id' =>'required',
                'school_id' => 'required',
            ]);
        }else{

            if($request->school_id){
                $request->merge(array('school_id' => null));
            }

            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|unique:users|email',
                'access_id' =>'required'
            ]);
        }

        $request->merge(array('password' => bcrypt($request->password)));

        $user = new User($request->all());
        
        if($user->save()){
         return response()->json(['message'=>'User has been added', 'user'=>$user]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\c  $c
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\c  $c
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $access = Access::find($user->access_id);
        $school = School::find($user->school_id);

        $user_school_id = Auth::user()->school_id;

        $accessData = Access::when(Auth::user()->access_id != 0, function ($query) use ($user_school_id) {
                        return $query->where('accesses.id', '>=', $user_school_id);
                    })->get();

        $schoolData = School::when(Auth::user()->access_id != 0, function ($query) use ($user_school_id) {
                            return $query->where('schools.id', $user_school_id);
                        })->get();

        return view('pages.user.edit')->with(['user'=>$user, 'access'=> $access, 'school' => $school, 'accesses' => $accessData, 'schools' => $schoolData]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\c  $c
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->access_id != 0 && $request->access_id != null){
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email',
                'access_id' =>'required',
                'school_id' => 'required',
            ]);
        }else{

            if($request->school_id){
                $request->merge(array('school_id' => null));
            }

            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email',
                'access_id' =>'required'
            ]);
        }

        $user = User::find($id);

        $request->merge(array('password' => bcrypt($request->password)));
        
        if($user->update($request->all())){
         return response()->json(['message'=>'User has been update', 'user'=>$user]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\c  $c
     * @return \Illuminate\Http\Response
     */
    public function destroy(c $c)
    {
        //
    }

    public function logout(){
        auth()->logout();
        return redirect('/home');
        
    }
}