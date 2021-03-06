<?php

return [
    'index_title' => 'Panel Yöneticileri',
    'create_title' => 'Yönetici Oluştur',
    'edit_title' => 'Yönetici Düzenle',


    'labels' => [
        'image' => 'Görsel',
        'status' => 'Durum',
        'role_id' => 'Rol',
        'fullname' => 'Ad Soyad',
        'username' => 'Kullanıcı Adı',
        'email' => 'E-posta',
        'password' => 'Şifre',
    ],

    'password_regex' => 'Şifre harf, sayı ve özel karakter (@$!%*#?.&) içermelidir.',

    'response_message' => [
        'store' => 'Admin başarıyla oluşturulmuştur.',
        'update' => 'Admin başarıyla güncellenmiştir.',
        'id_error' => "Verilen id'ye (:id) ait admin bulunamadı!"
    ],
];
