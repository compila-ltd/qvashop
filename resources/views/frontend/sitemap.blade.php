<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://www.qvashop.com/</loc>
        <lastmod>{{ date("Y-m-d") }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>https://www.qvashop.com/blog</loc>
        <lastmod>{{ date("Y-m-d") }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.4</priority>
    </url>
    <url>
        <loc>https://www.qvashop.com/terms</loc>
        <lastmod>{{ date("Y-m-d") }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.4</priority>
    </url>
    <url>
        <loc>https://www.qvashop.com/return-policy</loc>
        <lastmod>{{ date("Y-m-d") }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.4</priority>
    </url>
    <url>
        <loc>https://www.qvashop.com/support-policy</loc>
        <lastmod>{{ date("Y-m-d") }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.4</priority>
    </url>
    <url>
        <loc>https://www.qvashop.com/privacy-policy</loc>
        <lastmod>{{ date("Y-m-d") }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.4</priority>
    </url>
    @foreach($categories as $category)
    <url>
        <loc>{{ route('products.category', $category->slug) }}</loc>
        <lastmod>{{ $category->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
    @foreach($products as $product)
    <url>
        <loc>{{ route('product', $product->slug) }}</loc>
        <lastmod>{{ $product->updated_at->tz('UTC')->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>