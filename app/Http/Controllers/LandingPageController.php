<?php

namespace App\Http\Controllers;

use App\Models\LandingClient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function index(): View
    {
        $clients = LandingClient::where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $stats = [
            ['value' => '99.9%', 'label' => 'Network Visibility', 'desc' => 'Visibilitas total infrastruktur'],
            ['value' => '< 1s', 'label' => 'Real-Time Monitoring', 'desc' => 'Deteksi latency & downtime'],
            ['value' => '100+', 'label' => 'Centralized Dashboard', 'desc' => 'Integrasi node & server'],
            ['value' => '5x', 'label' => 'Faster Incident Response', 'desc' => 'Penanganan masalah lebih cepat'],
        ];

        $features = [
            [
                'icon' => 'activity',
                'title' => 'Real-Time Network Monitoring',
                'description' => 'Pantau status jaringan dan layanan secara langsung melalui dashboard monitoring yang diperbarui setiap detik.'
            ],
            [
                'icon' => 'gauge',
                'title' => 'Network Performance',
                'description' => 'Lihat performa jaringan, bandwidth, latency, packet loss, dan metrik penting lainnya secara mendalam.'
            ],
            [
                'icon' => 'server',
                'title' => 'Service Status',
                'description' => 'Tampilkan status layanan secara jelas: Normal, Warning, Gangguan, hingga status Offline.'
            ],
            [
                'icon' => 'ticket',
                'title' => 'Incident & Ticket Management',
                'description' => 'Buat, kelola, dan pantau laporan gangguan jaringan secara otomatis untuk respons tim IT yang lebih cepat.'
            ],
            [
                'icon' => 'chart',
                'title' => 'Reports & Analytics',
                'description' => 'Lihat riwayat performa, uptime, tren traffic, dan buat laporan eksekutif secara berkala.'
            ],
            [
                'icon' => 'layout-dashboard',
                'title' => 'Centralized Dashboard',
                'description' => 'Semua informasi jaringan dan perangkat terintergrasi penuh dalam satu dashboard yang intuitif.'
            ],
        ];

        $steps = [
            [
                'number' => '01',
                'title' => 'Connect Your Network',
                'description' => 'Hubungkan seluruh layanan, router, switch, atau perangkat jaringan yang ingin dipantau.'
            ],
            [
                'number' => '02',
                'title' => 'Monitor Your Services',
                'description' => 'Pantau status jaringan, uptime, dan performa layanan melalui dashboard terpusat.'
            ],
            [
                'number' => '03',
                'title' => 'Detect Network Issues',
                'description' => 'Identifikasi gangguan, anomali traffic, dan perubahan status secara otomatis.'
            ],
            [
                'number' => '04',
                'title' => 'Take Action Faster',
                'description' => 'Gunakan informasi monitoring real-time dan tiket otomatis untuk membantu tim IT menangani masalah.'
            ],
        ];

        $benefits = [
            'Monitoring jaringan terpusat dalam satu platform.',
            'Informasi gangguan dipantau secara real-time dan akurat.',
            'Meningkatkan visibilitas total pada infrastruktur enterprise.',
            'Dashboard modern yang mudah digunakan oleh seluruh tim IT.',
            'Mendukung pengambilan keputusan berbasis data metrik.',
            'Data monitoring dan laporan terstruktur secara otomatis.'
        ];

        return view('landing.index', compact('stats', 'features', 'steps', 'benefits', 'clients'));
    }
}