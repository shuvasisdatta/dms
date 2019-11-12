<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Locker;

class LockerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $table="lockers";
        $perPage = $request->perPage?? 10;
        $sortColumn = $request->sortColumn?? 'id';
        $sortDir = $request->sortDir?? 'desc';
        $searchColumn = $request->searchColumn?? '*';
        $searchText = $request->searchText?? '';
        
        if($searchColumn == '*') {
            $column = 'name';
            $lockers = Locker::where('name', 'like', '%' . $searchText . '%');
        } else if($searchColumn) {            
            $lockers = Locker::where($searchColumn, 'like', '%' . $searchText . '%');
        }

        $lockers = $lockers->orderBy($sortColumn, $sortDir);
        
        $lockers = $lockers->paginate($perPage);

        return ['data' => $lockers, 'draw' => $request->draw];
    }

    public function fetchAll() {
        return Locker::orderBy('name', 'asc')->get();
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
            'name'  => 'required|string|max:191|unique:lockers',
        ]);

        $locker = Locker::create([
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
        $locker = Locker::findOrFail($id);

        $this->validate($request, [
            'name'  => 'required|string|max:191|unique:lockers,name,'.$locker->id,
        ]);

        $locker->update($request->all());
        
        return $locker;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $locker = Locker::find($id);
        if(!is_null($locker)) {
            $locker->delete();
            return ['message' => 'Locker deleted Successfully'];
        } 
        
        return ['message' => 'Something wrong! Locker not deleted'];
    }
}
