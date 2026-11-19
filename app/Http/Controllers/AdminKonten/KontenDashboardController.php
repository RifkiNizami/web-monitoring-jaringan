<?php

namespace App\Http\Controllers\AdminKonten;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\LandingClient;
use Illuminate\View\View;

class KontenDashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama admin konten.
     */
    public function index(): View
    {
        $totalClients = LandingClient::count();
        $activeClients = LandingClient::where('is_active', true)->count();
        $latestClients = LandingClient::latest('updated_at')->take(6)->get();

        $totalAnnouncements = Announcement::count();
        $activeAnnouncements = Announcement::where('status', 'aktif')->count();
        $latestAnnouncements = Announcement::with('creator')->latest()->take(5)->get();

        $lastUpdatedClient = LandingClient::latest('updated_at')->first();
        $lastUpdatedAnnouncement = Announcement::latest('updated_at')->first();
        $mobileAppDownloadUrl = app_setting('mobile_app_download_url', 'https://netfly.id/download/netfly-mobile.apk');

        return view('admin-konten.konten_dashboard', compact(
            'totalClients',
            'activeClients',
            'latestClients',
            'totalAnnouncements',
            'activeAnnouncements',
            'latestAnnouncements',
            'lastUpdatedClient',
            'lastUpdatedAnnouncement',
            'mobileAppDownloadUrl'
        ));
    }
}
