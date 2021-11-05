<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (count($items) === 0) :
    echo '<div class="mdc-typography--body1 list__no_items">No entities found...</div>';
else :
    echo '<div class="mdc-typography--caption list__total">' . count($items) . ' entities</div>';

    $show_hit_data = (!defined('ADJUST_HIT_DATA') || ADJUST_HIT_DATA >= 0) ? true : false;
    $sides = $this->config->item('sides');
?>
    <div class="mdc-layout-grid">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell mdc-layout-grid__cell--span-12 flex--center">
                <div class="mdc-data-table mdc-elevation--z2">
                    <div class="mdc-data-table__table-container">

                        <?php echo $op_menu; ?>

                        <table class="mdc-data-table__table sortable">
                            <thead>
                                <tr class="mdc-data-table__header-row">
                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="entity_id">ID</th>
                                    <th class="mdc-data-table__header-cell" role="columnheader" scope="col" aria-sort="none" data-column-id="name" title="Player name">Name</th>
                                    <th class="mdc-data-table__header-cell" role="columnheader" scope="col" aria-sort="none" data-column-id="group">Group</th>
                                    <th class="mdc-data-table__header-cell" role="columnheader" scope="col" aria-sort="none" data-column-id="role">Role</th>

                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="shots" title="Shots">S</th>
                                    <?php if ($show_hit_data) : ?>
                                        <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="hits" title="Hits">H</th>
                                    <?php endif; ?>
                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="descending" data-column-id="kills" title="Kills">K</th>
                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="ascending" data-column-id="deaths" title="Deaths">D</th>
                                    <?php if ($show_hit_data) : ?>
                                        <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="fhits" title="Friendly fire">FF</th>
                                    <?php endif; ?>
                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="fkills" title="Teamkills">Tk</th>
                                    <th class="mdc-data-table__header-cell mdc-data-table__header-cell--numeric" role="columnheader" scope="col" aria-sort="none" data-column-id="vkills" title="Destroyed assets">DA</th>
                                </tr>
                            </thead>
                            <tbody class="mdc-data-table__content">
                                <?php foreach ($items as $index => $i) :

                                    $role = $i['role'];
                                    $group = $i['group_name'];
                                    if ($i['class'] !== '') {
                                        $role = $i['class'];
                                    } else {
                                        if (strpos($role, '@') !== false) {
                                            $role_group_arr = explode('@', $i['role']);
                                            if (isset($role_group_arr[0]) && $role_group_arr[0] !== '') {
                                                $role = $role_group_arr[0];
                                            }
                                            if (isset($role_group_arr[1]) && $role_group_arr[1] !== '') {
                                                $group = $role_group_arr[1];
                                            }
                                        }
                                    }

                                    $player_name = is_null($i['player_name']) ? '' : $i['player_name'];
                                    $name = html_escape($i['name']);
                                    $name_title = '';
                                    if ($i['player_id']) {
                                        $name = '<a href="' . base_url('player/') . $i['player_id'] . '">' . $name . '</a>';
                                        if ($player_name !== '' && $i['name'] !== $player_name) {
                                            $name_title = ' title="' . html_escape($player_name) . '"';
                                        }
                                    }

                                    $hits = $i['hits'];
                                    if (defined('ADJUST_HIT_DATA') && $i['operation_id'] < ADJUST_HIT_DATA) {
                                        $hits = '';
                                    }

                                    $fhits = $i['fhits'];
                                    if (defined('ADJUST_HIT_DATA') && $i['operation_id'] < ADJUST_HIT_DATA) {
                                        $fhits = '';
                                    }
                                ?>
                                    <tr class="mdc-data-table__row">
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $i['id']; ?></td>
                                        <td class="mdc-data-table__cell cell__title">
                                            <span<?php echo $name_title; ?>><?php echo $name; ?></span>
                                        </td>
                                        <td class="mdc-data-table__cell <?php echo 'side__' . html_escape(strtolower($i['side'])); ?>">
                                            <?php echo $sides[$i['side']]; ?> <?php echo html_escape($group); ?>
                                        </td>
                                        <td class="mdc-data-table__cell"><?php echo html_escape($role); ?></td>

                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $i['shots']; ?></td>
                                        <?php if ($show_hit_data) : ?>
                                            <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $hits; ?></td>
                                        <?php endif; ?>
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $i['kills']; ?></td>
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $i['deaths']; ?></td>
                                        <?php if ($show_hit_data) : ?>
                                            <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $fhits; ?></td>
                                        <?php endif; ?>
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $i['fkills']; ?></td>
                                        <td class="mdc-data-table__cell mdc-data-table__cell--numeric"><?php echo $i['vkills']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>