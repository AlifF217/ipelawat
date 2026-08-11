<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lihat Pengguna</title>
    <style>
        body {
            background-color: #f9f9f9;
            transform: scale(0.8);
            transform-origin: top left;
            width: 125%;
            color: black;
        }
        
        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #007a91;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .info-item {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-label {
            font-weight: bold;
            color: #007a91;
            margin-right: 10px;
        }
        
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background-color: #007a91;
            color: #fff;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }
        
        .back-btn:hover {
            background-color: #005f6b;
        }
    </style>
</head>
<body>
    <?= view('security_prompt') ?>
    
    <div class="container">
        <h1>👁️ Lihat Maklumat Pengguna</h1>
        
        <div class="user-info">
            <div class="info-item">
                <span class="info-label">Nama:</span>
                <span><?= esc($user['Name']) ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Email:</span>
                <span><?= esc($user['Email']) ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Bahagian:</span>
                <span><?= !empty($user['Division']) ? esc($user['Division']) : 'Tidak diisi' ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">No Telefon:</span>
                <span><?= !empty($user['Phone']) ? esc($user['Phone']) : 'Tidak diisi' ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span><?= !empty($user['Active']) ? 'Aktif' : 'Tidak Aktif' ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Level:</span>
                <span><?= esc($user['Level']) ?></span>
            </div>
        </div>
        
        <div class="text-center">
            <a href="javascript:history.back()" class="back-btn">Kembali ke Senarai</a>
            
            <?php if (isset($userLevel) && strtolower($userLevel) === 'superadmin'): ?>
                <a href="<?= base_url('editUser/' . $user['Id']) ?>" class="back-btn" style="background-color: #28a745; margin-left: 10px;">✏️ Edit</a>
                <a href="#" 
                   class="back-btn delete-user-btn" 
                   style="background-color: #dc3545; margin-left: 10px;"
                   data-id="<?= esc($user['Id']) ?>"
                   data-name="<?= esc($user['Name']) ?>">🗑️ Padam</a>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Delete user functionality for view page
            $('.delete-user-btn').click(function(e) {
                e.preventDefault();
                
                const userId = $(this).data('id');
                const userName = $(this).data('name');
                
                if(confirm('Adakah anda pasti mahu memadam pengguna "' + userName + '"?')) {
                    $.ajax({
                        url: '<?= base_url('deleteUser') ?>/' + userId,
                        type: 'POST',
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                window.location.href = '<?= base_url('userlist') ?>';
                            } else {
                                alert('Ralat: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Ralat: tidak dapat memadam pengguna. ' + error);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>