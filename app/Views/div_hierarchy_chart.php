<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Division Hierarchy</title>
    
    <style>
        
        

</style>
    
</head>
<body>

    <h2>🏢 Organisasi Bahagian Syarikat</h2>
<a href="<?= base_url('divisions/add') ?>" class="btn btn-success mb-3">+ Tambah Bahagian</a>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

<div class="tree" id="divisionTree">
    <?= buildTreeHTML($tree) ?>
</div>
<a href="<?= base_url('/userlist') ?>" class="btn btn-secondary">Kembali</a>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".tree-toggle").forEach(toggle => {
        toggle.addEventListener("click", () => {
            let parent = toggle.closest("li");
            let childList = parent.querySelector(":scope > ul");

            if (!childList) return;

            childList.classList.toggle("collapsed");

            // Switch arrow icons
            if (childList.classList.contains("collapsed")) {
                toggle.textContent = "▶"; // collapsed
            } else {
                toggle.textContent = "▼"; // expanded
            }
        });
    });
});
</script>
</body>
</html>

<?php
// Helper to recursively render tree as nested ULs
function buildTreeHTML($tree)
{
    $html = "<ul>";
    foreach ($tree as $node) {

        $hasChildren = isset($node['children']);

        // ✅ Determine color & font size based on parent_id
        $parentId = $node['parent_id'] ?? null;

        // Default values
        $color = "black";
        $fontSize = "16px";

        if ($parentId === null) {
            // ✅ ROOT NODES (NULL parent_id)
            $color = "#800000";     // dark red / maroon
            $fontSize = "28px";     // largest
        } else {
            switch ($parentId) {
                case 1:
                    $color = "purple";
                    $fontSize = "26px";
                    break;
                case 2:
                    $color = "blue";
                    $fontSize = "22px";
                    break;
                case 3:
                    $color = "turqoise";
                    $fontSize = "19px";
                    break;
                default:
                    $color = "black";
                    $fontSize = "16px";
            }
        }

        // ✅ Render list item
        $html .= "<li class='division'>";

        // Arrow icon
        if ($hasChildren) {
            $html .= "<span class='tree-toggle'>▼</span>";
        } else {
            $html .= "<span class='tree-toggle empty'></span>";
        }

        // ✅ Apply color + font size
        $html .= "<span class='node-text' style='color: {$color}; font-size: {$fontSize}; font-weight:600;'>{$node['name']}</span>";

        // Render children
        if ($hasChildren) {
            $html .= buildTreeHTML($node['children']);
        }

        $html .= "</li>";
    }
    $html .= "</ul>";
    return $html;
}

?>
