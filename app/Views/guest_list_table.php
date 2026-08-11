<?php
// Remove duplicates by 'name' and 'tel'
$uniqueGuests = [];
$seen = [];

if (!empty($guests)) {
    foreach ($guests as $guest) {
        $key = strtolower($guest['name']) . '|' . $guest['tel']; // unique key based on name + tel
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $uniqueGuests[] = $guest;
        }
    }
}
?>

<div class="container mt-5">
    <h3>Senarai Pelawat</h3>
    <a href="<?= base_url('userlist') ?>" class="btn btn-secondary mb-3">Kembali</a>

    <table id="guestTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th width="10%">#</th>
                <th width="50%">Nama</th>
                <th width="40%">No. Telefon</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($uniqueGuests)): ?>
                <?php foreach ($uniqueGuests as $index => $guest): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($guest['name']) ?></td>
                    <td><?= esc($guest['tel']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">Tiada data pelawat</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>