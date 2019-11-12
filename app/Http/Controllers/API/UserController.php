<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\User;
use App\Department;
use App\Role;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $users = User::with('department', 'role');

        $table      ="users";
        $perPage    = $request->perPage?? 10;
        $sortColumn = $request->sortColumn?? 'id';
        $sortDir    = $request->sortDir?? 'desc';
        $search     = $request->search? json_decode($request->search): '';

        if($request->search !== '{}') {
            $name           = property_exists($search, 'name')? $search->name: '';
            $email          = property_exists($search, 'email')? $search->email: '';
            $department_id  = property_exists($search, 'department_id')? $search->department_id: '';
            $role_id        = property_exists($search, 'role_id')? $search->role_id: '';
            
            $users = $users->where('name', 'like', '%'.$name.'%')
                ->where('email', 'like', '%'.$email.'%');
                
            if(!empty($department_id))  $users = $users->where('department_id', '=', $department_id);
            if(!empty($role_id))        $users = $users->where('role_id', '=', $role_id);
        }

        $users = $users->orderBy($sortColumn, $sortDir);

        $users = $users->paginate($perPage);

        return ['data' => $users, 'draw' => $request->draw];
    }

     public function fetchUserRelatedModels() {
        $departments    = Department::orderBy('name', 'asc')->get();
        $roles          = Role::orderBy('name', 'asc')->get();

        return ['departments' => $departments, 'roles' => $roles];
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
            'remember_me' => 'boolean'
        ]);
        
        $credentials = request(['email', 'password']);
        
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken($request->email);
        $token = $tokenResult->token;
        
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        
        $token->save();
        
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'user' => $user->load('role:id,name'),
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
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
            'name'              => 'required|string|max:191',
            'email'             => 'required|email|unique:users',
            'password'          => 'required|string|min:6',
            'role_id'           => 'required|numeric|exists:roles,id',
            'department_id'     => 'required|numeric|exists:departments,id',
        ]);
        
        $user = User::create([
            'name'          => $request->name,    
            'department_id' => $request->department_id,    
            'role_id'       => $request->role_id,    
            'email'         => $request->email,    
            'password'      => Hash::make($request->password),    
        ]);    

        return $user;
    }

    public function fetchAll() {
        return User::orderBy('name', 'asc')->get();
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
        $user = User::findOrFail($id);
        
        $this->validate($request, [
            'name'              => 'required|string|max:191',
            'email'             => 'required|string|max:191|email|unique:users,email,'.$user->id,
            'password'          => 'required|string|min:6',
            'role_id'           => 'required|numeric|exists:roles,id',
            'department_id'     => 'required|numeric|exists:departments,id',
        ]);

        // update the password request with hashed password
        $request->merge(['password' => Hash::make($request->password)]);

        $user->update($request->all());
        
        return $user;    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if(!is_null($user)) {
            $user->delete();
            return ['message' => 'User deleted Successfully'];
        } 
        
        return ['message' => 'Something wrong! User not deleted'];
    }
}
