<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Taglie gestite in modo rapido nel pannello.
     */
    protected array $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

    public function index(): View
    {
        $products = Product::withCount('variants')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $product = new Product([
            'is_active' => true,
        ]);
        $product->setRelation('variants', collect());

        return view('admin.products.create', [
            'product' => $product,
            'sizes' => $this->sizes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedProductData($request);
        $variants = $this->prepareVariants($request->input('variants', []));

        DB::transaction(function () use ($data, $variants) {
            /** @var Product $product */
            $product = Product::create($data);
            $this->syncVariants($product, $variants);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Prodotto creato con successo.');
    }

    public function edit(Product $product): View
    {
        $product->load('variants');

        return view('admin.products.edit', [
            'product' => $product,
            'sizes' => $this->sizes,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedProductData($request, $product);
        $variants = $this->prepareVariants($request->input('variants', []));

        DB::transaction(function () use ($product, $data, $variants) {
            $product->update($data);
            $product->refresh();
            $this->syncVariants($product, $variants);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Prodotto aggiornato con successo.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Prodotto eliminato.');
    }

    /**
     * Valida e normalizza i dati del prodotto.
     */
    protected function validatedProductData(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'brand'          => ['nullable', 'string', 'max:255'],
            'color'          => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'price'          => ['required', 'numeric', 'min:0'],
            'price_compare'  => ['nullable', 'numeric', 'min:0'],
            'image'          => ['nullable', 'image', 'max:2048'],
            'image_url'      => ['nullable', 'string', 'max:2048'],
            'image_ratio'    => ['nullable', 'in:standard,wide,tall'],
            'gallery.*'      => ['nullable', 'image', 'max:2048'],
            'gallery_urls'   => ['nullable', 'string'],
            'remove_gallery' => ['array'],
            'remove_gallery.*' => ['string'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        $data['price'] = round((float) $data['price'], 2);
        $data['price_compare'] = isset($data['price_compare'])
            ? round((float) $data['price_compare'], 2)
            : null;
        $data['is_active'] = $request->boolean('is_active');
        $data['image_ratio'] = $request->input('image_ratio', $product?->image_ratio ?? 'standard') ?: 'standard';

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
            $data['image_url'] = null;
        } elseif (! empty($data['image_url'])) {
            $data['image_url'] = trim($data['image_url']);
        } else {
            $data['image_url'] = null;
        }

        $gallery = $product ? collect($product->images ?? []) : collect();

        $removeGallery = collect($request->input('remove_gallery', []))->filter();
        if ($removeGallery->isNotEmpty()) {
            foreach ($removeGallery as $path) {
                if (!Str::startsWith($path, ['http://', 'https://']) && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            $gallery = $gallery->reject(fn ($img) => $removeGallery->contains($img))->values();
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                if ($file) {
                    $gallery->push($file->store('products', 'public'));
                }
            }
        }

        if ($request->filled('gallery_urls')) {
            $urls = preg_split('/\r\n|\r|\n/', $request->input('gallery_urls'));
            foreach ($urls as $url) {
                $url = trim($url);
                if ($url !== '') {
                    $gallery->push($url);
                }
            }
        }

        $data['images'] = $gallery->filter()->unique()->values()->all();

        if (empty($data['image'])) {
            unset($data['image']);
        }

        unset($data['gallery'], $data['gallery_urls'], $data['remove_gallery']);

        return $data;
    }

    /**
     * Legge le varianti inviate dal form.
     */
    protected function prepareVariants(array $input): array
    {
        $variants = [];

        foreach ($this->sizes as $size) {
            $row = $input[$size] ?? null;
            if (! is_array($row)) {
                continue;
            }

            $enabled = isset($row['enabled']) && (bool) $row['enabled'];
            if (! $enabled) {
                continue;
            }

            $stock = (int) max(0, (int) ($row['stock'] ?? 0));
            $price = $row['price'] ?? null;
            $priceCents = null;
            if ($price !== null && $price !== '') {
                $priceCents = (int) round(((float) $price) * 100);
            }

            $variants[$size] = [
                'stock' => $stock,
                'price_cents' => $priceCents,
            ];
        }

        return $variants;
    }

    /**
     * Crea/aggiorna/elimina le varianti in base all'input del form.
     */
    protected function syncVariants(Product $product, array $variants): void
    {
        $enabledSizes = array_keys($variants);

        if (empty($enabledSizes)) {
            ProductVariant::where('product_id', $product->id)->delete();
            return;
        }

        ProductVariant::where('product_id', $product->id)
            ->whereNotIn('size', $enabledSizes)
            ->delete();

        foreach ($variants as $size => $variant) {
            ProductVariant::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'size' => $size,
                ],
                [
                    'sku' => strtoupper(Str::slug($product->slug ?? $product->name) . '-' . $size),
                    'stock' => $variant['stock'],
                    'price_cents' => $variant['price_cents'],
                    'color' => $product->color,
                ]
            );
        }
    }
}
