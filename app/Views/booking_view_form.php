<div class="container mt-5">

    <div class="card shadow-sm">
        <div class="card-header <?= strtolower($booking['pelawat']) === 'pentadbir' ? 'bg-primary' : 'bg-success' ?> text-white">
            <h4 class="mb-0">
                <?= strtolower($booking['pelawat']) === 'pentadbir' ? 'Pendaftaran Pentadbir' : 'Pendaftaran Pelawat' ?>
            </h4>
        </div>

        <div class="card-body">

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <form>
                <div class="row">
                    <div class="col-md-6">

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" class="form-control" value="<?= esc($booking['name']) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. Telefon</label>
                            <input type="text" class="form-control" value="<?= esc($booking['phone_no']) ?>" readonly>
                        </div>

                        <!-- Pegawai Ditemui is ALWAYS visible -->
                        <div class="mb-3">
                            <label class="form-label">Pegawai Ditemui</label>
                            <input type="text" class="form-control" value="<?= esc($booking['officer']) ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sebab Lawatan</label>
                            <textarea class="form-control" rows="2" readonly><?= esc($booking['reason']) ?></textarea>
                        </div>

                    </div>

                    <!-- QR COLUMN -->
                    <div class="col-md-6">
                        <div class="qr-box">
                            <h5>Kod QR Akses</h5>

                            <div><img src="<?= $qr ?>" alt="QR Code" /></div>

                            <a href="<?= $qr ?>" class="btn btn-success btn-sm mt-3" download="qr_<?= strtolower($booking['pelawat']) ?>_<?= $booking['booking_id'] ?>.png">
                                Muat Turun QR (PNG)
                            </a>
                            <br>
                            <?php
                            $waMessage = "Sila buka link untuk melihat lawatan anda: $editURL , Terima kasih.";
                            $waLink = "https://wa.me/?text=" . urlencode($waMessage);
                            ?>
                            <a href="<?= $waLink ?>" target="_blank" class="btn btn-success mt-3">
                                <i class="fab fa-whatsapp"></i> Hantar ke WhatsApp
                            </a>
                            <div class="mt-3">
                                <small>Imbas untuk akses semula ke halaman ini.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- TIME FIELDS -->
                <div class="row mt-3">
                    <?php
                        $timeInVal      = $booking['time_in']      ?? '';
                        $timeOutExpVal  = $booking['time_out_exp'] ?? '';
                        $timeOutRealVal = $booking['time_out_real'] ?? '';
                    ?>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Masa Masuk</label>
                        <input type="datetime-local" class="form-control"
                               value="<?= $timeInVal ? date('Y-m-d\TH:i:s', strtotime($timeInVal)) : '' ?>" readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jangkaan Masa Keluar</label>
                        <input type="datetime-local" class="form-control"
                               value="<?= $timeOutExpVal ? date('Y-m-d\TH:i:s', strtotime($timeOutExpVal)) : '' ?>" readonly>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Masa Keluar Sebenar</label>
                        <input type="datetime-local" class="form-control"
                               value="<?= $timeOutRealVal ? date('Y-m-d\TH:i:s', strtotime($timeOutRealVal)) : '' ?>" readonly>
                    </div>
                </div>
            </form>

            <a href="javascript:history.back()" class="btn btn-secondary w-100 mt-2">Kembali ke Kalendar</a>

        </div>
    </div>

</div>
