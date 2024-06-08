<?php

namespace Modules\Product\Http\Controllers;

use Modules\Product\DataTables\ProductDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\Product;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @param ProductDataTable $dataTable
     * @return Renderable
     */
    public function index(ProductDataTable $dataTable)
    {
        abort_if(Gate::denies('access_products'), 403);

        return $dataTable->render('product::products.index');
    }

    /**
     * Show the form for creating a new product.
     *
     * @return Renderable
     */
    public function create()
    {
        abort_if(Gate::denies('create_products'), 403);

        return view('product::products.create');
    }

    /**
     * Store a newly created product in storage.
     *
     * @param StoreProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProductRequest $request)
    {
        DB::transaction(function () use ($request) {
            $product = Product::create($request->except('document', 'product_image'));

            $this->handleDocuments($request, $product);
            $this->handleImages($request, $product);

            toast('Product Created!', 'success');
        });

        return redirect()->route('products.index');
    }

    /**
     * Display the specified product.
     *
     * @param Product $product
     * @return Renderable
     */
    public function show(Product $product)
    {
        abort_if(Gate::denies('show_products'), 403);

        return view('product::products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param Product $product
     * @return Renderable
     */
    public function edit(Product $product)
    {
        abort_if(Gate::denies('edit_products'), 403);

        return view('product::products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param UpdateProductRequest $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $product->update($request->except('document', 'product_image'));

            $this->handleDocuments($request, $product, true);
            $this->handleImages($request, $product);

            toast('Product Updated!', 'info');
        });

        return redirect()->route('products.index');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            $product->clearMediaCollection('documents');
            $product->clearMediaCollection('images');
            $product->delete();

            toast('Product Deleted!', 'warning');
        });

        return redirect()->route('products.index');
    }

    /**
     * Handle the documents for the product.
     *
     * @param Request $request
     * @param Product $product
     * @param bool $isUpdate
     */
    protected function handleDocuments(Request $request, Product $product, $isUpdate = false)
    {
        if ($isUpdate) {
            $product->getMedia('documents')
                ->whereNotIn('file_name', $request->input('document', []))
                ->each(fn ($media) => $media->delete());
        }

        $existingMedia = $product->getMedia('documents')->pluck('file_name')->toArray();

        foreach ($request->input('document', []) as $file) {
            if (!in_array($file, $existingMedia)) {
                $product->addMedia(Storage::path('temp/dropzone/' . $file))->toMediaCollection('documents');
            }
        }
    }

    /**
     * Handle the images for the product.
     *
     * @param Request $request
     * @param Product $product
     */
    protected function handleImages(Request $request, Product $product)
    {
        if ($request->hasFile('product_image')) {
            $product->clearMediaCollection('images');

            foreach ($request->file('product_image') as $file) {
                $product->addMedia($file)->toMediaCollection('images');
            }
        }
    }
}
