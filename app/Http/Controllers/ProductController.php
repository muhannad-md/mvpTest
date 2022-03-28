<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Auth;

class ProductController extends Controller
{
    public function __construct(){
        $this->middleware('can:update,product', ['only' => ['update', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProductResource::collection(Product::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $user = Auth::user();

        if($user->role != User::SELLER_ROLE)
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();

        $product = Product::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'cost' => $request->cost,
            'user_id' => $user->id,
        ]);

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $user = Auth::user();

        if($user->role != User::SELLER_ROLE && $user->id != $request->user_id)
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();

        $product->update([
            'name' => $request->name,
            'amount' => $request->amount,
            'cost' => $request->cost,
        ]);

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $user = Auth::user();

        if($user->role != User::SELLER_ROLE && $user->id != $request->user_id)
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        
        $product->delete();
        return response()->json([
            'message' => 'Product deleted.'
        ], 200);
    }

    public function buy(Request $request)
    {
        $user = Auth::user();

        if($user->role != User::BYUER_ROLE)
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        
        $product = Product::find($request->product_id);

        if($product){
            
            $total = $product->cost * $request->amount;

            if($product->amount < $request->amount){
                return response()->json([
                    'message' => 'Not enough products.'
                ], 200);
            }

            if($user->deposit < $total){
                return response()->json([
                    'message' => 'Not enough money.'
                ], 200);
            }

            $product->update([
                'amount' => $product->amount - $request->amount,
            ]);

            $user->update([
                'deposit' => $user->deposit - $total,
            ]);
            

            $change = User::calculateChange($user->deposit);

            return response()->json([
                'total' => $total,
                'product' => new ProductResource($product),
                'change' => $change,
            ], 200);
        }

    }
}
