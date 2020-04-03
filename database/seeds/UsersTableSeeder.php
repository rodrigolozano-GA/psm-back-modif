<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;

class UsersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		/*DB::table('users')->insert([
			'name' => 'admin',
			'email' => 'admin@a.com',
			'password' => bcrypt('testAdmin')
		]);*/

		$user = new User;
        $user->name = "admin";
        $user->email = "admin@a.com";
        $user->password = bcrypt('testAdmin');
        $user->save();
	}
}
