<?php

namespace Tests\Feature;

use App\Enums\MenuItemType;
use App\Enums\MenuLocation;
use App\Enums\SettingType;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\QuestionTopic;
use App\Models\Setting;
use App\Models\Slide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_posts_endpoint_returns_only_published_posts_and_filters_by_category(): void
    {
        $category = Category::factory()->create(['slug' => 'berita']);
        $otherCategory = Category::factory()->create(['slug' => 'pengumuman']);

        $published = Post::factory()->for($category)->create(['title' => 'Artikel Publik']);
        Post::factory()->for($otherCategory)->create(['title' => 'Artikel Lain']);
        Post::factory()->for($category)->draft()->create(['title' => 'Artikel Draft']);
        Post::factory()->for($category)->create([
            'title' => 'Artikel Masa Depan',
            'published_at' => now()->addDay(),
        ]);

        $response = $this->getJson('/api/v1/posts?category=berita');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $published->id)
            ->assertJsonPath('data.0.category.slug', 'berita');
    }

    public function test_showing_a_post_increments_its_view_count_and_hides_drafts(): void
    {
        $post = Post::factory()->create(['slug' => 'judul-artikel', 'view_count' => 3]);
        $draft = Post::factory()->draft()->create(['slug' => 'draft-artikel']);

        $this->getJson('/api/v1/posts/judul-artikel')
            ->assertOk()
            ->assertJsonPath('data.slug', 'judul-artikel')
            ->assertJsonPath('data.view_count', 4);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'view_count' => 4]);

        $this->getJson("/api/v1/posts/{$draft->slug}")->assertNotFound();
    }

    public function test_pages_endpoint_returns_published_page_and_hides_draft(): void
    {
        Page::factory()->create(['slug' => 'tentang-kami', 'title' => 'Tentang Kami']);
        Page::factory()->draft()->create(['slug' => 'halaman-draft']);

        $this->getJson('/api/v1/pages/tentang-kami')
            ->assertOk()
            ->assertJsonPath('data.title', 'Tentang Kami');

        $this->getJson('/api/v1/pages/halaman-draft')->assertNotFound();
    }

    public function test_categories_endpoint_returns_active_categories_with_published_posts_count(): void
    {
        $active = Category::factory()->create(['name' => 'Berita', 'sort_order' => 1]);
        Category::factory()->inactive()->create(['name' => 'Nonaktif']);

        Post::factory()->for($active)->count(2)->create();
        Post::factory()->for($active)->draft()->create();

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Berita')
            ->assertJsonPath('data.0.posts_count', 2);
    }

    public function test_slides_endpoint_respects_active_flag_and_schedule_dates(): void
    {
        $visible = Slide::factory()->create([
            'title' => 'Slide Aktif',
            'start_date' => today()->subDay(),
            'end_date' => today()->addDay(),
        ]);

        Slide::factory()->create([
            'title' => 'Slide Kedaluwarsa',
            'end_date' => today()->subDay(),
        ]);

        Slide::factory()->create([
            'title' => 'Slide Nonaktif',
            'is_active' => false,
        ]);

        $this->getJson('/api/v1/slides')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $visible->id);
    }

    public function test_menus_endpoint_resolves_nested_items_and_internal_links(): void
    {
        $page = Page::factory()->create(['slug' => 'visi-misi']);
        $menu = Menu::factory()->create(['location' => MenuLocation::Header]);

        $parent = MenuItem::factory()->for($menu)->create([
            'label' => 'Profil',
            'type' => MenuItemType::Page,
            'linkable_id' => $page->id,
            'sort_order' => 1,
        ]);

        MenuItem::factory()->for($menu)->create([
            'parent_id' => $parent->id,
            'label' => 'Kontak',
            'type' => MenuItemType::Custom,
            'url' => '/kontak',
            'sort_order' => 1,
        ]);

        $this->getJson('/api/v1/menus/header')
            ->assertOk()
            ->assertJsonPath('data.location', 'header')
            ->assertJsonPath('data.items.0.label', 'Profil')
            ->assertJsonPath('data.items.0.url', '/pages/visi-misi')
            ->assertJsonPath('data.items.0.children.0.label', 'Kontak')
            ->assertJsonPath('data.items.0.children.0.url', '/kontak');
    }

    public function test_settings_endpoint_groups_and_casts_values(): void
    {
        Setting::factory()->create([
            'group' => 'general',
            'key' => 'site_name',
            'value' => 'Portal Instansi',
            'type' => SettingType::Text,
        ]);

        Setting::factory()->create([
            'group' => 'general',
            'key' => 'maintenance_mode',
            'value' => '0',
            'type' => SettingType::Boolean,
        ]);

        $this->getJson('/api/v1/settings')
            ->assertOk()
            ->assertJsonPath('data.general.site_name', 'Portal Instansi')
            ->assertJsonPath('data.general.maintenance_mode', false);
    }

    public function test_faqs_and_question_topics_endpoints_filter_active_records(): void
    {
        $topic = QuestionTopic::factory()->create(['slug' => 'pelayanan']);
        $otherTopic = QuestionTopic::factory()->create(['slug' => 'administrasi']);

        Faq::factory()->for($topic, 'topic')->create(['question' => 'Bagaimana cara daftar?']);
        Faq::factory()->for($otherTopic, 'topic')->create(['question' => 'Bagaimana syarat surat?']);

        $this->getJson('/api/v1/question-topics')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson('/api/v1/faqs?topic=pelayanan')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.question', 'Bagaimana cara daftar?');
    }
}
