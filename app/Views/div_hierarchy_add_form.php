<!DOCTYPE html>
<html lang="en">
<body>
<div id="division-form-wrapper" class="p-4">
<div class="container" style="max-width:600px;">
    <h2 class="mb-4">➕ Tambah Bahagian</h2>

    <form action="<?= base_url('divisions/store') ?>" method="post" id="divisionForm">
        <div id="dropdownContainer">
            <div class="dropdown-wrapper">
                <label>Pilih Bahagian</label>
                <select class="form-select parent-dropdown" name="parent_id">
                    <option value="">— (Level Teratas) —</option>
                    <?php
                    function buildDivisionOptions($tree, $level = 0) {
                        $html = '';
                        foreach ($tree as $node) {
                            $indent = str_repeat('— ', $level);
                            $html .= "<option value='{$node['id']}' data-has-children='" . (isset($node['children']) ? '1' : '0') . "'>{$indent}" . esc($node['name']) . "</option>";
                        }
                        return $html;
                    }

                    echo buildDivisionOptions($tree);
                    ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>Nama Bahagian Baru</label>
            <input type="text" name="name" class="form-control" placeholder="Nama Bahagian" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Bahagian</button>
        <a href="<?= base_url('/divisions') ?>" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</div>

<script>
// Store the full tree structure in JS
const divisionTree = <?= json_encode($tree) ?>;

// Recursive function to find children of a parent ID
function getChildren(tree, parentId) {
    for (let node of tree) {
        if (node.id == parentId) return node.children || [];
        if (node.children) {
            let result = getChildren(node.children, parentId);
            if (result.length) return result;
        }
    }
    return [];
}

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('dropdownContainer');

    container.addEventListener('change', e => {
        if (!e.target.classList.contains('parent-dropdown')) return;

        // Remove any dropdowns after the current one
        let dropdowns = Array.from(container.querySelectorAll('.parent-dropdown'));
        let index = dropdowns.indexOf(e.target);
        for (let i = dropdowns.length - 1; i > index; i--) {
            dropdowns[i].closest('.dropdown-wrapper').remove();
        }

        const selectedId = e.target.value;
        if (!selectedId) return;

        const children = getChildren(divisionTree, selectedId);
        if (children.length === 0) return; // leaf node, stop here

        // Create new dropdown for children
        const wrapper = document.createElement('div');
        wrapper.className = 'dropdown-wrapper';
        const label = document.createElement('label');
        label.textContent = 'Pilih Sub Bahagian';
        const select = document.createElement('select');
        select.className = 'form-select parent-dropdown';
        select.name = 'parent_id';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = '— Pilih —';
        select.appendChild(defaultOption);

        children.forEach(child => {
            const opt = document.createElement('option');
            opt.value = child.id;
            opt.textContent = child.name;
            opt.dataset.hasChildren = child.children ? 1 : 0;
            select.appendChild(opt);
        });

        wrapper.appendChild(label);
        wrapper.appendChild(select);
        container.appendChild(wrapper);
    });
});
</script>
</body>
</html>
