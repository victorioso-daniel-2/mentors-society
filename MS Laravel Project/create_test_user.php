<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Creating test user...\n";

try {
    // Create a test student
    $studentNumber = '2021-00001-TEST';
    $firstName = 'Test';
    $lastName = 'User';
    $email = 'test@example.com';
    
    // Check if user already exists
    $existingUser = DB::table('user')->where('student_number', $studentNumber)->first();
    $existingStudent = DB::table('student')->where('student_number', $studentNumber)->first();
    
    if ($existingUser || $existingStudent) {
        echo "Test user already exists!\n";
        echo "Student Number: {$studentNumber}\n";
        echo "Password: {$firstName}{$lastName}\n";
        echo "Email: {$email}\n";
        exit;
    }
    
    // Create student record
    DB::table('student')->insert([
        'last_name' => $lastName,
        'first_name' => $firstName,
        'middle_initial' => 'T',
        'student_number' => $studentNumber,
        'email' => $email,
        'course' => 'BSED ENGLISH',
        'year_level' => '1st Year',
        'section' => '1-1',
        'academic_status' => 'active',
    ]);
    
    // Create user record with password
    $password = Hash::make($firstName . $lastName);
    DB::table('user')->insert([
        'student_number' => $studentNumber,
        'password' => $password,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Test user created successfully!\n";
    echo "Student Number: {$studentNumber}\n";
    echo "Password: {$firstName}{$lastName}\n";
    echo "Email: {$email}\n";
    echo "\nYou can now use these credentials to test the login functionality.\n";
    
} catch (Exception $e) {
    echo "Error creating test user: " . $e->getMessage() . "\n";
} 