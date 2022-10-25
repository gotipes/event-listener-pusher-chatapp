<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('users')->insert([
            [
                'name' => 'andry',
                'email' => 'andry@gmail.com',
                'password' => bcrypt('11111111'),
                'created_at' => now()
            ],
            [
                'name' => 'melani',
                'email' => 'melani@gmail.com',
                'password' => bcrypt('11111111'),
                'created_at' => now()
            ],
            [
                'name' => 'wilsen',
                'email' => 'wilsen@gmail.com',
                'password' => bcrypt('11111111'),
                'created_at' => now()
            ],
            [
                'name' => 'andreas',
                'email' => 'andreas@gmail.com',
                'password' => bcrypt('11111111'),
                'created_at' => now()
            ],
            [
                'name' => 'julistian',
                'email' => 'julistian@gmail.com',
                'password' => bcrypt('11111111'),
                'created_at' => now()
            ],
        ]);

        DB::table('chats')->insert([
            [
                'name' => 'Andri Melani',
                'is_group' => 0,
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Andri Wilsen',
                'is_group' => 0,
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Wilsen Melani',
                'is_group' => 0,
                'created_by' => 3,
                'created_at' => now()
            ],
            [
                'name' => 'Group Chat Keluarga',
                'is_group' => 1,
                'created_by' => 3,
                'created_at' => now()
            ],
        ]);

        DB::table('chat_user')->insert([
            [ 'user_id' => 1, 'chat_id' => 1 ],
            [ 'user_id' => 2, 'chat_id' => 1 ],

            [ 'user_id' => 1, 'chat_id' => 2 ],
            [ 'user_id' => 3, 'chat_id' => 2 ],
            
            [ 'user_id' => 2, 'chat_id' => 3 ],
            [ 'user_id' => 3, 'chat_id' => 3 ],
            
            [ 'user_id' => 1, 'chat_id' => 4 ],
            [ 'user_id' => 2, 'chat_id' => 4 ],
            [ 'user_id' => 3, 'chat_id' => 4 ],
        ]);
    }
}
