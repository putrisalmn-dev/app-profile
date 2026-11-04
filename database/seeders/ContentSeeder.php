<?php

namespace Database\Seeders;

use App\Enums\MenuItemType;
use App\Enums\MenuLocation;
use App\Enums\PageStatus;
use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::query()->first() ?? User::factory()->admin()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
        ]);

        $categories = [
            ['name' => 'Berita Utama', 'slug' => 'berita-utama', 'description' => 'Berita dan kegiatan utama instansi.', 'sort_order' => 1],
            ['name' => 'Pengumuman', 'slug' => 'pengumuman', 'description' => 'Informasi resmi dan pengumuman untuk masyarakat.', 'sort_order' => 2],
            ['name' => 'Artikel Edukasi', 'slug' => 'artikel-edukasi', 'description' => 'Artikel dan panduan layanan publik.', 'sort_order' => 3],
        ];

        foreach ($categories as $data) {
            Category::query()->updateOrCreate(['slug' => $data['slug']], $data + ['is_active' => true]);
        }

        $berita = Category::query()->where('slug', 'berita-utama')->first();
        $pengumuman = Category::query()->where('slug', 'pengumuman')->first();

        Post::query()->updateOrCreate(
            ['slug' => 'peluncuran-portal-profil-dan-layanan-masyarakat'],
            [
                'user_id' => $author->id,
                'category_id' => $berita?->id,
                'title' => 'Peluncuran Portal Profil dan Layanan Masyarakat',
                'summary' => 'Portal digital resmi diluncurkan untuk memudahkan akses informasi dan penyampaian pertanyaan masyarakat.',
                'content' => '<p>Kami resmi meluncurkan portal profil instansi yang terintegrasi dengan layanan pertanyaan masyarakat.</p><p>Masyarakat kini dapat mengakses informasi terbaru serta mengajukan pertanyaan secara langsung melalui aplikasi.</p>',
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(2),
                'is_featured' => true,
            ],
        );

        Post::query()->updateOrCreate(
            ['slug' => 'jadwal-pelayanan-publik-terbaru'],
            [
                'user_id' => $author->id,
                'category_id' => $pengumuman?->id,
                'title' => 'Jadwal Pelayanan Publik Terbaru',
                'summary' => 'Informasi jam operasional pelayanan tatap muka dan daring.',
                'content' => '<p>Pelayanan tatap muka beroperasi setiap hari kerja pukul 08.00 s.d. 16.00 WIB. Layanan pertanyaan daring tersedia 24 jam.</p>',
                'status' => PostStatus::Published,
                'published_at' => now()->subDay(),
                'is_featured' => false,
            ],
        );

        $pages = [
            [
                'title' => 'Tentang Kami',
                'slug' => 'tentang-kami',
                'content' => '<p>Instansi kami berkomitmen memberikan pelayanan prima, transparan, dan akuntabel bagi seluruh masyarakat.</p>',
                'sort_order' => 1,
            ],
            [
                'title' => 'Visi dan Misi',
                'slug' => 'visi-dan-misi',
                'content' => '<h3>Visi</h3><p>Terwujudnya pelayanan publik yang modern, cepat, dan terpercaya.</p><h3>Misi</h3><ul><li>Meningkatkan kualitas layanan informasi publik.</li><li>Menyediakan kanal komunikasi masyarakat yang responsif.</li></ul>',
                'sort_order' => 2,
            ],
            [
                'title' => 'Struktur Organisasi',
                'slug' => 'struktur-organisasi',
                'content' => '<p>Informasi susunan organisasi dan tata kerja instansi.</p>',
                'sort_order' => 3,
            ],
        ];

        foreach ($pages as $data) {
            Page::query()->updateOrCreate(
                ['slug' => $data['slug']],
                $data + [
                    'user_id' => $author->id,
                    'status' => PageStatus::Published,
                    'show_in_menu' => true,
                ],
            );
        }

        $headerMenu = Menu::query()->updateOrCreate(
            ['location' => MenuLocation::Header],
            ['name' => 'Menu Utama', 'is_active' => true],
        );

        $aboutPage = Page::query()->where('slug', 'tentang-kami')->first();

        MenuItem::query()->updateOrCreate(
            ['menu_id' => $headerMenu->id, 'label' => 'Beranda'],
            ['type' => MenuItemType::Custom, 'url' => '/', 'sort_order' => 1, 'is_active' => true],
        );

        if ($aboutPage !== null) {
            MenuItem::query()->updateOrCreate(
                ['menu_id' => $headerMenu->id, 'label' => 'Tentang Kami'],
                [
                    'type' => MenuItemType::Page,
                    'linkable_type' => 'page',
                    'linkable_id' => $aboutPage->id,
                    'sort_order' => 2,
                    'is_active' => true,
                ],
            );
        }
    }
}
