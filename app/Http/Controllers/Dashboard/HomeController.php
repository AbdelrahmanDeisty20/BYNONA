<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Category, Order, Product, User};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // =========================
        // KPIs (الكروت العلوية)
        // =========================

        // طلبات اليوم
        $todayOrders = Order::whereDate('created_at', today())->count();

        // مبيعات اليوم
        $todaySales = Order::whereDate('created_at', today())->sum('total_price');

        // عدد العملاء
        $customersCount = User::where('user_type', 'user')->where('is_verfived', true)->count();

        // أكثر منتج مبيعًا اليوم
        $topProductToday = Product::select(
            'products.id',
            'products.name_ar',
            'products.name_en',
            DB::raw('SUM(orders_items.quantity) as total_qty')
        )
            ->join('properties', 'products.id', '=', 'properties.product_id')
            ->join('orders_items', 'properties.id', '=', 'orders_items.property_id')
            ->join('orders', 'orders.id', '=', 'orders_items.order_id')
            ->whereDate('orders.created_at', today())
            ->groupBy('products.id', 'products.name_ar', 'products.name_en')
            ->orderByDesc('total_qty')
            ->first();

        // =========================
        // Charts
        // =========================

        // طلبات الأسبوع (Bar)
        $weeklyOrders = Order::select(
            DB::raw('DAYNAME(created_at) as day'),
            DB::raw('COUNT(*) as total')
        )
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('day')
            ->pluck('total', 'day');

        // المبيعات الشهرية (Line)
        $monthlySales = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_price) as total')
        )
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        // أكثر المنتجات مبيعًا (Chart + Table)
        $topProducts = Product::select(
            'products.id',
            'products.name_ar',
            'products.name_en',
            DB::raw('SUM(orders_items.quantity) as total_qty'),
            DB::raw('SUM(orders_items.quantity * orders_items.price) as revenue')
        )
            ->join('properties', 'products.id', '=', 'properties.product_id')
            ->join('orders_items', 'properties.id', '=', 'orders_items.property_id')
            ->groupBy('products.id', 'products.name_ar', 'products.name_en')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('dashboard.dashboard', compact(
            'todayOrders',
            'todaySales',
            'customersCount',
            'topProductToday',
            'weeklyOrders',
            'monthlySales',
            'topProducts'
        ));
    }

    /**
     * Orders Chart (Last 7 Days)
     */
    public function ordersChart()
    {
        $data = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $data->pluck('date'),
            'data' => $data->pluck('total'),
        ]);
    }

    /**
     * Revenue Chart (Last 7 Days)
     */
    public function revenueChart()
    {
        $data = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as total')
        )
            ->where('status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $data->pluck('date'),
            'data' => $data->pluck('total'),
        ]);
    }

    public function sortCategory()
    {
        $categories = Category::orderBy('sort_order', 'ASC')->paginate(10);

        return view('dashboard.categories.sort', compact('categories'));
    }

    public function updateSort(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        // order = [5, 1, 9, 2 ...]
        foreach ($request->order as $index => $id) {
            Category::where('id', $id)->update([
                'sort_order' => $index + 1,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categories sorted successfully',
        ]);
    }

    public function switchLang($lang)
    {
        if (in_array($lang, ['en', 'ar'])) {
            session(['lang' => $lang]);
            session()->save();  // Ensure session is saved for guests
            app()->setLocale($lang);
        }

        return redirect()->back();
    }
}
