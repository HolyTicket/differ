<?php
namespace App\Services;

use App\Models\Connection;
use App\Services\BaseService;
use App\Models\Deploy;
use App\Models\Change;

use Auth;

/**
 * Class DependencyService: checks the generated changes and alters them when needed
 * @package App\Services
 */
class DependencyService extends BaseService
{
    /**
     * Check the changes and alter some if needed
     * @param $deploy_id
     */
    public static function check($deploy_id) {
        $changes = Change::where('deploy_id', $deploy_id)->get();

        foreach($changes as $change) {
            // When a column is added as AUTO_INCREMENT, the corresponding primary index is always added and disabled in the selection
            if($change->type == 'column_added' && strpos($change->sql, 'AUTO_INCREMENT')) {
                // Disable the corresponding index
                $c = Change::where(['deploy_id' => $deploy_id, 'entity' => 'index', 'type' => 'index_added', 'name' => 'PRIMARY'])->first();
                $c->disable = 1;
                $c->save();
            }
            // When a column is altered as AUTO_INCREMENT, the corresponding primary index is always added and disabled in the selection
            if($change->type == 'attribute_altered' && $change->name == 'auto_increment') {
                $parent = $change->parent()->first();
                $parent_id = $parent->parent_id;
                $c = Change::where(['deploy_id' => $deploy_id, 'entity' => 'index', 'type' => 'index_added', 'name' => 'PRIMARY', 'parent_id' => $parent_id])->first();
                $c->disable = 1;
                $c->save();
            }
        }

    }
}