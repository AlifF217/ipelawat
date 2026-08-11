<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Senarai Pelawat</h4>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>No. Telefon</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pelawats)): ?>
                        <?php foreach ($pelawats as $pelawat): ?>
                            <tr>
                                <td><?= esc($pelawat['id']) ?></td>
                                <td><?= esc($pelawat['name']) ?></td>
                                <td><?= esc($pelawat['tel']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Tiada rekod pelawat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <a href="<?= base_url() ?>" class="btn btn-secondary mt-3">Kembali</a>
        </div>
    </div>
</div>
