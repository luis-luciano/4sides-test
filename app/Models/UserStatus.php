<?php

namespace App\Models;

enum StatusUser: string
{
    case Active = 'Activo';
    case Inactive = 'Inactivo';
}
