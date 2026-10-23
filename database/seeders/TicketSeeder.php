<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = [
            [
                'id' => 1,
                'ticket_number' => 'INC-2026-1024',
                'customer_id' => 3,
                'project_id' => 1,
                'device_id' => 3,
                'client_name' => 'Dinas Pendidikan Provinsi DKI Jakarta',
                'project_name' => 'Smart City Backbone Sekolah Tahap II',
                'node_code' => 'AP-OUTDOOR-DISDIK-01',
                'priority' => 'P2 - HIGH',
                'priority_level' => 'high',
                'subject' => 'Packet Loss Tinggi (>14%) Backbone Disdik Jakarta Pusat',
                'description' => 'Akses koneksi Internet di lokasi hub pusat mengalami degradasi kualitas dan loss tinggi.',
                'assigned_technician' => null,
                'is_tech_warning' => false,
                'status' => '3. In Progress (Sedang Dikerjakan)',
                'status_type' => 'in_progress',
                'status_note' => null,
                'reported_at' => '2026-10-10 04:06:00',
                'resolved_at' => null,
            ],
            [
                'id' => 2,
                'ticket_number' => 'INC-2026-1030',
                'customer_id' => 2,
                'project_id' => 3,
                'device_id' => 2,
                'client_name' => 'PT. Shopee International Indonesia',
                'project_name' => 'Fulfillment Center Warehouse Cikarang',
                'node_code' => 'SW-AGG-BKS-02',
                'priority' => 'P1 - CRITICAL',
                'priority_level' => 'critical',
                'subject' => 'Switch Agg-Bks_04 Terputus (Site Warehouse Bekasi)',
                'description' => 'Unit switch padam total, tim teknisi lapangan sedang menuju lokasi untuk pengecekan fisik.',
                'assigned_technician' => null,
                'is_tech_warning' => false,
                'status' => '2. Assigned (Ditugaskan)',
                'status_type' => 'assigned',
                'status_note' => null,
                'reported_at' => '2026-10-10 04:06:00',
                'resolved_at' => null,
            ],
            [
                'id' => 3,
                'ticket_number' => 'REQ-2026-0882',
                'customer_id' => 2,
                'project_id' => 2,
                'device_id' => null,
                'client_name' => 'PT. Shopee International Indonesia',
                'project_name' => 'Dedicated Internet SCBD Hub',
                'node_code' => null,
                'priority' => 'P3 - MEDIUM',
                'priority_level' => 'medium',
                'subject' => 'Permintaan Bandwidth On-Demand (+500 Mbps) Flash Sale',
                'description' => 'Permintaan alokasi bandwidth sementara selama periode kampanye belanja online akbar.',
                'assigned_technician' => null,
                'is_tech_warning' => true,
                'status' => '1. Open (Baru)',
                'status_type' => 'open',
                'status_note' => null,
                'reported_at' => '2026-10-10 04:06:00',
                'resolved_at' => null,
            ],
            [
                'id' => 4,
                'ticket_number' => 'INC-2026-0955',
                'customer_id' => 1,
                'project_id' => 5,
                'device_id' => 1,
                'client_name' => 'PT. Bank Mandiri (Persero) Tbk',
                'project_name' => 'MPLS Core Interconnect Plaza Mandiri',
                'node_code' => 'RTR-CORE-MANDIRI-01',
                'priority' => 'P2 - HIGH',
                'priority_level' => 'high',
                'subject' => 'High Latency Spike (>120ms) Core Link Plaza Mandiri',
                'description' => 'Terjadi fluktuasi latensi pada link utama. Rute telah berhasil distabilkan dengan backup link.',
                'assigned_technician' => null,
                'is_tech_warning' => false,
                'status' => '4. Resolved (Terselesaikan)',
                'status_type' => 'resolved',
                'status_note' => 'Selesai: 10/10 02:00',
                'reported_at' => '2026-10-10 04:06:00',
                'resolved_at' => '2026-10-10 02:00:00',
            ],
        ];

        foreach ($tickets as $data) {
            Ticket::updateOrCreate(['ticket_number' => $data['ticket_number']], $data);
        }
    }
}
