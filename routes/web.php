<?php

// Format: 'URL' => 'Controller@method'
return [
    // Rute Publik
    '/'                 => 'Home@index',
    '/login'            => 'Auth@login',
    '/register'         => 'Auth@register',

    // Rute Pelanggan
    '/customer'         => 'Customer@index',
    '/customer/pesanan' => 'Customer@pesananSaya',

    // Rute Admin (Menggunakan namespace Admin/)
    '/admin/login'      => 'Admin/LoginAdmin@index',
    '/admin/logout'     => 'Admin/LogoutAdmin@index',
    '/admin/dashboard'  => 'Admin/Dashboard@index',
    '/admin/pelanggan'  => 'Admin/Pelanggan@index',
    '/admin/produk'     => 'Admin/Produk@index',
    '/admin/pesanan'    => 'Admin/Pesanan@index',
];