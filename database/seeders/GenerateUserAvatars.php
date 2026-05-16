<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class GenerateUserAvatars extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generating avatars for all users...');

        $users = User::all();
        $avatarDir = storage_path('app/public/avatars');

        if (!File::exists($avatarDir)) {
            File::makeDirectory($avatarDir, 0755, true);
        }

        $generated = 0;
        $skipped = 0;

        foreach ($users as $user) {
            if ($user->avatar) {
                $skipped++;
                continue;
            }

            $filename = 'avatar_' . $user->id . '_' . time() . '.png';
            $filepath = $avatarDir . '/' . $filename;

            $this->generatePersonAvatar($user->id, $user->name, $filepath);

            $user->update(['avatar' => $filename]);
            $generated++;
        }

        $this->command->info("Done! Generated: {$generated}, Skipped (already has avatar): {$skipped}");
    }

    private function generatePersonAvatar(int $userId, string $name, string $filepath): void
    {
        $size = 128;

        $image = imagecreatetruecolor($size, $size);

        $bgColor = $this->generateUniqueColor($userId, $name);
        $bg = imagecolorallocate($image, $bgColor['r'], $bgColor['g'], $bgColor['b']);

        imagefilledrectangle($image, 0, 0, $size, $size, $bg);

        $white = imagecolorallocate($image, 255, 255, 255);
        
        $shirtColor = $this->generateShirtColor($userId);
        $shirt = imagecolorallocate($image, $shirtColor['r'], $shirtColor['g'], $shirtColor['b']);

        $centerX = $size / 2;

        imagefilledellipse($image, $centerX, 35, 40, 40, $white);

        $bodyWidth = 70;
        $bodyHeight = 50;
        $bodyTop = 60;
        
        $this->imagefilledroundedrect($image, $centerX - $bodyWidth/2, $bodyTop, $centerX + $bodyWidth/2, $bodyTop + $bodyHeight, 20, $shirt);

        imagepng($image, $filepath);
        imagedestroy($image);
    }

    private function imagefilledroundedrect($img, $x1, $y1, $x2, $y2, $radius, $color): void
    {
        imagefilledrectangle($img, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($img, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);

        imagefilledarc($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $color, IMG_ARC_PIE);
    }

    private function generateUniqueColor(int $userId, string $name): array
    {
        $hash = md5($userId . $name);
        
        $hue = hexdec(substr($hash, 0, 2)) % 360;
        $saturation = 50 + (hexdec(substr($hash, 2, 2)) % 30);
        $lightness = 35 + (hexdec(substr($hash, 4, 2)) % 20);

        return $this->hslToRgb($hue, $saturation, $lightness);
    }

    private function generateShirtColor(int $userId): array
    {
        $colors = [
            ['r' => 255, 'g' => 255, 'b' => 255],
            ['r' => 239, 'g' => 246, 'b' => 255],
            ['r' => 243, 'g' => 244, 'b' => 246],
            ['r' => 229, 'g' => 231, 'b' => 235],
        ];
        
        return $colors[$userId % count($colors)];
    }

    private function hslToRgb(float $h, float $s, float $l): array
    {
        $s /= 100;
        $l /= 100;

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;

        if ($h < 60) {
            $r = $c; $g = $x; $b = 0;
        } elseif ($h < 120) {
            $r = $x; $g = $c; $b = 0;
        } elseif ($h < 180) {
            $r = 0; $g = $c; $b = $x;
        } elseif ($h < 240) {
            $r = 0; $g = $x; $b = $c;
        } elseif ($h < 300) {
            $r = $x; $g = 0; $b = $c;
        } else {
            $r = $c; $g = 0; $b = $x;
        }

        return [
            'r' => round(($r + $m) * 255),
            'g' => round(($g + $m) * 255),
            'b' => round(($b + $m) * 255),
        ];
    }
}