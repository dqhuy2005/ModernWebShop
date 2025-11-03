<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\ProductFilterRequest;
use App\Repository\ProductRepository;

class HomeController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    public function index()
    {
        $featuredCategories = Category::active()
            ->withCount('products')
            ->limit(3)
            ->get();

        $newProducts = Product::active()
            ->with('category')
            ->latest('created_at')
            ->limit(8)
            ->get();

        $categories = Category::active()
            ->with(['products' => function ($query) {
                $query->active()
                    ->where('is_hot', true)
                    ->latest('updated_at')
                    ->limit(15);
            }])
            ->limit(value: 5)
            ->get();

        $topSellingProducts = Product::active()
            ->with('category')
            ->mostViewed(12)
            ->get()
            ->chunk(6);

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

    public function showCategory($slug, ProductFilterRequest $request)
    {
        $category = Category::where('slug', $slug)
            ->select('id', 'name', 'slug', 'image')
            ->firstOrFail();

        $filters = $request->getFilters();

        $products = $this->productRepository
            ->getFilteredProducts($category->id, $filters)
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('user.partials.product-grid', compact('products'))->render(),
                'pagination' => view('user.partials.pagination', compact('products'))->render()
            ]);
        }

        return view('user.category', compact('category', 'products', 'filters'));
    }

    public function hotDeals()
    {
        $hotDeals = Product::active()
            ->hot()
            ->with('category')
            ->paginate(12);

        return view('user.hot-deals', compact('hotDeals'));
    }

    public function searchSuggestions(Request $request)
    {
        $keyword = $request->input('keyword', '');

        if (strlen($keyword) < 2) {
            return response()->json([
                'success' => true,
                'products' => []
            ]);
        }

        $products = Product::active()
            ->search($keyword)
            ->select('id', 'name', 'slug', 'image', 'price')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image ? asset('storage/' . $product->image) : asset('assets/imgs/products/default.png'),
                    'price' => $product->price,
                    'formatted_price' => number_format($product->price, 0, ',', '.') . 'đ',
                    'url' => route('products.show', $product->slug),
                ];
            });

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }
}
