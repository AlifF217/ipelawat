<?php

namespace App\Controllers;

use App\Models\DivisionModel;
use CodeIgniter\Controller;

class DivisionController extends Controller
{
    // Show the full division hierarchy
    public function index()
    {
        $model = new DivisionModel();
        $divisions = $model->findAll();
        $tree = $this->buildTree($divisions);

        // Load div_hierarchy.php (main hierarchy display)
        return view('div_hierarchy', ['tree' => $tree]);
    }

    // Show form to add a new division
   public function add()
{
    // Load the Division model
    $divisionModel = new \App\Models\DivisionModel();

    // Get all divisions
    $divisions = $divisionModel->findAll();

    // Build the tree structure
    $tree = $this->buildDivisionTree($divisions);

    // Pass $tree to the view
    return view('div_hierarchy_add', [
        'tree' => $tree
    ]);
}

/**
 * Recursive function to build a tree array from flat divisions
 */
private function buildDivisionTree(array $divisions, $parentId = null)
{
    $branch = [];

    foreach ($divisions as $division) {
        if ($division['parent_id'] == $parentId) {
            $children = $this->buildDivisionTree($divisions, $division['id']);
            if ($children) {
                $division['children'] = $children;
            }
            $branch[] = $division;
        }
    }

    return $branch;
}

    // Recursive helper to build nested tree
    private function buildTree(array $elements, $parentId = null)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }
}
