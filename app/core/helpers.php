<?php

/**
 * Render gambar produk: tampilkan <img> jika ada foto, fallback ke emoji icon.
 *
 * @param array  $item   Array produk/item dengan key 'foto' dan 'icon'
 * @param string $size   CSS width & height (default '40px')
 * @param string $radius CSS border-radius (default '6px')
 * @param string $style  CSS tambahan
 */
function produk_img(array $item, string $size = '40px', string $radius = '6px', string $style = ''): string {
    $foto = $item['foto'] ?? '';
    $icon = $item['icon'] ?? '📦';
    $nama = htmlspecialchars($item['nama'] ?? $item['nama_produk'] ?? '', ENT_QUOTES);

    if (!empty($foto)) {
        $s = "width:{$size};height:{$size};object-fit:cover;border-radius:{$radius};display:block;{$style}";
        return '<img src="' . BASEURL . 'uploads/produk/' . htmlspecialchars($foto, ENT_QUOTES) . '" alt="' . $nama . '" style="' . $s . '">';
    }
    return '<span style="font-size:' . $size . ';line-height:1;' . $style . '">' . htmlspecialchars($icon, ENT_QUOTES) . '</span>';
}
