<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\Brand;

use App\Http\Resources\ItemCollection;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $products = Product::query();

        if(!empty($_GET['category'])){

            $slugs = explode(',', $_GET['category']);
            $cat_ids = Category::select('id')->whereIn('slug', $slugs)->pluck('id')->toArray();
            $products->whereIn('category_id',  $cat_ids);
        }

        if(!empty($_GET['brand'])){
            $slugs = explode(',', $_GET['brand']);
            $brand_ids = Brand::select('id')->whereIn('slug', $slugs)->pluck('id')->toArray();
            $products->whereIn('brand_id',  $brand_ids);
        }

        if(!empty($_GET['sortBy'])){

            if($_GET['sortBy'] == 'category'){
                $products->with(array('category' => function($query)
                {
                    $query->orderBy('name', 'desc');
                
                }));
            }

            if($_GET['sortBy'] == 'brand'){
                $products->with(array('brand' => function($query)
                {
                    $query->orderBy('name', 'desc');
                
                }));
            }
            if($_GET['sortBy'] == 'price'){
                $products->orderBy('price', 'asc');
            }
    
            




        }

        if(!empty($_GET['show'])){
            $products = $products->paginate($_GET['show']);
        }else{
            $products = $products->orderBy('id')->paginate(9); 
        }        
        
        
        // $products = $products->get();

    	return view('shop')->with('products', $products);

    }

    public function itemList()
    {   
        return new ItemCollection( Product::all() );
    }


    public function show(Request $request)
    {
        $product = Product::where('slug', $request->slug)->first();
        if ($product) {
            return view('shop.single')->with('product', $product);
        }else{
            return view('shop.single')->with('product', []);
        }
    }

    
    public function filter(Request $request)
    {
        $data = $request->all();

        $catURL = '';
        if(!empty($data['category'])){
            foreach($data['category'] as $category){
                if(empty($catURL)){
                    $catURL .= '&category='.$category;
                }else{
                    $catURL .= ','.$category;
                }
            }
        }

        $brandURL = '';
        if(!empty($data['brand'])){
            foreach($data['brand'] as $brand){
                if(empty($brandURL)){
                    $brandURL .= '&brand='.$brand;
                }else{
                    $brandURL .= ','.$brand;
                }
            }
        }

        $sortByURL = '';
        if(!empty($data['sortBy'])){
            $sortByURL .= '&sortBy='.$data['sortBy'];
        }

        $showURL = '';
        if(!empty($data['show'])){
            $showURL .= '&show='.$data['show'];
        }

        return redirect()->route('shop',$catURL.$brandURL.$showURL.$sortByURL);
    }


    public function categoryProduct(Request $request){

        $cat = Category::where('slug', $request->slug)->first();
        if ($cat) {
            $products = Product::where('category_id', $cat->id)->paginate(9);
            return view('shop')->with('products', $products);
        }else{
            return view('shop')->with('products', []);
        }
        
    } 

    public function brandProduct(Request $request){

        $brand = Brand::where('slug', $request->slug)->first();
        if ($brand) {
            $products = Product::where('brand_id', $brand->id)->paginate(9);
            return view('shop')->with('products', $products);
        }else{
            return view('shop')->with('products', []);
        }
        
    }    

    public function search(Request $request){
        
        $products = Product::orWhere('title', 'like', '%'.$request->search.'%' )
                    ->orWhere('description', 'like', '%'.$request->search.'%' )
                    ->orWhere('slug', 'like', '%'.$request->search.'%' )
                    ->orWhere('price', 'like', '%'.$request->search.'%' )
                    ->orWhere('quantity', 'like', '%'.$request->search.'%' )
                    ->orderBy('id', 'desc')
                    ->paginate(9);
        return view('shop.search')->with('products', $products);

    }
}
