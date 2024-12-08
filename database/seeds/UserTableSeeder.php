<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        $userPermission = Permission::all();
        $userRole = Role::where('slug', 'super-admin')->first();

        $userRole->permissions()->attach($userPermission);

        $userId = time().rand(1000,9000);

        User::create([
            'id' => $userId,
            'username' => 'admin',
            'first_name' => 'Md Ziaur',
            'last_name' => 'Rahman',
            'slug' => 'zia-rahman',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10)
        ]);

        $user = User::where('id', $userId)->first();
        $user->roles()->attach($userRole);
        $user->permissions()->attach($userPermission);
        $user->media()->create([
            'url' => url('/').'/media/avatar/placeholder.png'
        ]);

        Schema::enableForeignKeyConstraints();

    }
}
