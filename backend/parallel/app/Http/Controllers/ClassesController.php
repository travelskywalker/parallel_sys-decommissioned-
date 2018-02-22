<?php

namespace App\Http\Controllers;

use App\Section;
use App\Classes;
use App\School;
use App\Http\Resources\Classes as ClassesResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class ClassesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_school_id = Auth::user()->school_id;

        $classes = DB::table('classes as c')
            ->select('c.id', 'c.name', 'c.notes', 'c.description', 'c.status', 'schools.name as school_name')
            ->leftJoin('schools', 'c.school_id', '=', 'schools.id')
            ->orderby('c.name','asc')
            ->when(Auth::user()->access_id != 0, function ($query) use ($user_school_id) {
                return $query->where('c.school_id', $user_school_id);
            })
            ->get();

        return view('pages.classes.classes')->with('classes', $classes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function show(Classes $classes, $id)
    {
        $class = Classes::find($id);

        $school = School::find($class->school_id);

        return view('pages.classes.class')->with(['class'=>$class, 'school'=>$school]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function edit(Classes $classes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Classes $classes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Classes  $classes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Classes $classes)
    {
        //
    }

    public function getsections($class_id){
        $sections = Section::where('classes_id', '=', $class_id)->get();

        return response()->json(['message'=>'success', 'data'=>$sections]);
    }


}