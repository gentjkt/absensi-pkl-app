<?php
namespace App\Controllers;
use App\Helpers\Auth;
use App\Models\{Database, AuditLog};
class AuditController extends Controller {
    public function index(): void {
        Auth::requireRole('admin');
        $db = new Database(require __DIR__.'/../../config/db.php');
        $logs = (new AuditLog($db))->latest(300);
        $this->view('admin/audit',['logs'=>$logs]);
    }
}