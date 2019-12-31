<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Document;
use App\Department;
use App\Plant;
use App\Equipment;
use App\Category;
use App\Locker;

class DocumentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
        $documents = Document::with('department', 'plant', 'equipment', 'category', 'locker', 'user');
        
        $table      ="documents";
        $perPage    = $request->perPage?? 10;
        $sortColumn = $request->sortColumn?? 'id';
        $sortDir    = $request->sortDir?? 'desc';
        $search     = $request->search? json_decode($request->search): '';
        
        if($request->search !== '{}') {
            $title          = property_exists($search, 'title')? $search->title: '';
            $description    = property_exists($search, 'description')? $search->description: '';
            $type           = property_exists($search, 'type')? $search->type: '';
            $department_id  = property_exists($search, 'department_id')? $search->department_id: '';
            $plant_id       = property_exists($search, 'plant_id')? $search->plant_id: '';
            $equipment_id   = property_exists($search, 'equipment_id')? $search->equipment_id: '';
            $category_id    = property_exists($search, 'category_id')? $search->category_id: '';
            $locker_id      = property_exists($search, 'locker_id')? $search->locker_id: '';
            
            $documents = $documents->where('title', 'like', '%'.$title.'%')
                ->where('description', 'like', '%'.$description.'%')
                ->where('type', 'like', '%'.$type.'%');
                
            if(!empty($department_id))  $documents = $documents->where('department_id', '=', $department_id);
            if(!empty($plant_id))       $documents = $documents->where('plant_id', '=', $plant_id);
            if(!empty($equipment_id))   $documents = $documents->where('equipment_id', '=', $equipment_id);
            if(!empty($category_id))    $documents = $documents->where('category_id', '=', $category_id);
            if(!empty($locker_id))      $documents = $documents->where('locker_id', '=', $locker_id);
        }

        /*if($searchColumn == '*') {
            $column = 'name';

            $documents = $documents->where('title', 'like', '%' . $searchText . '%')
                ->orWhere('description', 'like', '%' . $searchText . '%')
                ->orWhere('type', 'like', '%' . $searchText . '%')
                ->orWhereHas('department', function($query) use ($request, $column, $searchText) {
                        $query->where($column, 'LIKE', "%". $searchText ."%");
                    })
                ->orWhereHas('category', function($query) use ($request, $column, $searchText) {
                     $query->where($column, 'LIKE', "%". $searchText ."%");
                    })
                ->orWhereHas('locker', function($query) use ($request, $column, $searchText) {
                        $query->where($column, 'LIKE', "%". $searchText ."%");
                    });
        } else if(Str::contains($searchColumn, '.')) {
            $searchRelatedTable         = Str::before($searchColumn, '.');
            $searchRelatedTableColumn   = Str::after($searchColumn, '.');
            
            $documents = $documents->WhereHas($searchRelatedTable, function($query) use ($searchRelatedTableColumn, $searchText) {
                $query->where($searchRelatedTableColumn, 'LIKE', "%". $searchText ."%");
            });
        } else if($searchColumn) {            
            $documents = $documents->where($searchColumn, 'like', '%' . $searchText . '%');
        }*/

        if(Str::contains($sortColumn, '.')) {
            // make table plural as tables in laravel database are stored as plural
            $sortRelatedTable = Str::plural(Str::before($sortColumn, '.'));
            $sortRelatedTableColumn = Str::after($sortColumn, '.');
            $documents = $documents->select('documents.*')
                ->join($sortRelatedTable, $table.'.'.Str::singular($sortRelatedTable).'_id', '=', $sortRelatedTable.'.id')
                ->orderBy($sortRelatedTable.'.'.$sortRelatedTableColumn, $sortDir);           
        } else { 
            $documents = $documents->orderBy($sortColumn, $sortDir);
        }

        $documents = $documents->paginate($perPage);

        return ['data' => $documents, 'draw' => $request->draw];
    }

    public function fetchDocumentRelatedModels() {
        $departments    = Department::orderBy('name', 'asc')->get();
        $plants         = Plant::with('equipments')->orderBy('name', 'asc')->get();
        $categories     = Category::orderBy('name', 'asc')->get();
        $lockers        = Locker::orderBy('name', 'asc')->get();

        return ['departments' => $departments, 'plants' => $plants, 'categories' => $categories, 'lockers' => $lockers];
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
            'title'         => 'required|string|max:191',
            'description'   => 'required|string',
            'department_id' => 'required|numeric|exists:departments,id',
            'plant_id'      => 'required|numeric|exists:plants,id',
            'equipment_id' => 'required|numeric|exists:equipment,id',
            'category_id'   => 'required|numeric|exists:categories,id',
            'locker_id'     => 'required|numeric|exists:lockers,id'
        ]);
        
        if($request->document) {
            $this->validate($request, [
                'document'      => 'required|file|max:102400', // Restrict maximum document size to 100 MB. You can customize it here.
            ]);
        }

        $time= round(microtime(true) * 1000); // time in miliseconds
        if($request->document) {
            $fileName = Str::before($request->document->getClientOriginalName(), '.'.$request->document->extension()) . $time . '.'.$request->document->extension(); 
            $path = $request->document->storeAs('public/documents/'. $request->plant_id . '/' . $request->equipment_id . '/' . $request->department_id . '/'. $request->category_id . '/' . $request->locker_id . '/' , $fileName);
        }
        
        $document = Document::create([
            'title'         => $request->title,    
            'description'   => $request->description,   
            'department_id' => $request->department_id,   
            'plant_id'      => $request->plant_id,   
            'equipment_id'  => $request->equipment_id,   
            'category_id'   => $request->category_id,   
            'locker_id'     => $request->locker_id,
            'user_id'       => 1, // by default it's Admin
            'type'          => $request->document? $request->document->extension(): '',
            'slug'          => $request->document? $path: $time
        ]);    

        return $document;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $document = Document::find($id);
        return $document;
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
        $document = Document::findOrFail($id);
        
        $this->validate($request, [
            'title'         => 'required|string|max:191',
            'description'   => 'required|string',
            'department_id' => 'required|numeric|exists:departments,id',
            'plant_id'      => 'required|numeric|exists:plants,id',
            'equipment_id'  => 'required|numeric|exists:equipment,id',
            'category_id'   => 'required|numeric|exists:categories,id',
            'locker_id'     => 'required|numeric|exists:lockers,id',
            // 'user_id'       => 'required|numeric|exists:users,id',
        ]);

        if($request->document) {
            $this->validate($request, [
                'document'      => 'required|file|max:102400', // Restrict maximum document size to 100 MB. You can customize it here.
            ]);
        }

        $time= round(microtime(true) * 1000); // time in miliseconds
        if($request->document) {
            $fileName = Str::before($request->document->getClientOriginalName(), '.'.$request->document->extension()) . $time . '.'.$request->document->extension(); 
            $path = $request->document->storeAs('public/documents/'. $request->plant_id . '/' . $request->equipment_id . '/' . $request->department_id . '/'. $request->category_id . '/' . $request->locker_id . '/' , $fileName);
            
            // Check a document is already there or not. If present then delete the document.
            if(!is_numeric($document->slug)) {
                // delete the file
                unlink(Str::replaceLast('public', 'storage/', public_path($document->slug)));
            }
        }
        
        $document_data = [
            'title'         => $request->title,    
            'description'   => $request->description,   
            'department_id' => $request->department_id,   
            'plant_id'      => $request->plant_id,   
            'equipment_id'  => $request->equipment_id,   
            'category_id'   => $request->category_id,   
            'locker_id'     => $request->locker_id,
            'user_id'       => 1, // by default it's Admin
            'type'          => $request->document? $request->document->extension(): $document->type,
            'slug'          => $request->document? $path: $document->slug
        ];    
    
        $document->update($document_data);  


        // update for document upload in edit mode also
        // if($request->document) {
        //     $this->validate($request, [
        //         'document'      => 'required|file|max:51200', // Restrict maximum document size to 50 MB. You can customize it here.
        //     ]);
        // }

        // $time= round(microtime(true) * 1000); // time in miliseconds
        // if($request->document) {
        //     $fileName = Str::before($request->document->getClientOriginalName(), '.'.$request->document->extension()) . $time . '.'.$request->document->extension(); 
        //     $path = $request->document->storeAs('public/documents/'. $request->plant_id . '/' . $request->equipment_id . '/' . $request->department_id . '/'. $request->category_id . '/' . $request->locker_id . '/' , $fileName);
        // }

        // $document_data = [
        //     'title'         => $request->title,    
        //     'description'   => $request->description,   
        //     'department_id' => $request->department_id,   
        //     'plant_id'      => $request->plant_id,   
        //     'equipment_id'  => $request->equipment_id,   
        //     'category_id'   => $request->category_id,   
        //     'locker_id'     => $request->locker_id,
        //     'user_id'       => 1, // by default it's Admin
        //     'type'          => $request->document? $request->document->extension(): '',
        //     'slug'          => $request->document? $path: $time
        // ];    

        // $document->update($document_data);
        // end of update for document upload in edit mode also

        // $document->update($request->all());
            
        return $document;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $document = Document::find($id);

        if(!is_null($document)) {
            // if there is any document exists then delete it first before deleted from database
            if($document->type !== '') {
                unlink(Str::replaceLast('public', 'storage/', public_path($document->slug)));
                // unlink(public_path(Str::after($document->slug, url('/'))));
                // Storage::delete('public/'. Str::after($document->slug, url('/storage')));
            }

            $document->delete();
            return ['message' => 'Document deleted Successfully'];
        } 
        
        return ['message' => 'Something wrong! Document not deleted'];
    }
}
