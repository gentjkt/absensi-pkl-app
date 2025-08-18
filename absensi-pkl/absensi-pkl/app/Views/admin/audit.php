<h2>Audit Log</h2>
<div class="card">
  <table>
    <thead><tr><th>Waktu</th><th>User</th><th>Role</th><th>Aksi</th><th>Detail</th></tr></thead>
    <tbody>
    <?php foreach($logs as $l): ?>
      <tr>
        <td><?= \App\Helpers\Response::e($l['created_at']) ?></td>
        <td><?= \App\Helpers\Response::e(($l['username']??'-').' / '.($l['name']??'-')) ?></td>
        <td><?= \App\Helpers\Response::e($_SESSION['user']['role'] ?? '-') ?></td>
        <td><?= \App\Helpers\Response::e($l['action']) ?></td>
        <td><?= \App\Helpers\Response::e($l['detail']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>