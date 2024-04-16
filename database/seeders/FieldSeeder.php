<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Field::query()
        ->insert([
            ["name"=>"Facebook","type"=>"social"],
            ["name"=>"Instagram","type"=>"social"],
            ["name"=>"WhatsApp","type"=>"social"],
            ["name"=>"Snapchat","type"=>"social"],
            ["name"=>"Discord","type"=>"social"],
            ["name"=>"Telegram","type"=>"social"],
            ["name"=>"Messenger","type"=>"social"],
            ["name"=>"YouTube","type"=>"social"],
            ["name"=>"TikTok","type"=>"social"],
            ["name"=>"BeReal","type"=>"social"],
            ["name"=>"LinkedIn","type"=>"social"],
            ["name"=>"Twitter/X","type"=>"social"],
            ["name"=>"WeChat","type"=>"social"],
            ["name"=>"Pinterest","type"=>"social"],
            ["name"=>"Reddit","type"=>"social"],
            ["name"=>"Twitch","type"=>"social"],
            ["name"=>"Threads","type"=>"social"],
            ["name"=>"name","type"=>"button"],
            ["name"=>"company_name","type"=>"button"],
            ["name"=>"company_url","type"=>"button"],
            ["name"=>"company_position","type"=>"button"],
            ["name"=>"phone_number1","type"=>"button"],
            ["name"=>"phone_number2","type"=>"button"],
            ["name"=>"email1","type"=>"button"],
            ["name"=>"email2","type"=>"button"],
            ["name"=>"bio","type"=>"button"],
            ["name"=>"photo","type"=>"button"],
            ["name"=>"banner","type"=>"button"],
            ["name"=>"location","type"=>"button"],
            ["name"=>"youtube_video","type"=>"button"],
            ["name"=>"paypal","type"=>"button"],
            ["name"=>"venmo","type"=>"button"],
            ["name"=>"cashapp","type"=>"button"],
        ]);
    }
}
