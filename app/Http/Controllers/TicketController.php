<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Tampilkan halaman antrean tiket komplain klien & insiden.
     */
    public function index(Request $request): View
    {
        // 6 Summary Metrics
        $summary = [
            'all' => 4,
            'open' => 1,
            'assigned' => 1,
            'in_progress' => 1,
            'resolved' => 1,
            'closed' => 0,
        ];

        // Daftar teknisi lapangan NOC
        $technicians = [
            'Budi Santoso (Field Tech JKT)',
            'Ahmad Rifai (Backbone Specialist)',
            'Doni Prasetyo (NOC L2 Support)',
            'Rian Hermawan (Field Tech BKS)',
            'Fikri Ramadhan (Fiber Splicer)',
        ];

        // Daftar status penanganan
        $statusOptions = [
            '1. Open (Baru)',
            '2. Assigned (Ditugaskan)',
            '3. In Progress (Sedang Dikerjakan)',
            '4. Resolved (Terselesaikan)',
            '5. Closed (Ditutup)',
        ];

        // Data 4 antrean tiket sesuai desain Figma
        $tickets = [
            [
                'id' => 'INC-2026-1024',
                'priority' => 'P2 - HIGH',
                'priority_level' => 'high',
                'client_name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'project_name' => 'Smart City Backbone Sekolah Tahap II',
                'node_code' => 'AP-OUTDOOR-DISDIK-01',
                'subject' => 'Packet Loss Tinggi (>14%) Backbone Disdik Jakarta Pusat',
                'description' => 'Akses koneksi Internet di lokasi hub pusat mengalami degradasi kualitas dan loss...',
                'assigned_technician' => null,
                'is_tech_warning' => false,
                'status' => '3. In Progress (Sedang Dikerjakan)',
                'status_type' => 'in_progress',
                'status_note' => null,
                'report_date' => '10 Okt 2026',
                'report_time' => '04:06 WIB',
            ],
            [
                'id' => 'INC-2026-1030',
                'priority' => 'P1 - CRITICAL',
                'priority_level' => 'critical',
                'client_name' => 'PT. Shopee International Indonesia',
                'project_name' => 'Fulfillment Center Warehouse Cikarang',
                'node_code' => 'SW-AGG-BKS-02',
                'subject' => 'Switch Agg-Bks_04 Terputus (Site Warehouse Bekasi)',
                'description' => 'Unit switch padam total, tim teknisi lapangan sedang menuju lokasi untuk pengece...',
                'assigned_technician' => null,
                'is_tech_warning' => false,
                'status' => '2. Assigned (Ditugaskan)',
                'status_type' => 'assigned',
                'status_note' => null,
                'report_date' => '10 Okt 2026',
                'report_time' => '04:06 WIB',
            ],
            [
                'id' => 'REQ-2026-0882',
                'priority' => 'P3 - MEDIUM',
                'priority_level' => 'medium',
                'client_name' => 'PT. Shopee International Indonesia',
                'project_name' => 'Dedicated Internet SCBD Hub',
                'node_code' => null,
                'subject' => 'Permintaan Bandwidth On-Demand (+500 Mbps) Flash Sale',
                'description' => 'Permintaan alokasi bandwidth sementara selama periode kampanye belanja online ak...',
                'assigned_technician' => null,
                'is_tech_warning' => true,
                'status' => '1. Open (Baru)',
                'status_type' => 'open',
                'status_note' => null,
                'report_date' => '10 Okt 2026',
                'report_time' => '04:06 WIB',
            ],
            [
                'id' => 'INC-2026-0955',
                'priority' => 'P2 - HIGH',
                'priority_level' => 'high',
                'client_name' => 'PT. Bank Mandiri (Persero) Tbk',
                'project_name' => 'MPLS Core Interconnect Plaza Mandiri',
                'node_code' => 'RTR-CORE-MANDIRI-01',
                'subject' => 'High Latency Spike (>120ms) Core Link Plaza Mandiri',
                'description' => 'Terjadi fluktuasi latensi pada link utama. Rute telah berhasil distabilkan denga...',
                'assigned_technician' => null,
                'is_tech_warning' => false,
                'status' => '4. Resolved (Terselesaikan)',
                'status_type' => 'resolved',
                'status_note' => 'Selesai: 10/10 02:00',
                'report_date' => '10 Okt 2026',
                'report_time' => '04:06 WIB',
            ],
        ];

        return view('admin.tickets', compact('summary', 'technicians', 'statusOptions', 'tickets'));
    }
}
