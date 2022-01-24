<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminstratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //! Pertama kita memanfaatkan model User dengan membuat instance baru sebagai variabel $administrator
        $administrator = new User;

        // ? tambahkan properti-properti
        $administrator->username = "administrator";
        $administrator->name = "Site Administrator";
        $administrator->email = "administrator@larashop.test";
        $administrator->roles = json_encode(["ADMIN"]);
        //! Untuk menghashing password kita agar password tersebut di database tidak disimpan sebagai plain teks
        $administrator->password = Hash::make("larashop");
        $administrator->avatar = "saat-ini-tidak-ada-file.png";
        $administrator->address = "Jawa Tengah, Tegal, Dawuhan";

        // ! Perintah untuk insert ke database
        $administrator->save();

        // ! Menampilkan callback pada command line
        $this->command->info("User Admin berhasil diinsert");
    }
}
