<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['Admin', 'User'];
        foreach ($roles as $role) {
            factory(App\Role::class)->create(['name' => $role]);
        }

        $departments = ['Default', 'Instrument & Control', 'Electrical', 'Mechanical', 'Process', 'Inspection & Safety', 'Quality Control'];
        foreach ($departments as $department) {
            factory(App\Department::class)->create(['name' => $department]);
        }

        // $this->call(UsersTableSeeder::class);
        // make user admin with role admin
        $user = factory(App\User::class)->create([
            'name' => 'Admin',
            'department_id' => 1,
            'role_id' => 1,
            'email' => 'admin@splerp.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123$'), // password
            'remember_token' => Str::random(10),
        ]);
        // make user 'User' with role User
        $user = factory(App\User::class)->create([
            'name' => 'User',
            'department_id' => 1,
            'role_id' => 2,
            'email' => 'user@splerp.com',
            'email_verified_at' => now(),
            'password' => Hash::make('user123$'), // password
            'remember_token' => Str::random(10),
        ]);

        // attach admin role and "Instrument & Control" Department to the user admin
        // $user->roles()->sync(1);
        // $user->departments()->sync(1);

        // // create another 9 users with different roles except 'admin'
        // factory(App\User::class, 9)->create()->each(function ($user) {
        //     $roles_models = App\Role::where('name', '!=', 'Admin')->pluck('id');
        //     $department_models = App\Department::pluck('id');
            
        //     // create & set roles
        //     $user->roles()->sync($roles_models->random(1));

        //     // create & set departments
        //     $user->departments()->sync($department_models->random(1));
        // });

        // factory(App\Category::class, 10)->create();
        // factory(App\Locker::class, 10)->create();
        // factory(App\Document::class, 10)->create();
    }
}
