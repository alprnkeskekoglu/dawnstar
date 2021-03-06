<?php

return [
    'index_title' => 'Menü İçeriği',
    'create_title' => 'Menü İçeriği Oluştur',
    'edit_title' => 'Menü İçeriği Düzenle',

    'order_save' => 'Sıralamayı Kaydet',

    'labels' => [
        'image' => 'Görsel',
        'status' => 'Durum',
        'name' => 'Adı',
        'type' => 'Tipi',
        'url_id' => 'İç Link',
        'out_link' => 'Dış Link',
        'target' => 'Hedef',
    ],

    'type' => [
        'internal_link' => 'İç Link',
        'out_link' => 'Dış Link',
        'blank_link' => 'Boş Link'
    ],

    'target' => [
        'blank' => 'Yeni Sekme',
        'self' => 'Aynı Sekme',
    ],

    'response_message' => [
        'store' => 'Menü İçeriği başarıyla oluşturulmuştur.',
        'update' => 'Menü İçeriği başarıyla güncellenmiştir.',
        'id_error' => "Verilen id'ye (:id) ait menü içeriği bulunamadı!"
    ],

    'swal' => [
        'success' => [
            'title' => 'Başarılı!',
            'subtitle' => 'Sıralama başarıyla güncellendi.'
        ],
        'error' => [
            'title' => 'Hata!',
            'subtitle' => 'Sıralama güncellenirken hata oluştu.'
        ],
    ]
];
