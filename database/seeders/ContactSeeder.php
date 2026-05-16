<?php

namespace Database\Seeders;

use App\Contact\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        Contact::factory()->count(5)->create();
    }
}
