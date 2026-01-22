<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\ProductExport;
use App\Http\Controllers\Controller;
use App\Imports\ProductImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductExcelController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv',
            'zip_file' => 'nullable|mimes:zip'
        ]);

        $extractedPath = null;
        if ($request->hasFile('zip_file')) {
            $zip = new \ZipArchive;
            $res = $zip->open($request->file('zip_file')->path());
            if ($res === TRUE) {
                $extractedPath = storage_path('app/temp_imports/' . uniqid());
                if (!file_exists($extractedPath)) {
                    mkdir($extractedPath, 0777, true);
                }
                $zip->extractTo($extractedPath);
                $zip->close();
            }
        }

        try {
            Excel::import(new ProductImport($extractedPath), $request->file('excel_file'));

            // Cleanup temp files if any
            if ($extractedPath && file_exists($extractedPath)) {
                $this->removeDirectory($extractedPath);
            }

            return back()->with('success', __('Products imported successfully!'));
        } catch (\Exception $e) {
            // Cleanup on error
            if ($extractedPath && file_exists($extractedPath)) {
                $this->removeDirectory($extractedPath);
            }
            return back()->with('error', __('Error importing products: ') . $e->getMessage());
        }
    }

    private function removeDirectory($path)
    {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }
        rmdir($path);
    }

    public function export()
    {
        return Excel::download(new ProductExport, 'products_export_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }
}
