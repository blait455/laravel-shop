<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use App\Category;
use App\Post_category;
use App\Brand;
use App\Cart;
use App\Order;
use App\Shipping;
use App\Post;
use App\Product;
use App\Product_review;
use App\Widget;
use Auth;
class Helper
{
	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $d Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boole $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source https://gravatar.com/site/implement/images/php/
	 */
	public static function get_gravatar( $email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = array() ) {
	    $url = 'https://www.gravatar.com/avatar/';
	    $url .= md5( strtolower( trim( $email ) ) );
	    $url .= "?s=$s&d=$d&r=$r";
	    if ( $img ) {
	        $url = '<img src="' . $url . '"';
	        foreach ( $atts as $key => $val )
	            $url .= ' ' . $key . '="' . $val . '"';
	        $url .= ' />';
	    }
	    return $url;
	}

	//frontend cat supply
    public static function productCategoryList()
    {
        return Category::orderBy('id', 'desc')->get();
    
    }

    public static function postCategoryList()
    {
        return Post_category::orderBy('id', 'desc')->get();
    
    }

    public static function postCategory($post)
    {
        $cat = [];
        foreach($post->categories as $k => $category):
            $cat[$k] = $category->id;
        endforeach;

        return $cat;
    }

    public static function recentPost()
    {
        return Post::orderBy('id', 'desc')->limit(3)->get();
    }

    public static function recentProduct($count = 4)
    {
        return Product::latest()->orderBy('id', 'desc')->limit($count)->get();
    }
    public static function inspireProduct($count = 8)
    {
        return Product::all()->random()->limit($count)->orderBy('id', 'desc');
    }    

    // public static function postCommentTotal($post)
    // {
    //     return Post_category::orderBy('id', 'desc')->get();
    
    // }
    public static function maxPrice()
    {
        return ceil(Product::max('offer_price'));
    
    }
    public static function minPrice()
    {
        return floor(Product::min('offer_price'));
    
    }

    //frontend brands supply
    public static function productBrandList()
    {
        return Brand::orderBy('id', 'desc')->get();
    
    }


    // static $user_id = auth()->user()->id;

    //frontend cart count
    public static function cartCount( $user_id ='' )
    {   
    	if(Auth::check()) {
            if ($user_id == '') $user_id = auth()->user()->id;
            return Cart::where('user_id', $user_id)->where('order_id', null)->sum('quantity');
        }else return 0;
    }

    //frontend cart count
    public static function orderCount($id, $user_id='' )
    {
        if(Auth::check()) {
          if ($user_id == '') $user_id = auth()->user()->id;  
          return Cart::where('user_id', $user_id)->where('order_id', $id)->sum('quantity');   
        }else return 0;
    	
    
    }

    //frontend order price
    public static function orderPrice($id, $user_id='' )
    {
        
    	if(Auth::check()){
            if ($user_id == '') $user_id = auth()->user()->id;
            $order_price = (float)Cart::where('user_id', $user_id)->where('order_id', $id)->sum('price');
            if ($order_price) {
                return number_format((float)($order_price), 2, '.', '');
            }else return 0;
        }else return 0;
    }

    //frontend grand price
    public static function grandPrice($id, $user_id='' )
    {
        if(Auth::check()){
            if ($user_id == '') $user_id = auth()->user()->id;
            $order = Order::find($id)->first();
            if ($order) {
                $shipping_price = (float)$order->shipping->price; 
                $order_price = self::orderPrice($id, $user_id);
                return number_format((float)($order_price + $shipping_price), 2, '.', '');
            }else return 0;
        }else return 0;
    }

    //frontend shipping
    public static function shiping()
    {
        return Shipping::orderBy('id', 'desc')->get();
    
    }

    //product review
    public static function reviewStar($product_id, $star = 0)
    {
        return (int)Product_review::where('product_id', $product_id)->where('status', '1')->where('rating', (float)$star)->count('rating');
    
    }
    //product review
    public static function reviewOveralStar($product_id, $option = 'avg')
    {
        if ($option == 'count') return Product_review::where('status', '1')->where('product_id', $product_id)->count('rating');
        return number_format((float)(Product_review::where('status', '1')->where('product_id', $product_id)->avg('rating')), 2, '.', '');
    }
    //user rating
    public static function reviewStar_fa($id)
    {
        $review = Product_review::find($id);
        $rating_count = (int)$review->rating;

        $rating = '';
        if ($rating_count) {
            $i=0;
            for (; $i < $rating_count; $i++) { 
                $rating .= '<i class="fa fa-star"></i>';
            }
            for ($j=5; $j > $i; $j--) { 
                $rating .= '<i class="fa fa-star-o"></i>';
            }
        }
        return $rating;
    }

    //frontend shipping
    public static function currency()
    {
        return '€';
    
    }

    //widget areas
    public static function widget_areas()
    {
        $widget = array();
        $widget['feature_1']='Feature 1';
        $widget['feature_2']='Feature 2';
        $widget['feature_3']='Feature 3';
        $widget['feature_4']='Feature 4';

        $widget['footer_1']='Footer 1';
        $widget['footer_2']='Footer 2';
        $widget['footer_3']='Footer 3';
        $widget['footer_4']='Footer 4';
        $widget['footer_5']='Footer 5';
        return $widget;
    
    }

    //getting widget
    public static function get_widget($position = '')
    {
        if ($position == 'footer') {
            return Widget::where('position', 'like', 'footer'.'%')->orderBy('position', 'asc')->get();
        }

        if ($position == 'feature') {
            return Widget::where('position', 'like', 'feature'.'%')->orderBy('position', 'asc')->get();
        }
        
        return Widget::orderBy('position', 'desc')->get();
    
    }

}