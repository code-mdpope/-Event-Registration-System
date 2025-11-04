<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportController extends Controller
{
    /**
     * Export event registrations to PDF
     */
    public function exportRegistrationsPDF(Event $event)
    {
        $event->load(['registrations.user']);
        $registrations = $event->registrations()->where('status', 'approved')->with('user')->get();

        $pdf = Pdf::loadView('exports.registrations-pdf', compact('event', 'registrations'));
        return $pdf->download("registrations-{$event->title}-" . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export event registrations to CSV
     */
    public function exportRegistrationsCSV(Event $event)
    {
        $registrations = $event->registrations()
            ->where('status', 'approved')
            ->with('user')
            ->get();

        $filename = "registrations-{$event->title}-" . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($registrations) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Name', 'Email', 'Registration Date', 'Status']);
            
            // Add data
            foreach ($registrations as $registration) {
                fputcsv($file, [
                    $registration->user->name,
                    $registration->user->email,
                    $registration->registration_date->format('Y-m-d H:i:s'),
                    ucfirst($registration->status),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export event attendance list to PDF
     */
    public function exportAttendancePDF(Event $event)
    {
        $event->load(['attendances.user']);
        $attendances = $event->attendances()->with('user')->get();

        $pdf = Pdf::loadView('exports.attendance-pdf', compact('event', 'attendances'));
        return $pdf->download("attendance-{$event->title}-" . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export event attendance list to CSV
     */
    public function exportAttendanceCSV(Event $event)
    {
        $attendances = $event->attendances()->with('user')->get();

        $filename = "attendance-{$event->title}-" . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Name', 'Email', 'Checked In At']);
            
            // Add data
            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->user->name,
                    $attendance->user->email,
                    $attendance->checked_in_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate QR code for event ticket
     */
    public function generateQRCode(Registration $registration)
    {
        if ($registration->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($registration->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved registrations have tickets.');
        }

        // Generate QR code data (will be used by JavaScript to generate QR code)
        $qrData = json_encode([
            'registration_id' => $registration->id,
            'user_id' => $registration->user_id,
            'event_id' => $registration->event_id,
            'verification_code' => md5($registration->id . $registration->user_id . $registration->event_id),
        ]);

        return view('tickets.qr', compact('registration', 'qrData'));
    }
}
