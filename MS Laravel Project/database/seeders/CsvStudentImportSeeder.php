<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;
use App\Models\AcademicYear;

class CsvStudentImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting student data import from CSV files...');

        $dataDirectory = database_path('seeders/data');
        
        // Ensure academic year 2024-2025 exists using Eloquent model
        $academicYear = AcademicYear::firstOrCreate([
            'description' => '2024-2025',
            'start_date' => '2024-06-01',
            'end_date' => null
        ]);
        $academicYearId = $academicYear->academic_year_id;

        // Ensure President role and user_role assignment
        $presidentStudentNumber = '2021-00112-TG-0';
        $presidentUserId = null;
        $presidentStudentId = null;
        $presidentClassId = null;
        $presidentYearLevel = null;
        $presidentRoleId = DB::table('role')->where('role_name', 'President')->value('role_id');

        // Get all CSV files in the data directory matching the new pattern (e.g., 'EDEN 1-1.csv')
        $csvFiles = glob($dataDirectory . '/*.csv');
        if (empty($csvFiles)) {
            $this->command->info('No CSV files found in the data directory.');
            return;
        }
        foreach ($csvFiles as $filePath) {
            $fileName = basename($filePath);
            $this->command->info('Processing: ' . $fileName);
            if (!file_exists($filePath)) {
                $this->command->info('File not found, skipping: ' . $fileName);
                continue;
            }
            $file = fopen($filePath, 'r');
            // Find the course/section line and the header
            $classInfo = null;
            $headerFound = false;
            while (($row = fgetcsv($file)) !== false) {
                if (isset($row[0]) && preg_match('/BSED\s(ENGLISH|MATHEMATICS)/i', $row[0])) {
                    $classInfo = $this->parseCourseAndSection($row[0]);
                    fgetcsv($file); // skip header
                    $headerFound = true;
                    break;
                }
            }
            if (!$headerFound || !$classInfo) {
                $this->command->error("Could not find course/header in: " . $fileName);
                fclose($file);
                continue;
            }
            // Parse class name and section from file name or data
            $className = $this->getClassNameFromFile($fileName); // e.g., 'EDEN 1-1'
            $section = $this->getSectionFromFile($fileName); // e.g., '1-1'
            // Create class if not exists (Query Builder does not support firstOrCreate)
            $class = DB::table('class')
                ->where('class_name', $className)
                ->where('academic_year_id', $academicYearId)
                ->first();
            if (!$class) {
                $classId = DB::table('class')->insertGetId([
                    'class_name' => $className,
                    'academic_year_id' => $academicYearId,
                    'remarks' => null,
                    'class_president_id' => null
                ]);
                $class = DB::table('class')->where('class_id', $classId)->first();
            }
            $classId = $class->class_id;
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
                DB::transaction(function () use ($parsedName, $studentNumber, $formattedBirthday, $email, $classInfo, $academicYear, $academicYearId, $fileName, $row, $classId, $presidentStudentNumber, &$presidentUserId, &$presidentStudentId) {
                    try {
                        $existingUser = DB::table('user')->where('student_number', $studentNumber)->first();
                        $existingStudent = DB::table('student')->where('email', $email)->first();
                        if ($existingUser || $existingStudent) {
                            $this->command->warn("Skipping duplicate entry for email: {$email} or student number: {$studentNumber} in file {$fileName}");
                            return;
                        }
                        $firstName = str_replace(' ', '', ucwords(strtolower($parsedName['first_name'])));
                        $lastName = ucfirst(strtolower($parsedName['last_name']));
                        $password = Hash::make($firstName . $lastName);
                        // Insert student first (now includes email)
                        $studentId = DB::table('student')->insertGetId([
                            'last_name' => $parsedName['last_name'],
                            'first_name' => $parsedName['first_name'],
                            'middle_initial' => $parsedName['middle_initial'],
                            'student_number' => $studentNumber,
                            'email' => $email,
                            'course' => $classInfo['course'],
                            'year_level' => $this->getYearLevelFromSection($classInfo['section']),
                            'section' => $classInfo['section'],
                            'academic_status' => 'active',
                        ]);
                        // Insert user (no email)
                        DB::table('user')->insert([
                            'student_number' => $studentNumber,
                            'password' => $password,
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        // Insert into student_class (use student_number, not student_id)
                        DB::table('student_class')->insert([
                            'student_number' => $studentNumber,
                            'class_id' => $classId,
                            'academic_year_id' => $academicYearId,
                            'year_level' => $this->getYearLevelFromSection($classInfo['section']),
                        ]);
                        // Track president info for later
                        if ($studentNumber === $presidentStudentNumber) {
                            $presidentUserId = $studentNumber;
                            $presidentStudentId = $studentNumber;
                        }
                        // Example: Insert a transaction for this student (ensure student_number is always included)
                        // DB::table('transaction')->insert([
                        //     'student_number' => $studentNumber,
                        //     'type_id' => $someTypeId,
                        //     'amount' => $someAmount,
                        //     'transaction_date' => now(),
                        //     'recorded_by' => $officerStudentNumber, // if needed
                        //     'remarks' => 'Payment for FRA',
                        // ]);
                    } catch (Exception $e) {
                        $this->command->error("Failed to import row for {$email}. Error: " . $e->getMessage());
                        Log::error("Failed to import from {$fileName} for row: " . implode(',', $row) . " | Error: " . $e->getMessage());
                    }
                });
            }
            fclose($file);
            $this->command->info("Finished processing: " . $fileName);
        }
        // When assigning org president role, use transferPresidency
        if ($presidentStudentNumber) {
            $academicYearId = $academicYear->academic_year_id;
            $transferDate = '2024-06-01'; // Or set dynamically if needed
            $this->transferPresidency($presidentStudentNumber, $presidentRoleId, $academicYearId, $transferDate);
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

    private function getClassNameFromFile(string $fileName): string
    {
        // Return the base name without extension (e.g., 'EDEN 1-1')
        return pathinfo($fileName, PATHINFO_FILENAME);
    }

    private function getSectionFromFile(string $fileName): string
    {
        // Extract the section (e.g., '1-1') from the file name
        $base = pathinfo($fileName, PATHINFO_FILENAME);
        if (preg_match('/(\d-\d)$/', $base, $matches)) {
            return $matches[1];
        }
        return '';
    }

    /**
     * Assign the President role to a user, handling presidency transfer.
     * If a President already exists for the academic year, set their end_date to the transfer date.
     * Then assign the new President with the given start_date.
     */
    private function transferPresidency($newPresidentStudentNumber, $roleId, $academicYearId, $transferDate)
    {
        // Find the current president for this academic year (if any, and not ended)
        $currentPresident = DB::table('user_role')
            ->where('role_id', $roleId)
            ->where('academic_year_id', $academicYearId)
            ->whereNull('end_date')
            ->first();
        if ($currentPresident) {
            // Set the end_date to the transfer date
            DB::table('user_role')
                ->where('user_role_id', $currentPresident->user_role_id)
                ->update(['end_date' => $transferDate]);
        }
        // Assign the new president
        DB::table('user_role')->insert([
            'student_number' => $newPresidentStudentNumber,
            'role_id' => $roleId,
            'academic_year_id' => $academicYearId,
            'start_date' => $transferDate,
            'end_date' => null
        ]);
    }
} 