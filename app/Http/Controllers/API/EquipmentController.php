<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Equipment;
use App\Plant;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $equipments = Equipment::with('plant');

        $table="equipments";
        $perPage = $request->perPage?? 10;
        $sortColumn = $request->sortColumn?? 'id';
        $sortDir = $request->sortDir?? 'desc';
        $perPage = $request->perPage?? 10;
        $search  = $request->search? json_decode($request->search): '';

        if($request->search !== '{}') {
            $name          = property_exists($search, 'name')? $search->name: '';
            $plant_id      = property_exists($search, 'plant_id')? $search->plant_id: '';
            
            $equipments = $equipments->where('name', 'like', '%'.$name.'%');
            if(!empty($plant_id)) $equipments = $equipments->where('plant_id', '=', $plant_id);
        }


        if(Str::contains($sortColumn, '.')) {
            // make table plural as tables in laravel database are stored as plural
            $sortRelatedTable = Str::plural(Str::before($sortColumn, '.'));
            $sortRelatedTableColumn = Str::after($sortColumn, '.');
            $equipments = $equipments->select('equipments.*')
                ->join($sortRelatedTable, $table.'.'.Str::singular($sortRelatedTable).'_id', '=', $sortRelatedTable.'.id')
                ->orderBy($sortRelatedTable.'.'.$sortRelatedTableColumn, $sortDir);           
        } else { 
            $equipments = $equipments->orderBy($sortColumn, $sortDir);
        }

        $equipments = $equipments->paginate($perPage);

        return ['data' => $equipments, 'draw' => $request->draw];
    }

    public function fetchUserRelatedModels() {
        $plants = Plant::orderBy('name', 'asc')->get();

        return ['plants' => $plants];
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
            'name'  => 'required|string|max:191|unique:equipment',
            'plant_id' => 'required|numeric|exists:plants,id'
        ]);

        $equipment = Equipment::create([
            'name'      => $request->name,    
            'plant_id'  => $request->plant_id,    
        ]);

        return $equipment;
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
        $equipment = Equipment::findOrFail($id);

        $this->validate($request, [
            'name'      => 'required|string|max:191|unique:equipment,name,'.$equipment->id,
            'plant_id'  => 'required|numeric|exists:plants,id',
        ]);

        $equipment->update($request->all());
            
        return $equipment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $equipment = Equipment::find($id);
        if(!is_null($equipment)) {
            $equipment->delete();
            return ['message' => 'Equipment deleted Successfully'];
        } 
        
        return ['message' => 'Something wrong! Equipment not deleted'];
    }
}
