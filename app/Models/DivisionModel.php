<?php

namespace App\Models;

use CodeIgniter\Model;

class DivisionModel extends Model
{
    protected $table = 'divisions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'parent_id'];

    /**
     * Recursively build a hierarchy tree from flat data.
     *
     * @param array $divisions
     * @param int|null $parentId
     * @return array
     */
    public function buildTree(array $divisions, $parentId = null): array
    {
        $branch = [];

        foreach ($divisions as $division) {
            if ($division['parent_id'] == $parentId) {
                $children = $this->buildTree($divisions, $division['id']);
                if (!empty($children)) {
                    $division['children'] = $children;
                }
                $branch[] = $division;
            }
        }

        return $branch;
    }
}
