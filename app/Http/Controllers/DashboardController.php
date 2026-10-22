<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard user.
     */
    public function index(): View
    {
        $metrics = [
            'customer_active' => [
                'title' => 'CUSTOMER AKTIF',
                'badge' => 'Terverifikasi',
                'value' => '1, 248',
                'subvalue' => '+12 bln ini',
                'footer_left' => 'Corporate & Retail',
                'footer_right' => '99.4% Aktif',
            ],
            'project_active' => [
                'title' => 'PROJECT AKTIF',
                'badge' => 'On Track',
                'value' => '42',
                'subvalue' => 'Instalasi Baru',
                'footer_left' => 'Target Selesai',
                'footer_right' => '5 Minggu Ini',
            ],
            'device_online' => [
                'title' => 'PERANGKAT ONLINE',
                'badge' => 'Online',
                'value' => '8, 432',
                'subvalue' => '/ 8,444 unit',
                'footer_left' => 'Ketersediaan Fisik',
                'footer_right' => '99.86% Sehat',
            ],
            'device_offline' => [
                'title' => 'PERANGKAT OFFLINE',
                'badge' => 'Offline',
                'value' => '12',
                'subvalue' => 'Unit Padam',
                'footer_left' => 'Status Tindakan',
                'footer_right' => 'Investigasi Aktif',
            ],
        ];

        $alerts = [
            [
                'badge' => 'PADAM',
                'badge_type' => 'danger',
                'time' => '10:42:32 WIB',
                'title' => 'Switch Agg-Bks_04 Terputus',
                'desc' => 'Koneksi padam di POP Bekasi. Indikasi kehilangan catu daya listrik AC.',
            ],
            [
                'badge' => 'BEBAN TINGGI',
                'badge_type' => 'warning',
                'time' => '10:45:00 WIB',
                'title' => 'Router Core-East_01 CPU 92%',
                'desc' => 'Utilisasi CPU melebihi ambang batas aman selama lebih dari 5 menit.',
            ],
            [
                'badge' => 'ANOMALI',
                'badge_type' => 'info',
                'time' => '09:32:45 WIB',
                'title' => 'Backbone JKT-SBY Latensi Naik',
                'desc' => 'RTT terdeteksi 120ms (Batas Normal transmisi: ≤15ms).',
            ],
        ];

        $tickets = [
            [
                'id' => 'INC-2026-1023',
                'customer' => 'PT. XYZ Teknologi',
                'package' => 'Dedicated Fiber 1Gbps',
                'issue' => 'Link Down (Kabel Putus / FO Cut)',
                'priority' => 'P1 - Critical',
                'priority_type' => 'critical',
                'status' => 'Menunggu Dispatch',
                'status_color' => 'amber',
                'duration' => '45m 12s',
                'action_label' => 'Tangani',
                'action_type' => 'primary',
            ],
            [
                'id' => 'INC-2026-1024',
                'customer' => 'Dinas Pendidikan Kota',
                'package' => 'Project Smart City Jaringan FO',
                'issue' => 'Packet Loss Tinggi (>15%)',
                'priority' => 'P2 - High',
                'priority_type' => 'high',
                'status' => 'Dianalisis NOC',
                'status_color' => 'blue',
                'duration' => '12m 05s',
                'action_label' => 'Detail',
                'action_type' => 'outline',
            ],
            [
                'id' => 'REQ-2026-0882',
                'customer' => 'RS Medika Center',
                'package' => 'Dedicated Internet Broadband',
                'issue' => 'Permintaan Bandwidth On-Demand (+50M)',
                'priority' => 'P3 - Medium',
                'priority_type' => 'medium',
                'status' => 'Menunggu Persetujuan',
                'status_color' => 'slate',
                'duration' => '2j 15m',
                'action_label' => 'Proses',
                'action_type' => 'outline',
            ],
        ];

        return view('admin.dashboard', compact('metrics', 'alerts', 'tickets'));
    }
}
