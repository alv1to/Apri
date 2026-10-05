<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Dibayar = 'dibayar';
    case SedangDibuat = 'sedang_dibuat';
    case SiapDiambil = 'siap_diambil';
    case Selesai = 'selesai';
    case Cancel = 'cancel';
}