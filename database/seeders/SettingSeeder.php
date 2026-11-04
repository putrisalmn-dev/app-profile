<?php

namespace Database\Seeders;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'label' => 'Nama Instansi', 'value' => 'Portal Profil Instansi', 'type' => SettingType::Text],
            ['group' => 'general', 'key' => 'site_tagline', 'label' => 'Tagline', 'value' => 'Melayani Masyarakat dengan Transparan dan Akuntabel', 'type' => SettingType::Text],
            ['group' => 'general', 'key' => 'site_description', 'label' => 'Deskripsi Singkat', 'value' => 'Portal informasi resmi dan layanan pertanyaan masyarakat.', 'type' => SettingType::Textarea],
            ['group' => 'general', 'key' => 'site_logo', 'label' => 'Logo Instansi', 'value' => null, 'type' => SettingType::Image],
            ['group' => 'contact', 'key' => 'address', 'label' => 'Alamat Kantor', 'value' => 'Jl. Merdeka No. 1, Jakarta Pusat', 'type' => SettingType::Textarea],
            ['group' => 'contact', 'key' => 'email', 'label' => 'Email Resmi', 'value' => 'info@instansi.go.id', 'type' => SettingType::Text],
            ['group' => 'contact', 'key' => 'phone', 'label' => 'Telepon', 'value' => '(021) 1234567', 'type' => SettingType::Text],
            ['group' => 'contact', 'key' => 'whatsapp', 'label' => 'WhatsApp Layanan', 'value' => '6281234567890', 'type' => SettingType::Text],
            ['group' => 'social', 'key' => 'instagram', 'label' => 'Instagram URL', 'value' => 'https://instagram.com/instansi', 'type' => SettingType::Text],
            ['group' => 'social', 'key' => 'facebook', 'label' => 'Facebook URL', 'value' => 'https://facebook.com/instansi', 'type' => SettingType::Text],
            ['group' => 'social', 'key' => 'youtube', 'label' => 'YouTube URL', 'value' => 'https://youtube.com/@instansi', 'type' => SettingType::Text],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting,
            );
        }
    }
}
