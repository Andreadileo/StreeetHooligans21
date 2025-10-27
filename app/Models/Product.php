<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * Campi mass-assignable
     */
    protected $fillable = [
        'name',
        'slug',
        'brand',
        'color',
        'description',
        'price',
        'price_compare',
        'image',       // es: path in storage/public...
        'image_url',   // url assoluto opzionale
        'images',      // array di immagini extra
        'badge',
        'image_ratio',
        'sizes',       // array (anche con quantità per taglia se vuoi)
        'is_active',
    ];

    /**
     * Casting automatico dei tipi
     */
    protected $casts = [
        'price'         => 'decimal:2',
        'price_compare' => 'decimal:2',
        'images'        => 'array',
        'sizes'         => 'array',
        'is_active'     => 'boolean',
    ];

    /**
     * Accessor aggiuntivi utili quando serializziamo il modello.
     */
    protected $appends = [
        'price_cents',
        'cover_image_url',
        'product_url',
    ];

    protected $attributes = [
        'image_ratio' => 'standard',
    ];

    /**
     * Hook di modello
     */
    protected static function booted(): void
    {
        // Slug in creazione
        static::creating(function (Product $product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = static::makeUniqueSlug($product->name);
            }
        });

        // Slug in update se cambia il name e lo slug è vuoto o diverso dal name
        static::updating(function (Product $product) {
            if (
                $product->isDirty('name') &&
                (empty($product->slug) || Str::slug($product->getOriginal('name')) === $product->slug)
            ) {
                $product->slug = static::makeUniqueSlug($product->name, $product->id);
            }
        });

        static::retrieved(function (Product $product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = static::makeUniqueSlug($product->name, $product->id);
                $product->saveQuietly();
            }
        });
    }

    /**
     * Genera uno slug univoco per la tabella products.
     */
    protected static function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            static::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
    

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    
    /**
     * Accessor comodo: se image_url manca ma c'è image,
     * prova a restituire l’URL pubblico dello storage.
     */
    public function getImageUrlAttribute($value): ?string
    {
        if (!empty($value)) {
            return $this->resolveImagePath($value);
        }

        if (!empty($this->attributes['image'])) {
            return $this->resolveImagePath($this->attributes['image']);
        }

        return null;
    }

    /**
     * Accessor: prezzo in centesimi (int) calcolato dal campo decimal.
     */
    public function getPriceCentsAttribute(): int
    {
        $raw = $this->attributes['price'] ?? 0;

        return (int) round(((float) $raw) * 100);
    }

    /**
     * Accessor: URL completo dell'immagine di copertina (fallback su placeholder).
     */
    public function getCoverImageUrlAttribute(): string
    {
        return $this->getImageUrlAttribute(
            $this->attributes['image_url'] ?? ($this->attributes['image'] ?? '')
        ) ?? asset('images/placeholder.jpg');
    }

    /**
     * Accessor: URL del prodotto (gestisce slug mancanti e fallback).
     */
    public function getProductUrlAttribute(): string
    {
        $slug = $this->slug;

        if ($slug && Route::has('product.show')) {
            try {
                return route('product.show', ['product' => $slug]);
            } catch (UrlGenerationException $e) {
                // continua con fallback
            }
        }

        if ($slug) {
            return url('/products/' . $slug);
        }

        return route('catalog.index');
    }

    /**
     * Scope: solo prodotti attivi
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: ricerca semplice su name/brand/color
     */
    public function scopeSearch($query, ?string $term)
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('brand', 'like', "%{$term}%")
              ->orWhere('color', 'like', "%{$term}%");
        });
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Normalizza il valore di immagine: supporta URL assolute e path locali.
     */
    protected function resolveImagePath(string $value): string
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return asset('images/placeholder.jpg');
        }

        if (preg_match('/^https?:\/\//i', $trimmed)) {
            return $trimmed;
        }

        if (Str::startsWith($trimmed, ['storage/', 'images/', 'img/', 'assets/', 'build/'])) {
            return asset($trimmed);
        }

        if (Str::startsWith($trimmed, ['/'])) {
            return url($trimmed);
        }

        return url('storage/' . ltrim($trimmed, '/'));
    }

    public function getImageRatioVariant(string $default = 'standard'): string
    {
        $value = $this->image_ratio ?: $default;

        return in_array($value, ['standard', 'wide', 'tall'], true) ? $value : $default;
    }
}
