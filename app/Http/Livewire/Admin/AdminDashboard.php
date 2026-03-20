<?php

namespace App\Http\Livewire\Admin;

use App\Services\DashboardService;
use Livewire\Component;

class AdminDashboard extends Component
{
    public $weeklyRevenueData;
    public $salesByCategoryData;
    public $recentActivities;
    public $todayStats;
    public $monthStats;
    public $bestSellers;
    public $lowStockProducts;

    protected $dashboardService;

    public function boot(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->weeklyRevenueData = $this->dashboardService->getWeeklyRevenueData();
        $this->salesByCategoryData = $this->dashboardService->getSalesByCategoryData();
        $this->recentActivities = $this->dashboardService->getRecentActivities();
        $this->todayStats = $this->dashboardService->getTodayStats();
        $this->monthStats = $this->dashboardService->getMonthStats();
        $this->bestSellers = $this->dashboardService->getTodayBestSellers();
        $this->lowStockProducts = $this->dashboardService->getLowStockProducts();
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        $this->dispatch('dataRefreshed');
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard')
            ->layout('layouts.admin');
    }
}
