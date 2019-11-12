<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Department;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $table="departments";
        $perPage = $request->perPage?? 10;
        $sortColumn = $request->sortColumn?? 'id';
        $sortDir = $request->sortDir?? 'desc';
        $searchColumn = $request->searchColumn?? '*';
        $searchText = $request->searchText?? '';

        $departments = Department::where('name', 'like', '%' . $searchText . '%');

        $departments = $departments->orderBy($sortColumn, $sortDir);
        
        $departments = $departments->paginate($perPage);

        return ['data' => $departments, 'draw' => $request->draw];
    }

    public function fetchAll() {
        return Department::orderBy('name', 'asc')->get();
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
            'name'  => 'required|string|max:191|unique:departments',
        ]);

        $department = Department::create([
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
        $department = Department::findOrFail($id);

        $this->validate($request, [
            'name'  => 'required|string|max:191|unique:departments,name,'.$department->id,
        ]);

        $department->update($request->all());
        
        return $department;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $department = Department::find($id);
        if(!is_null($department)) {
            $department->delete();
            return ['message' => 'Department deleted Successfully'];
        } 
        
        return ['message' => 'Something wrong! Department not deleted'];
    }
}
