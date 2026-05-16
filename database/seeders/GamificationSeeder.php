<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Reward;
use App\Models\TierMilestone;
use Illuminate\Database\Seeder;

class GamificationSeeder extends Seeder
{
    public function run()
    {
        $this->seedTiers();
        $this->seedBadges();
        $this->seedRewards();
    }

    private function seedTiers()
    {
        $tiers = [
            [
                'tier' => 'bronze',
                'min_points' => 0,
                'name' => 'Bronze Volunteer',
                'name_my' => 'Sukarelawan Perunggu',
                'benefits' => 'Basic recognition, community updates, event updates',
                'benefits_my' => 'Pengiktirafan asas, kemas kini komuniti, kemas kini acara',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#CD7F32"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
            ],
            [
                'tier' => 'silver',
                'min_points' => 200,
                'name' => 'Silver Volunteer',
                'name_my' => 'Sukarelawan Perak',
                'benefits' => 'Early event registration (24h before), monthly newsletter, exclusive badges',
                'benefits_my' => 'Pendaftaran acara awal (24 jam sebelumnya), surat berita bulanan, lencana eksklusif',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#C0C0C0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>',
            ],
            [
                'tier' => 'gold',
                'min_points' => 500,
                'name' => 'Gold Volunteer',
                'name_my' => 'Sukarelawan Emas',
                'benefits' => 'Certificate eligibility, VIP event access, profile badge display',
                'benefits_my' => 'Kelayakan sijil, akses acara VIP, paparan lencana profil',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#FFD700"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>',
            ],
            [
                'tier' => 'platinum',
                'min_points' => 1000,
                'name' => 'Platinum Volunteer',
                'name_my' => 'Sukarelawan Platinum',
                'benefits' => 'Priority seating, special prayer area access, annual dinner invite',
                'benefits_my' => 'Tempat duduk keutamaan, akses kawasan solat khas, jemputan makan malam tahunan',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#E5E4E2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
            ],
            [
                'tier' => 'diamond',
                'min_points' => 2000,
                'name' => 'Diamond Volunteer',
                'name_my' => 'Sukarelawan Berlian',
                'benefits' => 'Lifetime recognition, leadership opportunities, permanent wall of honor',
                'benefits_my' => 'Pengiktirafan seumur hidup, peluang kepimpinan, kedudukan kehormat kekal',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#B9F2F1"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>',
            ],
        ];

        foreach ($tiers as $tier) {
            TierMilestone::create($tier);
        }
    }

    private function seedBadges()
    {
        $badges = [
            [
                'code' => 'first_step',
                'name' => 'First Step',
                'name_my' => 'Langkah Pertama',
                'description' => 'Complete your first volunteer event',
                'description_my' => 'Lengkapkan acara sukarelawan pertama anda',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>',
                'tier' => 'bronze',
                'points_awarded' => 25,
            ],
            [
                'code' => 'consistent',
                'name' => 'Consistent',
                'name_my' => 'Konsisten',
                'description' => 'Complete 5 volunteer events',
                'description_my' => 'Lengkapkan 5 acara sukarelawan',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
                'tier' => 'bronze',
                'points_awarded' => 50,
            ],
            [
                'code' => 'dedicated',
                'name' => 'Dedicated',
                'name_my' => 'Berdedikasi',
                'description' => 'Complete 10 volunteer events',
                'description_my' => 'Lengkapkan 10 acara sukarelawan',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#EF4444"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>',
                'tier' => 'silver',
                'points_awarded' => 100,
            ],
            [
                'code' => 'helping_hand',
                'name' => 'Helping Hand',
                'name_my' => 'Tangan Membantu',
                'description' => 'Complete 25 volunteer events',
                'description_my' => 'Lengkapkan 25 acara sukarelawan',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#10B981"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'tier' => 'gold',
                'points_awarded' => 200,
            ],
            [
                'code' => 'masjid_hero',
                'name' => 'Masjid Hero',
                'name_my' => 'Wira Masjid',
                'description' => 'Complete 50 volunteer events',
                'description_my' => 'Lengkapkan 50 acara sukarelawan',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#F59E0B"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
                'tier' => 'platinum',
                'points_awarded' => 500,
            ],
            [
                'code' => 'early_bird',
                'name' => 'Early Bird',
                'name_my' => 'Burung Awal',
                'description' => 'Join 10 events 7+ days early',
                'description_my' => 'Daftar 10 acara 7+ hari lebih awal',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#8B5CF6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/></svg>',
                'tier' => 'bronze',
                'points_awarded' => 75,
            ],
            [
                'code' => 'streak_master',
                'name' => 'Streak Master',
                'name_my' => 'Master Siri',
                'description' => 'Complete 5 events in a row',
                'description_my' => 'Lengkapkan 5 acara berturut-turut',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#3B82F6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                'tier' => 'silver',
                'points_awarded' => 75,
            ],
            [
                'code' => 'religious_scholar',
                'name' => 'Religious Scholar',
                'name_my' => 'Sarjana Agama',
                'description' => 'Complete 10 religious education events',
                'description_my' => 'Lengkapkan 10 acara pendidikan agama',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#7C3AED"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                'tier' => 'gold',
                'points_awarded' => 100,
            ],
            [
                'code' => 'emergency_responder',
                'name' => 'Emergency Responder',
                'name_my' => 'Penjawab Kecemasan',
                'description' => 'Complete 5 emergency relief events',
                'description_my' => 'Lengkapkan 5 acara bantuan kecemasan',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#DC2626"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                'tier' => 'gold',
                'points_awarded' => 100,
            ],
            [
                'code' => 'team_player',
                'name' => 'Team Player',
                'name_my' => 'Pemain Pasukan',
                'description' => 'Refer 3 members who complete events',
                'description_my' => 'Rujuk 3 ahli yang lengkapkan acara',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#06B6D4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
                'tier' => 'silver',
                'points_awarded' => 60,
            ],
            [
                'code' => 'perfect_record',
                'name' => 'Perfect Record',
                'name_my' => 'Rekod Sempurna',
                'description' => '30 consecutive days with no absences',
                'description_my' => '30 hari berturut-turut tanpa absen',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#22C55E"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'tier' => 'silver',
                'points_awarded' => 50,
            ],
            [
                'code' => 'monthly_champion',
                'name' => 'Monthly Champion',
                'name_my' => 'Juara Bulanan',
                'description' => 'Highest points in a calendar month',
                'description_my' => 'Mata tertinggi dalam bulan kalendar',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#F59E0B"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
                'tier' => 'gold',
                'points_awarded' => 150,
            ],
            [
                'code' => 'founding_member',
                'name' => 'Founding Member',
                'name_my' => 'Ahli Teras',
                'description' => 'Joined during the platform launch period',
                'description_my' => 'Menyertai semasa pelancaran platform',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#BE185D"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'tier' => 'diamond',
                'points_awarded' => 100,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }

    private function seedRewards()
    {
        $rewards = [
            [
                'code' => 'early_registration',
                'name' => 'Early Event Registration',
                'name_my' => 'Pendaftaran Acara Awal',
                'description' => 'Register for events 1 day before public release',
                'description_my' => 'Daftar untuk acara 1 hari sebelum pelepasan awam',
                'image_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#3B82F6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'category' => 'priority',
                'points_cost' => 100,
            ],
            [
                'code' => 'vip_seats',
                'name' => 'VIP Event Seats',
                'name_my' => 'Tempat VIP Acara',
                'description' => 'Guaranteed spot in premium events',
                'description_my' => 'Tempat terjamin dalam acara premium',
                'image_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#8B5CF6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
                'category' => 'priority',
                'points_cost' => 150,
            ],
            [
                'code' => 'certificate',
                'name' => 'Certificate of Appreciation',
                'name_my' => 'Sijil Penghargaan',
                'description' => 'Official printable PDF certificate with mosque branding',
                'description_my' => 'Sijil PDF boleh cetak rasmi dengan jenama masjid',
                'image_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#10B981"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>',
                'category' => 'recognition',
                'points_cost' => 200,
            ],
            [
                'code' => 'shoutout',
                'name' => 'Announcement Shoutout',
                'name_my' => 'Pengumuman Sorotan',
                'description' => 'Your name will be mentioned in Friday announcement',
                'description_my' => 'Nama anda akan disebut dalam pengumuman Jumaat',
                'image_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#F59E0B"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-2.153a.53.53 0 00-.079-.049l-2.26-2.215a.534.534 0 01-.05-.089l1.327-1.328a.53.53 0 01.075-.089l2.215-2.215a.534.534 0 01.09-.05l2.153-2.153a1.76 1.76 0 01.59-3.417"/></svg>',
                'category' => 'recognition',
                'points_cost' => 100,
            ],
            [
                'code' => 'wall_feature',
                'name' => 'Wall of Honor Feature',
                'name_my' => 'Ciri Dinding Kehormat',
                'description' => 'Your profile featured on the Wall of Honor for 1 week',
                'description_my' => 'Profil anda dipamerkan di Dinding Kehormat selama 1 minggu',
                'image_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#BE185D"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
                'category' => 'recognition',
                'points_cost' => 300,
            ],
            [
                'code' => 'merch_pack',
                'name' => 'Mosque Merchandise Pack',
                'name_my' => 'Pakej Barangan Masjid',
                'description' => 'Exclusive t-shirt, name card, and pin',
                'description_my' => 'T-shirt eksklusif, kad nama, dan pin',
                'image_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#6366F1"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
                'category' => 'privilege',
                'points_cost' => 250,
            ],
            [
                'code' => 'prayer_area_access',
                'name' => 'Special Prayer Area Access',
                'name_my' => 'Akses Kawasan Solat Khas',
                'description' => 'Priority seating in special prayer areas during Eid',
                'description_my' => 'Tempat duduk keutamaan di kawasan solat khas semasa Eid',
                'image_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#059669"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                'category' => 'privilege',
                'points_cost' => 150,
            ],
            [
                'code' => 'ramadan_package',
                'name' => 'Ramadan Volunteer Package',
                'name_my' => 'Pakej Sukarelawan Ramadan',
                'description' => 'Special iftar preparation team role during Ramadan',
                'description_my' => 'Peranan pasukan penyediaan iftar semasa Ramadan',
                'image_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#DC2626"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
                'category' => 'seasonal',
                'points_cost' => 300,
                'valid_from' => now()->startOfYear(),
                'valid_until' => now()->endOfYear(),
            ],
        ];

        foreach ($rewards as $reward) {
            Reward::create($reward);
        }
    }
}
