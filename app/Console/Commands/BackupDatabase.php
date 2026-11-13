<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'tair:backup-database';

    protected $description = 'Создать зашифрованную резервную копию MySQL';

    public function handle(): int
    {
        $directory = config('backups.directory');
        if (! $directory || ! is_dir($directory) || ! is_writable($directory)) {
            $this->error('Укажите существующий защищённый каталог DATABASE_BACKUP_PATH вне каталога приложения.');

            return self::FAILURE;
        }
        $real = realpath($directory);
        if (str_starts_with($real.DIRECTORY_SEPARATOR, base_path().DIRECTORY_SEPARATOR)) {
            $this->error('Каталог резервных копий должен находиться вне приложения.');

            return self::FAILURE;
        }
        $connection = config('database.connections.'.config('database.default'));
        if (($connection['driver'] ?? '') !== 'mysql') {
            $this->error('Команда предназначена для MySQL.');

            return self::FAILURE;
        }
        $process = new Process([config('backups.mysql_dump'), '--single-transaction', '--quick', '--skip-lock-tables', '--host='.$connection['host'], '--port='.$connection['port'], '--user='.$connection['username'], $connection['database']], null, ['MYSQL_PWD' => $connection['password']]);
        $process->setTimeout(120);
        try {
            $process->mustRun();
            $payload = Crypt::encryptString(base64_encode(gzencode($process->getOutput(), 9)));
        } catch (\Throwable $e) {
            $this->error('Не удалось создать резервную копию. Проверьте доступность mysqldump и базы данных.');

            return self::FAILURE;
        }
        $path = $real.'/tair-'.now()->format('Ymd-His').'-'.bin2hex(random_bytes(4)).'.sql.gz.enc';
        $temporary = $path.'.tmp';
        if (file_put_contents($temporary, $payload, LOCK_EX) === false) {
            $this->error('Не удалось сохранить резервную копию.');

            return self::FAILURE;
        }
        chmod($temporary, 0600);
        if (! rename($temporary, $path)) {
            @unlink($temporary);
            $this->error('Не удалось завершить сохранение резервной копии.');

            return self::FAILURE;
        }
        foreach (glob($real.'/tair-*.sql.gz.enc') as $old) {
            if (filemtime($old) < now()->subDays(30)->timestamp) {
                unlink($old);
            }
        }
        $this->info('Зашифрованная копия создана. Для восстановления требуется сохранённый ключ приложения.');

        return self::SUCCESS;
    }
}
