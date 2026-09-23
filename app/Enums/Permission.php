<?php

namespace App\Enums;

enum Permission: string
{
    case INVITE_MEMBERS = 'invite_members';
    case CREATE_CHANNEL = 'create_channel';
    case MANAGE_CHANNEL = 'manage_channel';
    case CREATE_PATUNGAN = 'create_patungan';
    case MANAGE_PATUNGAN = 'manage_patungan';
    case SEND_MESSAGE = 'send_message';

    public function label(): string
    {
        return match ($this) {
            self::INVITE_MEMBERS => 'Boleh undang temen lewat link',
            self::CREATE_CHANNEL => 'Boleh bikin channel baru',
            self::MANAGE_CHANNEL => 'Boleh kelola channel',
            self::CREATE_PATUNGAN => 'Boleh bikin patungan',
            self::MANAGE_PATUNGAN => 'Boleh kelola semua patungan',
            self::SEND_MESSAGE => 'Boleh kirim pesan di channel',
        };
    }

    /**
     * @return array<int, string> daftar slug permission.
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
