<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plant;

class PlantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $table="plants";
        $perPage = $request->perPage?? 10;
        $sortColumn = $request->sortColumn?? 'id';
        $sortDir = $request->sortDir?? 'desc';
        $searchColumn = $request->searchColumn?? '*';
        $searchText = $request->searchText?? '';

        $plants = Plant::where('name', 'like', '%' . $searchText . '%');

        $plants = $plants->orderBy($sortColumn, $sortDir);
        
        $plants = $plants->paginate($perPage);

        return ['data' => $plants, 'draw' => $request->draw];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|string|max:191|unique:plants',
        ]);

        $plant = Plant::create([
            'name'  => $request->name,    
        ]);  
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);

        $this->validate($request, [
            'name'  => 'required|string|max:191|unique:departments,name,'.$plant->id,
        ]);

        $plant->update($request->all());
        
        return $plant;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plant = Plant::find($id);
        if(!is_null($plant)) {
            $plant->delete();
            return ['message' => 'Plant deleted Successfully'];
        } 
        
        return ['message' => 'Something wrong! Plant not deleted'];
    }
}
