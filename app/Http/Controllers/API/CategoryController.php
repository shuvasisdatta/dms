<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::with('parent');
        $table="categories";
        $perPage = $request->perPage?? 10;
        $sortColumn = $request->sortColumn?? 'id';
        $sortDir = $request->sortDir?? 'desc';
        $searchColumn = $request->searchColumn?? '*';
        $searchText = $request->searchText?? '';

        if($searchColumn == '*') {
            $column = 'name';
            $categories = $categories->where('name', 'like', '%' . $searchText . '%');
        } else if($searchColumn === 'parent_id') {            
            $categories = $categories->WhereHas($table, function($query) use ($searchText) {
                $query->where('name', 'LIKE', "%". $searchText ."%");
            });
        } else if($searchColumn) {            
            $categories = $categories->where($searchColumn, 'like', '%' . $searchText . '%');
        }

        if($sortColumn == 'parent_id') {
            // make table plural as tables in laravel database are stored as plural
            $categories = $categories->whereHas('parent')->orderBy('name', $sortDir);         
        } else { 
            $categories = $categories->orderBy($sortColumn, $sortDir);
        }

        
        
        $categories = $categories->paginate($perPage);

        return ['data' => $categories, 'draw' => $request->draw];
    }

    public function fetchAll() {
        return Category::orderBy('name', 'asc')->get();
    }

    public function fetchAllParentCategories() {
        return Category::orderBy('name', 'asc')->get();
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
            'name'      => 'required|string|max:191|unique:categories',
            'parent_id' => 'nullable',
        ]);

        $category = Category::create([
            'name'      => $request->name,    
            'parent_id' => $request->parent_id,    
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
        $category = Category::findOrFail($id);

        $this->validate($request, [
            'name'      => 'required|string|max:191|unique:categories,name,'.$category->id,
            'parent_id' => 'nullable|exists:categories,id|not_in:'.$category->id,
        ],
        [
            'parent_id.not_in' => 'Parent Category can not be same as Category'
        ]
    );

        $category->update($request->all());
        
        return $category;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if(!is_null($category)) {
            $category->delete();
            return ['message' => 'Category deleted Successfully'];
        } 
        
        return ['message' => 'Something wrong! Category not deleted'];
    }
}
