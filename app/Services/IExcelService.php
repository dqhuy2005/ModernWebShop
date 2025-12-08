<?php

namespace App\Services;

interface IExcelService
{
    public function exportUsers(): string;

    public function exportOrders(): string;

    public function generateUserTemplate(): string;

    public function importUsers(string $content): array;

    public function getDownloadHeaders(string $filename): array;
}
