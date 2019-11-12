<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $table="roles";
        $perPage = $request->perPage?? 10;
        $sortColumn = $request->sortColumn?? 'id';
        $sortDir = $request->sortDir?? 'desc';
        $searchColumn = $request->searchColumn?? '*';
        $searchText = $request->searchText?? '';

        $roles = Role::where('name', 'like', '%' . $searchText . '%');
        
        $roles = $roles->orderBy($sortColumn, $sortDir);
        
        $roles = $roles->paginate($perPage);

        return ['data' => $roles, 'draw' => $request->draw];
    }

    public function fetchAll() {
        return Role::orderBy('name', 'asc')->get();
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
            'name'  => 'required|string|max:191|unique:roles',
        ]);

        $role = Role::create([
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
        $role = Role::findOrFail($id);

        $this->validate($request, [
            'name'  => 'required|string|max:191|unique:roles,name,'.$role->id,
        ]);

        $role->update($request->all());
        
        return $role;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        if(!is_null($role)) {
            $role->delete();
            return ['message' => 'Role deleted Successfully'];
        } 
        
        return ['message' => 'Something wrong! Role not deleted'];
    }
}
