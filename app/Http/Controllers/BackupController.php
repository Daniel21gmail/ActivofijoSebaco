<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function index()
    {
        // Ensure backups directory exists
        $path = base_path('backups');
        if (!File::exists($path)) {
            File::makeDirectory($path);
        }

        // Get all .sql files
        $files = File::files($path);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'date' => date('d/m/Y H:i:s', $file->getMTime()),
                    'timestamp' => $file->getMTime(),
                    'path' => $file->getPathname()
                ];
            }
        }

        // Sort by newest first
        usort($backups, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return Inertia::render('Backups/Index', [
            'backups' => $backups
        ]);
    }

    public function store()
    {
        $filename = 'backup_siafseb_' . date('Y-m-d_H-i-s') . '.sql';
        $path = base_path('backups/' . $filename);

        // Get DB credentials from .env
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbUser = env('DB_USERNAME', 'root');
        $dbPass = env('DB_PASSWORD', '');
        $dbName = env('DB_DATABASE', 'siafseb');

        // Assuming mysqldump is in path or standard XAMPP location
        // Try common XAMPP paths if just 'mysqldump' fails
        $mysqldump = 'mysqldump';
        if (file_exists('C:\xampp\mysql\bin\mysqldump.exe')) {
            $mysqldump = 'C:\xampp\mysql\bin\mysqldump.exe';
        }

        $passwordPart = $dbPass ? "-p\"$dbPass\"" : "";

        $command = "\"$mysqldump\" -h $dbHost -u $dbUser $passwordPart $dbName > \"$path\"";

        try {
            exec($command, $output, $returnVar);

            if ($returnVar === 0) {
                return redirect()->back()->with('message', 'Respaldo creado exitosamente.');
            } else {
                return redirect()->back()->with('error', 'Error al crear el respaldo. Código: ' . $returnVar);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Excepción: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $path = base_path('backups/' . $filename);

        if (File::exists($path)) {
            return response()->download($path);
        }

        return redirect()->back()->with('error', 'El archivo no existe.');
    }

    public function destroy($filename)
    {
        $path = base_path('backups/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
            return redirect()->back()->with('message', 'Respaldo eliminado correctamente.');
        }

        return redirect()->back()->with('error', 'El archivo no existe.');
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
