<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Outlet;
use App\Models\CategoryProduct;
use Auth;
use File;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:management');
    }
    
    public function index()
    {
        $bisnis = Auth::user()->business_id;
        $produk = Product::where('business_id', $bisnis)->orderBy('id', 'asc')->get();
        return view('management.product.list.index',
            compact(
                'produk'
        ));
    }

    public function create()
    {
        $bisnis = Auth::user()->business_id;
        $kategori = CategoryProduct::where('business_id', $bisnis)->get();
        $outlet = Outlet::where('business_id', $bisnis)->get();
        return view('management.product.list.create',
            compact(
                'kategori',
                'outlet'
        ));
    }

    public function store(Request $request)
    {
        $bisnis = Auth::user()->business_id;

        $this->validate($request, [
            'name' => 'required', 'string', 'max:255',
            'price' => 'required', 'numeric',
            'category_id' => 'required',
            'image' => 'required', 'image',
        ]);

        $image = $request->file('image');
        $imageName = str_replace(" ", "-", $bisnis . '-'. $request->get('category_id') . '-' . $request->get('name'));
        $fileName = $imageName . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('assets/img/product'), $fileName);

        $price = str_replace(".", '', $request->get('price'));

        $outlets = $request->get('outlet');

        $product = new Product();
        $product->name = $request->get('name');
        $product->price = $price;
        $product->category_id = $request->get('category_id');
        $product->business_id = $bisnis;
        $product->image = $fileName;
        $product->save();

        foreach ($outlets as $outlet) {
            $detail = new ProductDetail();
            $detail->product_id = $product->id;
            $detail->outlet_id = $outlet;
            $detail->save();
        }
        
        
        \Session::flash('success', 'Berhasil Menambahkan Produk');
        return redirect(route('management-product.index'));
    }

    public function delete($id)
    {
        $product = Product::where('id', $id)->first();
        $details = ProductDetail::where('product_id', $id)->get();

        if(!$product){
            \Session::flash('danger', 'Data tidak ditemukan');
            return redirect(route('management-product.index'));
        }

        File::delete(public_path('assets/img/product/' . $product->image));
        $product->delete();

        foreach ($details as $detail){
            $detail->delete();
        }

        \Session::flash('danger', 'Data Dihapus');
        return redirect(route('management-product.index'));
    }

    public function detail($id)
    {
        $product = Product::where('id', $id)->first();
        $details = ProductDetail::where('product_id', $id)->get();
        if(!$product){
            \Session::flash('danger', 'Data tidak ditemukan');
            return redirect(route('management-product.index'));
        }
        return view('management.product.list.detail',
            compact(
                'product',
                'details'
            )
        );
    }

    public function edit($id)
    {
        $bisnis = Auth::user()->business_id;
        $kategori = CategoryProduct::where('business_id', $bisnis)->get();
        $product = Product::where('id', $id)->first();
        return view('management.product.list.edit',
            compact(
                'product',
                'kategori'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $bisnis = Auth::user()->business_id;
        $product = Product::where('id', $id)->first();

        $this->validate($request, [
            'name' => 'required', 'string', 'max:255',
            'price' => 'required', 'numeric',
            'category_id' => 'required',
            'image' => 'image'
        ]);
        if (!empty($request->file('image'))) {

            File::delete(public_path('assets/img/product/' . $product->image));

            $image = $request->file('image');
            $imageName = str_replace(" ", "-", $bisnis . '-'. $request->get('category_id') . '-' . $request->get('name'));
            $fileName = $imageName . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/product'), $fileName);

            $price = str_replace(".", '', $request->get('price'));

            $product->name = $request->get('name');
            $product->price = $price;
            $product->category_id = $request->get('category_id');
            $product->image = $fileName;
            $product->save();

        }else {
            $price = str_replace(".", '', $request->get('price'));

            $product->name = $request->get('name');
            $product->price = $price;
            $product->category_id = $request->get('category_id');
            $product->save();
        }

        \Session::flash('success', 'Data Berhasil Disimpan');
        return redirect(route('management-product.index'));
    }
}
