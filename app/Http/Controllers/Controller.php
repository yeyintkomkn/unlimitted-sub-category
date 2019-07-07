<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function insert_category(Request $request){
        $name=$request->get('name');
        $type=$request->get('type');
        $parent_id=$request->get('parent_id');
        if ($type==='main'){
            $parent_id=0;
            $level=0;
        }
        else if ($type==="sub"){
            $level=Category::findOrFail($parent_id)->level+1;
        }
        Category::create([
            'name'=>$name,
            'level'=>$level,
            'parent_id'=>$parent_id
        ]);
        return redirect('/home');
    }

    function show_home(){
        $categories=Category::all();
        return view('home')->with([
            'categories'=>$categories
        ]);
    }

    function get_categories(){
        $categories=Category::orderBy('level')->get();
        return json_encode($categories);
    }
}


