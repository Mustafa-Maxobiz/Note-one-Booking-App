<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function index()
    {
        return view('admin.export.index');
    }

    public function exportBookings(Request $request)
    {
        $format = $request->get('format', 'csv');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $status = $request->get('status');

        $query = Booking::with(['teacher.user', 'student.user']);

        if ($dateFrom) {
            $query->whereDate('start_time', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('start_time', '<=', $dateTo);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->get();

        $data = [];
        foreach ($bookings as $booking) {
            $data[] = [
                'ID' => $booking->id,
                'Teacher' => $booking->teacher && $booking->teacher->user ? $booking->teacher->user->name : 'N/A',
                'Student' => $booking->student && $booking->student->user ? $booking->student->user->name : 'N/A',
                'Date' => $booking->start_time->format('Y-m-d'),
                'Time' => $booking->start_time->format('H:i'),
                'Duration' => $booking->duration_minutes . ' min',
                'Status' => ucfirst($booking->status),
                'Price' => '$' . number_format($booking->price, 2),
                'Created' => $booking->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $this->exportData($data, 'bookings', $format);
    }

    public function exportUsers(Request $request)
    {
        $format = $request->get('format', 'csv');
        $role = $request->get('role');

        $query = User::with(['teacher', 'student']);

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->get();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Role' => ucfirst($user->role),
                'Created' => $user->created_at->format('Y-m-d H:i:s'),
                'Status' => $user->email_verified_at ? 'Verified' : 'Pending',
            ];
        }

        return $this->exportData($data, 'users', $format);
    }

    public function exportPayments(Request $request)
    {
        $format = $request->get('format', 'csv');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Payment::with(['student.user', 'teacher.user']);

        if ($status) {
            $query->where('status', $status);
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $payments = $query->get();

        $data = [];
        foreach ($payments as $payment) {
            $data[] = [
                'ID' => $payment->id,
                'Student' => $payment->student && $payment->student->user ? $payment->student->user->name : 'N/A',
                'Teacher' => $payment->teacher && $payment->teacher->user ? $payment->teacher->user->name : 'N/A',
                'Amount' => '$' . number_format($payment->amount, 2),
                'Status' => ucfirst($payment->status),
                'Method' => ucfirst($payment->payment_method),
                'Transaction ID' => $payment->transaction_id,
                'Date' => $payment->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $this->exportData($data, 'payments', $format);
    }

    public function exportFeedback(Request $request)
    {
        $format = $request->get('format', 'csv');
        $type = $request->get('type');

        $query = Feedback::with(['booking.teacher.user', 'booking.student.user']);

        if ($type) {
            $query->where('type', $type);
        }

        $feedbacks = $query->get();

        $data = [];
        foreach ($feedbacks as $feedback) {
            $data[] = [
                'ID' => $feedback->id,
                'Teacher' => $feedback->booking && $feedback->booking->teacher && $feedback->booking->teacher->user ? $feedback->booking->teacher->user->name : 'N/A',
                'Student' => $feedback->booking && $feedback->booking->student && $feedback->booking->student->user ? $feedback->booking->student->user->name : 'N/A',
                'Rating' => $feedback->rating . '/5',
                'Comment' => $feedback->comment,
                'Type' => ucfirst(str_replace('_', ' ', $feedback->type)),
                'Public' => $feedback->is_public ? 'Yes' : 'No',
                'Date' => $feedback->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $this->exportData($data, 'feedback', $format);
    }

    private function exportData($data, $filename, $format)
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "{$filename}_{$timestamp}";

        if ($format === 'csv') {
            return $this->exportToCsv($data, $filename);
        } elseif ($format === 'json') {
            return $this->exportToJson($data, $filename);
        } elseif ($format === 'excel') {
            return $this->exportToExcel($data, $filename);
        }

        return back()->with('error', 'Invalid export format.');
    }

    private function exportToCsv($data, $filename)
    {
        if (empty($data)) {
            return back()->with('error', 'No data to export.');
        }

        $headers = array_keys($data[0]);
        $csv = fopen('php://temp', 'r+');
        
        // Add headers
        fputcsv($csv, $headers);
        
        // Add data
        foreach ($data as $row) {
            fputcsv($csv, $row);
        }
        
        rewind($csv);
        $csvContent = stream_get_contents($csv);
        fclose($csv);

        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ]);
    }

    private function exportToJson($data, $filename)
    {
        return Response::make(json_encode($data, JSON_PRETTY_PRINT), 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}.json\"",
        ]);
    }

    private function exportToExcel($data, $filename)
    {
        // For Excel export, we'll use CSV format with .xls extension
        // In a real application, you might want to use a library like PhpSpreadsheet
        return $this->exportToCsv($data, $filename . '.xls');
    }
}
