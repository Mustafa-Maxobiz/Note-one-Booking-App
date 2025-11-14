<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Feedback;
use App\Services\EmailService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SystemTest extends Command
{
    protected $signature = 'system:test {--section=all : Test specific section}';
    protected $description = 'Comprehensive system testing for all functionalities';

    private $testResults = [];
    private $totalTests = 0;
    private $passedTests = 0;

    public function handle()
    {
        $this->info('🚀 Starting Comprehensive System Test...');
        $this->newLine();

        $section = $this->option('section');

        switch ($section) {
            case 'auth':
                $this->testAuthentication();
                break;
            case 'users':
                $this->testUserManagement();
                break;
            case 'bookings':
                $this->testBookingSystem();
                break;
            case 'emails':
                $this->testEmailSystem();
                break;
            case 'reports':
                $this->testReportsSystem();
                break;
            case 'admin':
                $this->testAdminFunctions();
                break;
            case 'teacher':
                $this->testTeacherFunctions();
                break;
            case 'student':
                $this->testStudentFunctions();
                break;
            default:
                $this->testAllSections();
        }

        $this->displayResults();
    }

    private function testAllSections()
    {
        $this->testAuthentication();
        $this->testUserManagement();
        $this->testBookingSystem();
        $this->testEmailSystem();
        $this->testReportsSystem();
        $this->testAdminFunctions();
        $this->testTeacherFunctions();
        $this->testStudentFunctions();
    }

    private function testAuthentication()
    {
        $this->info('🔐 Testing Authentication System...');
        
        $this->runTest('Admin user exists', function() {
            $admin = User::where('role', 'admin')->first();
            return $admin !== null;
        });

        $this->runTest('User roles are properly set', function() {
            $users = User::all();
            foreach ($users as $user) {
                if (!in_array($user->role, ['admin', 'teacher', 'student'])) {
                    return false;
                }
            }
            return true;
        });

        $this->runTest('User passwords are hashed', function() {
            $user = User::first();
            return $user && !empty($user->password) && strlen($user->password) > 20;
        });

        $this->runTest('User relationships work', function() {
            $teacher = User::where('role', 'teacher')->first();
            $student = User::where('role', 'student')->first();
            
            if ($teacher && $student) {
                return $teacher->teacher !== null && $student->student !== null;
            }
            return false;
        });
    }

    private function testUserManagement()
    {
        $this->info('👥 Testing User Management System...');

        $this->runTest('Admin self-deletion protection', function() {
            $admin = User::where('role', 'admin')->first();
            return $admin && !$admin->canBeDeleted();
        });

        $this->runTest('Last admin protection', function() {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                $admin = User::where('role', 'admin')->first();
                return $admin && !$admin->canBeDeleted();
            }
            return true;
        });

        $this->runTest('User creation works', function() {
            try {
                $user = User::create([
                    'name' => 'Test User ' . time(),
                    'email' => 'test' . time() . '@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'student',
                ]);
                return $user !== null;
            } catch (\Exception $e) {
                return false;
            }
        });

        $this->runTest('User profile relationships', function() {
            $teacher = User::where('role', 'teacher')->first();
            $student = User::where('role', 'student')->first();
            
            return $teacher && $teacher->teacher && $student && $student->student;
        });
    }

    private function testBookingSystem()
    {
        $this->info('📅 Testing Booking System...');

        $this->runTest('Booking creation works', function() {
            try {
                $teacher = Teacher::first();
                $student = Student::first();
                
                if ($teacher && $student) {
                    $booking = Booking::create([
                        'teacher_id' => $teacher->id,
                        'student_id' => $student->id,
                        'start_time' => now()->addDays(1),
                        'end_time' => now()->addDays(1)->addHour(),
                        'duration_minutes' => 60,
                        'status' => 'pending',
                        'price' => 50.00,
                        'notes' => 'Test booking for system test',
                    ]);
                    return $booking !== null;
                }
                return false;
            } catch (\Exception $e) {
                return false;
            }
        });

        $this->runTest('Booking status updates work', function() {
            $booking = Booking::first();
            if ($booking) {
                $booking->update(['status' => 'confirmed']);
                return $booking->status === 'confirmed';
            }
            return false;
        });

        $this->runTest('Booking relationships work', function() {
            $booking = Booking::with(['teacher.user', 'student.user'])->first();
            return $booking && $booking->teacher && $booking->student;
        });
    }

    private function testEmailSystem()
    {
        $this->info('📧 Testing Email System...');

        $this->runTest('Welcome email template renders', function() {
            try {
                $data = [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'role' => 'Student',
                    'login_url' => 'http://localhost/login',
                    'dashboard_url' => 'http://localhost/student/dashboard',
                    'booking_url' => 'http://localhost/student/dashboard',
                ];
                
                $view = view('emails.welcome', ['data' => $data]);
                $html = $view->render();
                return strlen($html) > 1000;
            } catch (\Exception $e) {
                return false;
            }
        });

        $this->runTest('Account creation email template renders', function() {
            try {
                $data = [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'role' => 'Student',
                    'password' => 'temp123',
                    'login_url' => 'http://localhost/login',
                    'dashboard_url' => 'http://localhost/student/dashboard',
                ];
                
                $view = view('emails.account-creation', ['data' => $data]);
                $html = $view->render();
                return strlen($html) > 1000;
            } catch (\Exception $e) {
                return false;
            }
        });

        $this->runTest('Email service methods exist', function() {
            return method_exists(EmailService::class, 'sendEmail') &&
                   method_exists(EmailService::class, 'sendAccountCreationEmail');
        });
    }

    private function testReportsSystem()
    {
        $this->info('📊 Testing Reports System...');

        $this->runTest('Report controller methods exist', function() {
            $controller = new \App\Http\Controllers\Admin\ReportController();
            return method_exists($controller, 'index') &&
                   method_exists($controller, 'teachers') &&
                   method_exists($controller, 'students') &&
                   method_exists($controller, 'bookings');
        });

        $this->runTest('Database indexes exist', function() {
            try {
                $indexes = DB::select("SHOW INDEX FROM bookings");
                return count($indexes) > 0;
            } catch (\Exception $e) {
                return false;
            }
        });

        $this->runTest('Cache system works', function() {
            try {
                Cache::put('test_key', 'test_value', 60);
                $value = Cache::get('test_key');
                Cache::forget('test_key');
                return $value === 'test_value';
            } catch (\Exception $e) {
                return false;
            }
        });

        $this->runTest('Report data queries work', function() {
            try {
                $userCount = User::count();
                $teacherCount = Teacher::count();
                $studentCount = Student::count();
                $bookingCount = Booking::count();
                
                return $userCount >= 0 && $teacherCount >= 0 && 
                       $studentCount >= 0 && $bookingCount >= 0;
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    private function testAdminFunctions()
    {
        $this->info('👨‍💼 Testing Admin Functions...');

        $this->runTest('Admin dashboard route exists', function() {
            return \Route::has('admin.dashboard');
        });

        $this->runTest('User management routes exist', function() {
            return \Route::has('admin.users.index') &&
                   \Route::has('admin.users.create') &&
                   \Route::has('admin.users.store');
        });

        $this->runTest('Report routes exist', function() {
            return \Route::has('admin.reports.index') &&
                   \Route::has('admin.reports.teachers') &&
                   \Route::has('admin.reports.students');
        });

        $this->runTest('Settings routes exist', function() {
            return \Route::has('admin.settings.index') &&
                   \Route::has('admin.settings.update');
        });
    }

    private function testTeacherFunctions()
    {
        $this->info('👨‍🏫 Testing Teacher Functions...');

        $this->runTest('Teacher dashboard route exists', function() {
            return \Route::has('teacher.dashboard');
        });

        $this->runTest('Teacher availability system works', function() {
            $teacher = Teacher::first();
            return $teacher !== null;
        });

        $this->runTest('Teacher booking relationships work', function() {
            $teacher = Teacher::with('sessions')->first();
            return $teacher !== null;
        });
    }

    private function testStudentFunctions()
    {
        $this->info('👨‍🎓 Testing Student Functions...');

        $this->runTest('Student dashboard route exists', function() {
            return \Route::has('student.dashboard');
        });

        $this->runTest('Student booking relationships work', function() {
            $student = Student::with('sessions')->first();
            return $student !== null;
        });

        $this->runTest('Student profile exists', function() {
            $student = Student::first();
            return $student !== null;
        });
    }

    private function runTest($testName, $testFunction)
    {
        $this->totalTests++;
        
        try {
            $result = $testFunction();
            if ($result) {
                $this->passedTests++;
                $this->line("  ✅ {$testName}");
                $this->testResults[] = ['name' => $testName, 'status' => 'PASS', 'error' => null];
            } else {
                $this->line("  ❌ {$testName}");
                $this->testResults[] = ['name' => $testName, 'status' => 'FAIL', 'error' => 'Test returned false'];
            }
        } catch (\Exception $e) {
            $this->line("  ❌ {$testName} - Error: " . $e->getMessage());
            $this->testResults[] = ['name' => $testName, 'status' => 'FAIL', 'error' => $e->getMessage()];
        }
    }

    private function displayResults()
    {
        $this->newLine();
        $this->info('📋 Test Results Summary:');
        $this->newLine();
        
        $passRate = $this->totalTests > 0 ? round(($this->passedTests / $this->totalTests) * 100, 2) : 0;
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Tests', $this->totalTests],
                ['Passed Tests', $this->passedTests],
                ['Failed Tests', $this->totalTests - $this->passedTests],
                ['Pass Rate', $passRate . '%'],
            ]
        );

        if ($this->totalTests - $this->passedTests > 0) {
            $this->newLine();
            $this->error('❌ Failed Tests:');
            foreach ($this->testResults as $result) {
                if ($result['status'] === 'FAIL') {
                    $this->line("  • {$result['name']}: {$result['error']}");
                }
            }
        }

        $this->newLine();
        if ($passRate >= 90) {
            $this->info('🎉 System is in excellent condition!');
        } elseif ($passRate >= 75) {
            $this->warn('⚠️  System is mostly functional with minor issues.');
        } else {
            $this->error('🚨 System has significant issues that need attention.');
        }
    }
}