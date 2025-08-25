<?php
namespace App\Controllers;
use App\Helpers\Response;
class Controller {
    protected array $cfg;
    public function __construct(array $cfg){ $this->cfg=$cfg; }
    protected function view(string $path, array $data=[]): void {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $path . '.php';
        include __DIR__ . '/../Views/layouts/header.php';
        include $viewFile;
        include __DIR__ . '/../Views/layouts/footer.php';
    }
}