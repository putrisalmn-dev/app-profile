<?php

namespace Database\Seeders;

use App\Enums\AnswerStatus;
use App\Enums\QuestionStatus;
use App\Models\Faq;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionTopic;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::query()->first();

        $topics = [
            ['name' => 'Pelayanan Publik', 'slug' => 'pelayanan-publik', 'description' => 'Pertanyaan seputar prosedur dan persyaratan layanan.', 'sort_order' => 1],
            ['name' => 'Administrasi & Dokumen', 'slug' => 'administrasi-dokumen', 'description' => 'Pertanyaan mengenai pengurusan surat dan dokumen.', 'sort_order' => 2],
            ['name' => 'Pengaduan & Saran', 'slug' => 'pengaduan-saran', 'description' => 'Masukan, saran, dan pengaduan layanan.', 'sort_order' => 3],
        ];

        foreach ($topics as $data) {
            QuestionTopic::query()->updateOrCreate(['slug' => $data['slug']], $data + ['is_active' => true]);
        }

        $pelayanan = QuestionTopic::query()->where('slug', 'pelayanan-publik')->first();

        if ($pelayanan !== null) {
            Faq::query()->updateOrCreate(
                ['topic_id' => $pelayanan->id, 'question' => 'Bagaimana cara mengajukan pertanyaan melalui aplikasi?'],
                [
                    'answer' => '<p>Anda dapat memilih menu Pertanyaan Masyarakat pada aplikasi, mengisi formulir dengan topik dan isi pertanyaan, lalu mengirimkannya. Jawaban akan ditampilkan di aplikasi dan dikirim ke email Anda.</p>',
                    'sort_order' => 1,
                    'is_active' => true,
                ],
            );

            Faq::query()->updateOrCreate(
                ['topic_id' => $pelayanan->id, 'question' => 'Berapa lama waktu tanggapan atas pertanyaan yang dikirim?'],
                [
                    'answer' => '<p>Setiap pertanyaan yang masuk akan diverifikasi dan dijawab oleh petugas maksimal dalam 2 hari kerja.</p>',
                    'sort_order' => 2,
                    'is_active' => true,
                ],
            );

            $question = Question::query()->updateOrCreate(
                ['email' => 'warga@example.com', 'subject' => 'Persyaratan Pengajuan Layanan Informasi'],
                [
                    'topic_id' => $pelayanan->id,
                    'name' => 'Budi Santoso',
                    'phone' => '081234567890',
                    'body' => 'Mohon informasi dokumen apa saja yang perlu dibawa saat mengajukan permohonan informasi publik secara langsung?',
                    'status' => QuestionStatus::Answered,
                    'ip_address' => '127.0.0.1',
                ],
            );

            QuestionAnswer::withoutEvents(function () use ($question, $author): void {
                QuestionAnswer::query()->updateOrCreate(
                    ['question_id' => $question->id],
                    [
                        'user_id' => $author?->id,
                        'body' => '<p>Untuk permohonan informasi secara langsung, silakan membawa identitas diri (KTP) yang masih berlaku serta mengisi formulir permohonan di loket layanan informasi.</p>',
                        'status' => AnswerStatus::Published,
                        'answered_at' => now(),
                    ],
                );
            });
        }
    }
}
