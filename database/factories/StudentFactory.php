<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['male', 'female']),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Student $student) {
            $qrCodeData = json_encode(['student_id' => $student->id, 'name' => $student->name]);
            $qrCodeFileName = uniqid() . '.png'; // Use PNG format for the image

            // Generate QR code as an image with higher quality settings
            $qrCode = QrCode::format('png')
                ->size(300) // Increase the size for higher quality
                ->color(255, 0, 0) // Set the color to red (RGB format)
                ->margin(10)
                ->errorCorrection('H') // Use high error correction level
                ->generate($qrCodeData);

            // Save the QR code image to storage
            $qrCodeFilePath = 'public/qrcodes/' . $qrCodeFileName;
            Storage::put($qrCodeFilePath, $qrCode);

            // Update the student with QR code data
            $student->update([
                'qr_code_data' => $qrCodeData,
                'qr_code_image_path' => $qrCodeFilePath,
            ]);
        });
    }
}
