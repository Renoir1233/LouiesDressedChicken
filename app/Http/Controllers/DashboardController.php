<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent orders
        $recentOrders = Order::with('items')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Get low stock items - using your actual scope
        $lowStockItems = Inventory::lowStock()
            ->orderBy('quantity', 'asc')
            ->limit(5)
            ->get();
            
        // Get out of stock items - using your actual scope
        $outOfStockItems = Inventory::outOfStock()
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
            
        // Get top selling products
        $topProducts = $this->getTopSellingProducts();
            
        // Get sales data for chart (last 7 days)
        $salesData = $this->getSalesChartData();
        
        // Get payment summary
        $paymentSummary = $this->getPaymentSummary();
        
        return view('dashboard.index', compact(
            'stats', 
            'recentOrders', 
            'lowStockItems', 
            'outOfStockItems',
            'topProducts',
            'salesData',
            'paymentSummary'
        ));
    }
    
    private function getDashboardStats()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        return [
            // Sales Statistics
            'totalSales' => Order::where('status', 'completed')->sum('total') ?? 0,
            'todaySales' => Order::where('status', 'completed')->whereDate('created_at', $today)->sum('total') ?? 0,
            'weekSales' => Order::where('status', 'completed')->where('created_at', '>=', $weekStart)->sum('total') ?? 0,
            'monthSales' => Order::where('status', 'completed')->where('created_at', '>=', $monthStart)->sum('total') ?? 0,
            
            // Order Statistics
            'totalOrders' => Order::count() ?? 0,
            'pendingOrders' => Order::where('status', 'pending')->count() ?? 0,
            'completedOrders' => Order::where('status', 'completed')->count() ?? 0,
            'partialOrders' => Order::where('status', 'partial')->count() ?? 0,
            'cancelledOrders' => Order::where('status', 'cancelled')->count() ?? 0,
            
            // Inventory Statistics
            'lowStockCount' => Inventory::lowStock()->count() ?? 0,
            'outOfStockCount' => Inventory::outOfStock()->count() ?? 0,
            'totalProducts' => Inventory::count() ?? 0,
            'activeProducts' => Inventory::where('is_active', true)->count() ?? 0,
            
            // Business Statistics
            'totalSuppliers' => Supplier::count() ?? 0,
            'totalEmployees' => Employee::count() ?? 0,
            'activeEmployees' => Employee::where('status', 'Active')->count() ?? 0,
            
            // Payment Statistics
            'totalPayments' => Payment::count() ?? 0,
            'todayPayments' => Payment::whereDate('payment_date', $today)->count() ?? 0,
            'totalCashCollected' => Payment::where('payment_method', 'cash')->sum('amount') ?? 0,
        ];
    }
    
    private function getSalesChartData()
    {
        $salesData = ['labels' => [], 'data' => []];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $sales = Order::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('total');
                
            $salesData['labels'][] = now()->subDays($i)->format('M j');
            $salesData['data'][] = $sales ?? 0;
        }
        
        return $salesData;
    }
    
    private function getTopSellingProducts()
    {
        return \DB::table('order_items')
            ->join('inventory', 'order_items.inventory_id', '=', 'inventory.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select(
                'inventory.product_name',
                'inventory.product_code',
                \DB::raw('SUM(order_items.quantity) as total_quantity'),
                \DB::raw('SUM(order_items.total) as total_revenue')
            )
            ->groupBy('inventory.id', 'inventory.product_name', 'inventory.product_code')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();
    }
    
    private function getPaymentSummary()
    {
        $today = Carbon::today();
        
        return [
            'today' => [
                'count' => Payment::whereDate('payment_date', $today)->count(),
                'amount' => Payment::whereDate('payment_date', $today)->sum('amount') ?? 0,
            ],
            'week' => [
                'count' => Payment::where('payment_date', '>=', Carbon::now()->startOfWeek())->count(),
                'amount' => Payment::where('payment_date', '>=', Carbon::now()->startOfWeek())->sum('amount') ?? 0,
            ],
            'month' => [
                'count' => Payment::where('payment_date', '>=', Carbon::now()->startOfMonth())->count(),
                'amount' => Payment::where('payment_date', '>=', Carbon::now()->startOfMonth())->sum('amount') ?? 0,
            ]
        ];
    }
}