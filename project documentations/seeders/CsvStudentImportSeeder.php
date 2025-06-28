<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class CsvStudentImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('Starting student data import from CSV files...');

        $csvFiles = [
            'Copy of MS DIRECTORY - EDEN 1-1.csv',
            'Copy of MS DIRECTORY - EDEN 2-1.csv',
            'Copy of MS DIRECTORY - EDEN 3-1.csv',
            'Copy of MS DIRECTORY - EDEN 4-1.csv',
            'Copy of MS DIRECTORY - EDMT 1-1.csv',
            'Copy of MS DIRECTORY - EDMT 2-1.csv',
            'Copy of MS DIRECTORY - EDMT 3-1.csv',
            'Copy of MS DIRECTORY - EDMT 4-1.csv',
        ];

        $dataDirectory = database_path('seeders/data');
        
        // Check if academic year exists, if not create it
        $academicYear = DB::table('academic_year')
            ->where('description', 'AY 2024-2025')
            ->first();
            
        if (!$academicYear) {
            $academicYearId = DB::table('academic_year')->insertGetId([
                'description' => 'AY 2024-2025',
                'start_date' => '2024-08-01',
                'end_date' => '2025-07-31'
            ]);
            $academicYear = DB::table('academic_year')->where('academic_year_id', $academicYearId)->first();
        }

        foreach ($csvFiles as $fileName) {
            $filePath = $dataDirectory . '/' . $fileName;

            if (!file_exists($filePath)) {
                $this->command->error("File not found, skipping: " . $fileName);
                continue;
            }

            $file = fopen($filePath, 'r');

            // Find the course/section line and the header
            $classInfo = null;
            $headerFound = false;
            while (($row = fgetcsv($file)) !== false) {
                // Check if the first cell contains course info
                if (isset($row[0]) && preg_match('/BSED\s(ENGLISH|MATHEMATICS)/i', $row[0])) {
                    $classInfo = $this->parseCourseAndSection($row[0]);
                    // The next line is the header, so we read and discard it immediately.
                    fgetcsv($file); 
                    $headerFound = true;
                    break; 
                }
            }

            if (!$headerFound || !$classInfo) {
                $this->command->error("Could not find course/header in: " . $fileName);
                fclose($file);
                continue;
            }

            while (($row = fgetcsv($file)) !== false) {
                if (count($row) < 7 || empty(array_filter($row))) {
                    continue; 
                }

                $name = $row[0];
                $studentNumber = trim($row[1]);
                $birthday = $row[2];
                $email = trim($row[3]);

                if (empty($name) || empty($studentNumber) || empty($email)) {
                    Log::warning("Skipping row due to missing essential data in {$fileName}: " . implode(',', $row));
                    continue;
                }

                $parsedName = $this->parseName($name);
                $formattedBirthday = $this->parseDate($birthday);

                DB::transaction(function () use ($parsedName, $studentNumber, $formattedBirthday, $email, $classInfo, $academicYear, $fileName, $row) {
                    try {
                        $existingUser = DB::table('user')->where('email', $email)->first();
                        $existingStudent = DB::table('student')->where('student_number', $studentNumber)->first();

                        if ($existingUser || $existingStudent) {
                            $this->command->warn("Skipping duplicate entry for email: {$email} or student number: {$studentNumber} in file {$fileName}");
                            return;
                        }

                        $userId = DB::table('user')->insertGetId([
                            'first_name' => $parsedName['first_name'],
                            'middle_initial' => $parsedName['middle_initial'],
                            'last_name' => $parsedName['last_name'],
                            'email' => $email,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $studentId = DB::table('student')->insertGetId([
                            'user_id' => $userId,
                            'student_number' => $studentNumber,
                        ]);

                        $class = DB::table('class')->where('class_name', $classInfo['course'])->first();
                        $classId = null;
                        
                        if ($class) {
                            $classId = $class->class_id;
                        } else {
                            $classId = DB::table('class')->insertGetId([
                                'class_name' => $classInfo['course'],
                                'academic_year_id' => $academicYear->academic_year_id,
                            ]);
                        }

                        DB::table('student_class')->insert([
                            'student_id' => $studentId,
                            'class_id' => $classId,
                            'academic_year_id' => $academicYear->academic_year_id,
                            'year_level' => $this->getYearLevelFromSection($classInfo['section']),
                        ]);

                    } catch (Exception $e) {
                        $this->command->error("Failed to import row for {$email}. Error: " . $e->getMessage());
                        Log::error("Failed to import from {$fileName} for row: " . implode(',', $row) . " | Error: " . $e->getMessage());
                    }
                });
            }
            fclose($file);
            $this->command->info("Finished processing: " . $fileName);
        }
        $this->command->info('All student data has been imported.');
    }

    private function parseName(string $fullName): array
    {
        $fullName = trim($fullName);
        if (empty($fullName)) {
            return ['first_name' => '', 'middle_initial' => '', 'last_name' => ''];
        }

        $parts = explode(',', $fullName);
        $lastName = trim($parts[0]);
        $firstNamePart = isset($parts[1]) ? trim($parts[1]) : '';

        $nameParts = preg_split('/\s+/', $firstNamePart, -1, PREG_SPLIT_NO_EMPTY);
        $middleInitial = '';
        
        if (count($nameParts) > 1) {
            $lastWord = end($nameParts);
            if (strlen($lastWord) === 1 || (strlen($lastWord) === 2 && substr($lastWord, -1) === '.')) {
                $middleInitial = strtoupper($lastWord[0]);
                array_pop($nameParts);
            } else {
                 $middleInitial = strtoupper(substr($lastWord, 0, 1));
                 array_pop($nameParts);
            }
        }
        $firstName = implode(' ', $nameParts);

        return [
            'first_name' => $firstName,
            'middle_initial' => $middleInitial,
            'last_name' => $lastName,
        ];
    }

    private function parseDate(?string $dateStr): ?string
    {
        if (empty(trim($dateStr))) {
            return null;
        }
        try {
            return Carbon::parse(str_replace('-', '/', $dateStr))->format('Y-m-d');
        } catch (Exception $e) {
            Log::warning("Could not parse date: '{$dateStr}'");
            return null;
        }
    }

    private function parseCourseAndSection(string $header): array
    {
        $header = trim($header);
        if (preg_match('/(BSED\s(?:ENGLISH|MATHEMATICS))\s+([\d-]+)/i', $header, $matches)) {
            return [
                'course' => trim($matches[1]),
                'section' => trim($matches[2]),
            ];
        }
        return ['course' => 'Unknown Course', 'section' => '0-0'];
    }

    private function getYearLevelFromSection(string $section): string
    {
        if (empty($section) || !str_contains($section, '-')) {
            return 'Other';
        }
        $year = $section[0];
        return match ($year) {
            '1' => 'First Year',
            '2' => 'Second Year',
            '3' => 'Third Year',
            '4' => 'Fourth Year',
            default => 'Other',
        };
    }
} 