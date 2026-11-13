<?php

namespace Tests\Feature;

use Tests\TestCase;

class BackupDatabaseTest extends TestCase
{
    public function test_backup_requires_external_directory(): void
    {
        config(['backups.directory' => null]);
        $this->artisan('tair:backup-database')->assertFailed();
        config(['backups.directory' => storage_path()]);
        $this->artisan('tair:backup-database')->assertFailed();
    }
}
