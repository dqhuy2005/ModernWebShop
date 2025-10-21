<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page with products
     */
    public function index()
    {
        $featuredCategories = Category::active()
            ->withCount('products')
            ->where('products_count', '>', 0)
            ->limit(3)
            ->get();

        $newProducts = Product::active()
            ->with('category')
            ->latest('created_at')
            ->limit(8)
            ->get();

        $categories = Category::active()
            ->with(['products' => function ($query) {
                $query->active()->limit(4);
            }])
            ->limit(4)
            ->get();

        $topSellingProducts = Product::active()
            ->with('category')
            ->mostViewed(9)
            ->get()
            ->chunk(3);

        $hotDeals = Product::active()
            ->hot()
            ->with('category')
            ->limit(8)
            ->get();

        return view('user.home', compact(
            'featuredCategories',
            'newProducts',
            'categories',
            'topSellingProducts',
            'hotDeals'
        ));
    }
}
