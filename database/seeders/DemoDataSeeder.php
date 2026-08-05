<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\Donation;
use App\Models\MemberPoints;
use App\Models\EventVolunteer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Seeding demo data...');

        $this->insertMembers();
        $this->insertEvents();
        $this->insertDonations();
        $this->insertMemberPoints();
        $this->insertEventVolunteer();

        $this->command->info('Demo data seeded successfully!');
    }

    protected function insertMembers()
    {
        $members = [
            ['name' => 'Fatimah Binti Yusof', 'email' => 'fatimah@mosque.com', 'phone' => '0111111111'],
            ['name' => 'Ahmad Bin Ismail', 'email' => 'ahmad@mosque.com', 'phone' => '0112222222'],
            ['name' => 'Nurul Huda Binti Kamal', 'email' => 'nurul@mosque.com', 'phone' => '0113333333'],
            ['name' => 'Mohd Rizal Bin Abdullah', 'email' => 'rizal@mosque.com', 'phone' => '0114444444'],
            ['name' => 'Aisyah Binti Hassan', 'email' => 'aisyah@mosque.com', 'phone' => '0115555555'],
            ['name' => 'Khairul Anwar Bin Mat', 'email' => 'khairul@mosque.com', 'phone' => '0116666666'],
        ];

        foreach ($members as $i => $m) {
            User::create([
                'name' => $m['name'],
                'email' => $m['email'],
                'password' => Hash::make('password'),
                'role' => 'member',
                'phone' => $m['phone'],
                'email_verified_at' => now(),
                'age' => [28, 35, 24, 42, 26, 31][$i],
                'address' => [
                    'No 12, Jalan SS2/3, Petaling Jaya',
                    'Lot 45, Taman Mutiara, Ampang',
                    'No 8, Jalan Bukit Bintang, KL',
                    'No 3, Jalan Merpati, Shah Alam',
                    'No 22, Jalan Ros, Bangi',
                    'No 5, Jalan Mawar, Putrajaya',
                ][$i],
            ]);
        }

        $this->command->info('6 members inserted (IDs 4-9)');
    }

    protected function insertEvents()
    {
        Event::create([
            'title' => 'Gotong Royong Bersih Masjid',
            'description' => 'Membersihkan kawasan masjid, dewan, dan bilik wuduk untuk sambutan Aidilfitri. Sukarelawan diperlukan untuk menyapu, mengemop, dan menyusun perabot.',
            'event_date' => '2026-04-15 08:00:00',
            'end_time' => '2026-04-15 12:00:00',
            'location' => 'Masjid Al-Hasanah',
            'required_skills' => json_encode(['Cleaning', 'Gardening']),
            'max_volunteers' => 15,
            'status' => 'closed',
            'gamification_category' => 'general',
        ]);

        Event::create([
            'title' => 'Kuliah Maghrib: Fiqh Puasa',
            'description' => 'Kuliah mingguan selepas solat Maghrib. Tajuk kali ini adalah berkaitan fiqh puasa untuk persiapan Ramadan.',
            'event_date' => '2026-05-10 19:15:00',
            'end_time' => '2026-05-10 21:00:00',
            'location' => 'Masjid Al-Hasanah',
            'required_skills' => json_encode([]),
            'max_volunteers' => 5,
            'status' => 'closed',
            'gamification_category' => 'education',
        ]);

        Event::create([
            'title' => 'Bantuan Banjir Pantai Timur',
            'description' => 'Mengumpul dan menyusun barangan bantuan untuk mangsa banjir di Pantai Timur. Sukarelawan diperlukan untuk mengemas, mengisi, dan memuatkan barang ke dalam lori.',
            'event_date' => '2026-06-01 09:00:00',
            'end_time' => '2026-06-01 17:00:00',
            'event_location' => 'Dewan Serbaguna',
            'location_radius' => 'Any',
            'required_skills' => json_encode(['Heavy Lifting', 'Packing', 'Driving']),
            'max_volunteers' => 30,
            'health_requirement' => 'Sihat dan cergas',
            'status' => 'open',
            'gamification_category' => 'relief',
        ]);

        Event::create([
            'title' => 'Program Sahur Ramadan',
            'description' => 'Menyediakan makanan sahur untuk anak-anak yatim dan fakir miskin sepanjang bulan Ramadan. Sukarelawan diperlukan untuk memasak dan menghidang.',
            'event_date' => '2026-06-20 04:00:00',
            'end_time' => '2026-06-20 07:00:00',
            'location' => 'Masjid Al-Hasanah',
            'required_skills' => json_encode(['Cooking', 'Serving']),
            'max_volunteers' => 20,
            'status' => 'open',
            'gamification_category' => 'general',
        ]);

        Event::create([
            'title' => 'Kelas Asas Fardhu Ain',
            'description' => 'Kelas asas fardhu ain untuk kanak-kanak dan remaja. Sukarelawan diminta membantu sebagai fasilitator dan pengawas.',
            'event_date' => '2026-07-05 14:00:00',
            'end_time' => '2026-07-05 17:00:00',
            'location' => 'Bilik Kuliah, Masjid Al-Hasanah',
            'required_skills' => json_encode(['Teaching', 'Kids Friendly']),
            'max_volunteers' => 10,
            'status' => 'open',
            'gamification_category' => 'education',
        ]);

        Event::create([
            'title' => 'Gotong Royong Pengecatan',
            'description' => 'Mengetcat semula dinding dan pagar masjid yang telah pudar. Sukarelawan perlu membawa berus cat sendiri.',
            'event_date' => '2026-07-15 08:00:00',
            'end_time' => '2026-07-15 14:00:00',
            'location' => 'Masjid Al-Hasanah',
            'required_skills' => json_encode(['Painting', 'Cleaning']),
            'max_volunteers' => 25,
            'status' => 'open',
            'gamification_category' => 'general',
        ]);

        $this->command->info('6 events inserted');
    }

    protected function insertDonations()
    {
        Donation::create([
            'user_id' => 1,
            'amount' => 5000.00,
            'category' => 'zakat',
            'type' => 'obligatory',
            'source' => 'cash',
            'status' => 'confirmed',
            'verified_at' => now(),
            'verified_by' => 2,
            'donation_date' => '2026-01-15',
            'description' => 'Zakat pendapatan bulan Januari',
            'donor_name' => 'Admin Masjid',
            'donor_phone' => '0123456789',
        ]);

        Donation::create([
            'user_id' => 2,
            'amount' => 2500.00,
            'category' => 'zakat',
            'type' => 'obligatory',
            'source' => 'online',
            'status' => 'confirmed',
            'verified_at' => now(),
            'verified_by' => 1,
            'donation_date' => '2026-02-20',
            'description' => 'Zakat pendapatan bulan Februari',
            'donor_name' => 'Bendahari Masjid',
            'donor_phone' => '0198765432',
        ]);

        Donation::create([
            'user_id' => 4,
            'amount' => 300.00,
            'category' => 'zakat',
            'type' => 'obligatory',
            'source' => 'cash',
            'status' => 'pending',
            'donation_date' => '2026-05-18',
            'description' => 'Zakat pendapatan',
            'donor_name' => 'Fatimah Binti Yusof',
            'donor_phone' => '0111111111',
        ]);

        Donation::create([
            'user_id' => 1,
            'amount' => 1200.00,
            'category' => 'sadaqah',
            'type' => 'voluntary',
            'source' => 'cash',
            'status' => 'confirmed',
            'verified_at' => now(),
            'verified_by' => 2,
            'donation_date' => '2026-03-10',
            'description' => 'Derma am untuk tabung masjid',
            'donor_name' => 'Admin Masjid',
            'donor_phone' => '0123456789',
        ]);

        Donation::create([
            'user_id' => 5,
            'amount' => 500.00,
            'category' => 'sadaqah',
            'type' => 'voluntary',
            'source' => 'online',
            'status' => 'confirmed',
            'verified_at' => now(),
            'verified_by' => 1,
            'donation_date' => '2026-04-05',
            'description' => 'Derma untuk program bantuan banjir',
            'donor_name' => 'Ahmad Bin Ismail',
            'donor_phone' => '0112222222',
        ]);

        Donation::create([
            'user_id' => 6,
            'amount' => 150.00,
            'category' => 'sadaqah',
            'type' => 'voluntary',
            'source' => 'cash',
            'status' => 'pending',
            'donation_date' => '2026-05-19',
            'description' => 'Sumbangan ikhlas',
            'donor_name' => 'Nurul Huda Binti Kamal',
            'donor_phone' => '0113333333',
        ]);

        Donation::create([
            'user_id' => 1,
            'amount' => 3000.00,
            'category' => 'waqf',
            'type' => 'endowment',
            'source' => 'cash',
            'status' => 'confirmed',
            'verified_at' => now(),
            'verified_by' => 2,
            'donation_date' => '2026-01-01',
            'description' => 'Wakaf untuk pembinaan dewan baru',
            'donor_name' => 'Admin Masjid',
            'donor_phone' => '0123456789',
        ]);

        Donation::create([
            'user_id' => 7,
            'amount' => 1000.00,
            'category' => 'waqf',
            'type' => 'endowment',
            'source' => 'online',
            'status' => 'confirmed',
            'verified_at' => now(),
            'verified_by' => 1,
            'donation_date' => '2026-04-20',
            'description' => 'Wakaf untuk pembelian karpet baru',
            'donor_name' => 'Mohd Rizal Bin Abdullah',
            'donor_phone' => '0114444444',
        ]);

        Donation::create([
            'user_id' => 1,
            'amount' => 50.00,
            'category' => 'zakat_fitr',
            'type' => 'obligatory',
            'source' => 'cash',
            'status' => 'confirmed',
            'verified_at' => now(),
            'verified_by' => 2,
            'donation_date' => '2026-04-01',
            'description' => 'Zakat fitrah untuk diri sendiri',
            'donor_name' => 'Admin Masjid',
            'donor_phone' => '0123456789',
        ]);

        Donation::create([
            'user_id' => 8,
            'amount' => 200.00,
            'category' => 'zakat_fitr',
            'type' => 'obligatory',
            'source' => 'cash',
            'status' => 'confirmed',
            'verified_at' => now(),
            'verified_by' => 2,
            'donation_date' => '2026-04-02',
            'description' => 'Zakat fitrah untuk keluarga (4 orang)',
            'donor_name' => 'Aisyah Binti Hassan',
            'donor_phone' => '0115555555',
        ]);

        Donation::create([
            'user_id' => 1,
            'amount' => 800.00,
            'category' => 'sadaqah',
            'type' => 'voluntary',
            'source' => 'online',
            'status' => 'confirmed',
            'verified_at' => now(),
            'verified_by' => 2,
            'donation_date' => '2026-05-01',
            'description' => 'Derma untuk program iftar Ramadan',
            'donor_name' => 'Admin Masjid',
            'donor_phone' => '0123456789',
            'fund_purpose' => 'Program Iftar',
        ]);

        Donation::create([
            'user_id' => 9,
            'amount' => 100.00,
            'category' => 'sadaqah',
            'type' => 'voluntary',
            'source' => 'cash',
            'status' => 'pending',
            'donation_date' => '2026-05-20',
            'description' => 'Derma untuk tabung kebajikan',
            'donor_name' => 'Khairul Anwar Bin Mat',
            'donor_phone' => '0116666666',
        ]);

        $this->command->info('12 donations inserted');
    }

    protected function insertMemberPoints()
    {
        $points = [
            ['user_id' => 1, 'total' => 0, 'available' => 0, 'redeemed' => 0, 'streak' => 0, 'longest' => 0, 'last_activity' => null],
            ['user_id' => 2, 'total' => 0, 'available' => 0, 'redeemed' => 0, 'streak' => 0, 'longest' => 0, 'last_activity' => null],
            ['user_id' => 3, 'total' => 450, 'available' => 350, 'redeemed' => 100, 'streak' => 3, 'longest' => 7, 'last_activity' => '2026-05-18'],
            ['user_id' => 4, 'total' => 280, 'available' => 200, 'redeemed' => 80, 'streak' => 5, 'longest' => 10, 'last_activity' => '2026-05-19'],
            ['user_id' => 5, 'total' => 180, 'available' => 180, 'redeemed' => 0, 'streak' => 2, 'longest' => 4, 'last_activity' => '2026-05-15'],
            ['user_id' => 6, 'total' => 520, 'available' => 400, 'redeemed' => 120, 'streak' => 8, 'longest' => 12, 'last_activity' => '2026-05-20'],
            ['user_id' => 7, 'total' => 350, 'available' => 300, 'redeemed' => 50, 'streak' => 4, 'longest' => 6, 'last_activity' => '2026-05-17'],
            ['user_id' => 8, 'total' => 100, 'available' => 100, 'redeemed' => 0, 'streak' => 1, 'longest' => 3, 'last_activity' => '2026-05-10'],
            ['user_id' => 9, 'total' => 60, 'available' => 60, 'redeemed' => 0, 'streak' => 1, 'longest' => 1, 'last_activity' => '2026-05-12'],
        ];

        foreach ($points as $p) {
            MemberPoints::create([
                'user_id' => $p['user_id'],
                'total_points' => $p['total'],
                'available_points' => $p['available'],
                'redeemed_points' => $p['redeemed'],
                'current_streak' => $p['streak'],
                'longest_streak' => $p['longest'],
                'last_activity_date' => $p['last_activity'],
            ]);
        }

        $this->command->info('9 member_points rows inserted');
    }

    protected function insertEventVolunteer()
    {
        // Event 1 (id=1): Gotong Royong Bersih Masjid (closed)
        EventVolunteer::create(['event_id' => 1, 'user_id' => 3, 'status' => 'confirmed', 'attendance_status' => 'completed', 'points_awarded' => true, 'points_earned' => 50]);
        EventVolunteer::create(['event_id' => 1, 'user_id' => 4, 'status' => 'confirmed', 'attendance_status' => 'completed', 'points_awarded' => true, 'points_earned' => 50]);
        EventVolunteer::create(['event_id' => 1, 'user_id' => 5, 'status' => 'confirmed', 'attendance_status' => 'absent', 'absence_reason' => 'Demam', 'points_awarded' => false, 'points_earned' => 0]);
        EventVolunteer::create(['event_id' => 1, 'user_id' => 6, 'status' => 'confirmed', 'attendance_status' => 'completed', 'points_awarded' => true, 'points_earned' => 50]);
        EventVolunteer::create(['event_id' => 1, 'user_id' => 1, 'status' => 'confirmed', 'attendance_status' => 'completed', 'points_awarded' => true, 'points_earned' => 50]);

        // Event 2 (id=2): Kuliah Maghrib (closed)
        EventVolunteer::create(['event_id' => 2, 'user_id' => 3, 'status' => 'confirmed', 'attendance_status' => 'completed', 'points_awarded' => true, 'points_earned' => 30]);
        EventVolunteer::create(['event_id' => 2, 'user_id' => 7, 'status' => 'confirmed', 'attendance_status' => 'completed', 'points_awarded' => true, 'points_earned' => 30]);
        EventVolunteer::create(['event_id' => 2, 'user_id' => 9, 'status' => 'confirmed', 'attendance_status' => 'absent', 'absence_reason' => 'Ada urusan keluarga', 'points_awarded' => false, 'points_earned' => 0]);

        // Event 3 (id=3): Bantuan Banjir (open - no attendance yet)
        EventVolunteer::create(['event_id' => 3, 'user_id' => 3, 'status' => 'confirmed', 'attendance_status' => 'confirmed', 'points_awarded' => false, 'points_earned' => 0]);
        EventVolunteer::create(['event_id' => 3, 'user_id' => 4, 'status' => 'confirmed', 'attendance_status' => 'confirmed', 'points_awarded' => false, 'points_earned' => 0]);
        EventVolunteer::create(['event_id' => 3, 'user_id' => 6, 'status' => 'confirmed', 'attendance_status' => 'confirmed', 'points_awarded' => false, 'points_earned' => 0]);

        // Event 4 (id=4): Program Sahur
        EventVolunteer::create(['event_id' => 4, 'user_id' => 5, 'status' => 'confirmed', 'attendance_status' => 'confirmed', 'points_awarded' => false, 'points_earned' => 0]);
        EventVolunteer::create(['event_id' => 4, 'user_id' => 8, 'status' => 'confirmed', 'attendance_status' => 'confirmed', 'points_awarded' => false, 'points_earned' => 0]);

        $this->command->info('13 event_volunteer pivot rows inserted');
    }
}
