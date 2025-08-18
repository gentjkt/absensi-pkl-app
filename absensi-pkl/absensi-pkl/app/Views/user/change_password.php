<h2>Ubah Password</h2>

<?php if (!empty($_SESSION['error'])): ?>
    <p style="color:red"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
<?php endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
    <p style="color:green"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
<?php endif; ?>

<form method="POST" action="/user/change-password">
    <label>Password Lama</label><br>
    <input type="password" name="old_password" required><br><br>

    <label>Password Baru</label><br>
    <input type="password" name="new_password" required><br><br>

    <label>Konfirmasi Password Baru</label><br>
    <input type="password" name="confirm_password" required><br><br>

    <button type="submit">Simpan</button>
</form>
